<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/config.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Auction Lot Icon</title>
  <link rel="stylesheet" href="css/main.css?v=<?=$ADMIN_VERSION?>">
  <style>
    table, th, td {
      border: 1px solid;
      border-collapse: collapse;
    }

    #tblLotIcon tr:hover {
      background-color: #ddd;
    }

    #tblLotIcon td {
      padding: 3px 5px;
    }

    #tblLotIcon td.center {
      text-align: center;
    }
    
    a.pager-link {
      color: #17d;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="header bgPurple">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Auction Lot Icon</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div id="divLastUpdate" style="display: none; position: fixed; top: 50px; right: 0; color: #080; background-color: #fff; padding: 8px; border: solid 1px #888; border-right-width: 0" onclick="this.style.display='none'"></div>
  <div class="body">
    <div style="height: 40px;line-height: 30px;">
      <span style="text-decoration: underline">Search</span>
      <div style="display: inline-block; margin-left: 10px">
        Auction ID: <input id="tbAuctionId" type="number" style="width: 50px" value="0" min="0"/>
      </div>
      <input id="chkShowAll" type="checkbox" style="margin-left: 10px" />
      <label for="chkShowAll">Show All)</label>
      <input id="chkIncludeFeatured" type="checkbox" style="margin-left: 10px" />
      <label for="chkIncludeFeatured">Include Featured</label>
      <div style="display: inline-block; margin-left: 10px">
        Page: <input id="tbPage" type="number" style="width: 30px" value="1" min="1"/>
      </div>
      <div style="display: inline-block; margin-left: 10px; background-color: #e6e6e6; padding: 0 10px">
        <input id="tbKeyword" type="text" style="width: 120px" onchange="GetData()" placeholder="Input Keyword" />
        <input id="chkMatchFirstItem" type="checkbox" style="margin-left: 10px" />
        <label for="chkMatchFirstItem">Match 1<sup>st</sup> Item only</label>
      </div>
      <button style="margin-left:20px" onclick="GetData()">Get</button>
    </div>
    <table>
      <thead>
        <tr>
          <th style="width: 30px">#</th>
          <th style="width: 100px">Auction Date</th>
          <th style="width: 80px">Lot Num</th>
          <th style="width: 30px">★</th>
          <th style="width: 220px">Icon</th>
          <th style="width: 300px">Description EN</th>
          <th style="width: 300px">Description TC</th>
          <th style="width: 60px">Auction Status</th>
          <th style="width: 60px">Status</th>
          <th style="width: 120px"></th>
        </tr>
      </thead>
      <tbody id="tblLotIcon"></tbody>
    </table>
  </div>
  <div style="padding-left: 10px; height: 25px">
    <a href="#" class="pager-link" onclick="GoPage(-1);return false">&lt; Prev</a>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <a href="#" class="pager-link" onclick="GoPage(1);return false">Next &gt;</a>
  </div>
  <div class="remarks" style="padding-left: 8px">
    *If lot description is empty, go to <a href="batch_update_lot_description.php">Batch Update Lot Descriptions</a> to update the descriptions first.
  </div>
  <button style="position: fixed; right: 20px; bottom: 140px; width:36px; height: 36px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
  <button style="position: fixed; right: 20px; bottom: 100px; width:36px; height: 36px; font-size: 20px" onclick="JumpScroll(-100)" onmouseover="AutoScroll(-12)" onmouseout="StopScroll()">▲</button>
  <button style="position: fixed; right: 20px; bottom: 60px; width:36px; height: 36px; font-size: 20px" onclick="JumpScroll(100)" onmouseover="AutoScroll(12)" onmouseout="StopScroll()">▼</button>
  <button style="position: fixed; right: 20px; bottom: 20px; width:36px; height: 36px; font-size: 20px" onclick="window.scrollTo(0, document.body.scrollHeight)">⟱</button>
  <script src="js/main.js?v=<?=$ADMIN_VERSION?>"></script>
  <script>
    const pageSize = 50;
    const statusMapping = {
      "A": "Active",
      "P": "Pending",
      "I": "Inactive",
    };

    function GoPage(d) {
      var page = parseInt(document.getElementById("tbPage").value, 10);

      if (page <= 1 && d < 0) return;

      document.getElementById("tbPage").value = page + d;
      GetData();
      window.scrollTo(0, 0);
    }

    function GetData() {
      document.getElementById("tblLotIcon").innerHTML = "<tr><td colspan='10' style='text-align: center; height: 80px'>Loading...</td></tr>";

      var auctionId = document.getElementById("tbAuctionId").value;
      var showAll = document.getElementById("chkShowAll").checked;
      var includeFeatured = document.getElementById("chkIncludeFeatured").checked;
      var matchFirstItem = document.getElementById("chkMatchFirstItem").checked;
      var keyword = document.getElementById("tbKeyword").value.trim();
      var page = document.getElementById("tbPage").value;

      var apiUrl = "../en/api/admin-listLotIcon";
      apiUrl += "-" + auctionId;
      apiUrl += "-" + (showAll ? "Y" : "N");
      apiUrl += "-" + (includeFeatured ? "Y" : "N");
      apiUrl += "-" + page;
      if (keyword.length > 0) {
        apiUrl += "-" + encodeURIComponent(keyword);
        apiUrl += "-" + (matchFirstItem ? "Y" : "N");
      }
      
      var xhr = new XMLHttpRequest();
      
      xhr.open("GET", apiUrl);
      xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          var jsonData = JSON.parse(this.responseText);
          var lotIconList = jsonData["lot_list"];
          BuildTable(lotIconList);

          var total = jsonData["total"];
          document.getElementById("divLastUpdate").style.display = "block";
          document.getElementById("divLastUpdate").innerHTML = "Total: " + total + " rows";
        }
      }
      
      xhr.send();
    }

    function BuildTable(lotIconList) {
      var tblLotIcon = document.getElementById("tblLotIcon");
      tblLotIcon.innerHTML = "";
      
      var itemOffset = (parseInt(document.getElementById("tbPage").value, 10) - 1) * pageSize;
      for (var i = 0; i < lotIconList.length; ++i) {
        const lotIcon = lotIconList[i];
        
        var row = tblLotIcon.insertRow();
        var td0 = row.insertCell(0)
        td0.appendChild(document.createTextNode((itemOffset+i+1)));
        td0.classList.add("center");

        var td1 = row.insertCell(1);
        td1.appendChild(document.createTextNode(lotIcon.start_time.substr(0, 10)));
        td1.classList.add("center");

        row.insertCell(2).appendChild(document.createTextNode(lotIcon.lot_num));
        
        var td3 = row.insertCell(3);
        td3.appendChild(document.createTextNode(lotIcon.featured ? "★": "-"));
        td3.classList.add("center");

        var td4 = row.insertCell(4)
        var tbIcon = document.createElement("input");
        tbIcon.setAttribute("id", "tbIcon_" + i);
        tbIcon.setAttribute("value", lotIcon.icon);
        tbIcon.setAttribute("style", "width: 200px");
        td4.appendChild(tbIcon);

        row.insertCell(5).appendChild(document.createTextNode(lotIcon.description_en));
        row.insertCell(6).appendChild(document.createTextNode(lotIcon.description_tc));
        
        var td7 = row.insertCell(7);
        td7.appendChild(document.createTextNode(statusMapping[lotIcon.auction_status]));
        td7.classList.add("center");

        var td8 = row.insertCell(8);
        td8.appendChild(document.createTextNode(statusMapping[lotIcon.status]));
        td8.classList.add("center");

        var td9 = row.insertCell(9)
        var btnUpdate = document.createElement("button");
        btnUpdate.setAttribute("id", "btnUpdate_"+i);
        btnUpdate.setAttribute("data-i", i);
        btnUpdate.setAttribute("data-id", lotIcon.lot_id);
        btnUpdate.setAttribute("data-lot-num", lotIcon.lot_num);
        btnUpdate.setAttribute("style", "margin-left: 10px");
        btnUpdate.innerHTML = "Update";
        btnUpdate.onclick = function () {
          UpdateLotIcon(this);
        };

        var lnkPreview = document.createElement("a");
        lnkPreview.setAttribute("data-i", i);
        lnkPreview.setAttribute("style", "text-decoration: none; cursor: pointer");
        lnkPreview.appendChild(document.createTextNode("🖼"));
        lnkPreview.onclick = function () {
          PreviewLotIcon(this);
          return false;
        };

        td9.appendChild(lnkPreview);
        td9.appendChild(btnUpdate);
        td9.classList.add("center");
      }
    }

    function UpdateLotIcon(btn) {
      var i = btn.getAttribute("data-i");
      var lotId = btn.getAttribute("data-id");
      var icon = document.getElementById("tbIcon_"+i).value.trim();
      var lotNum = btn.getAttribute("data-lot-num");

      TempDisableButton(btn.getAttribute("id"));

      var postData = {
        lot_id: lotId,
        icon: icon,
      };

      var xhr = new XMLHttpRequest();
      xhr.open("POST", "../en/api/admin-updateAuctionLotIcon");
      xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
      xhr.onreadystatechange = function () {
        if (this.readyState == 4) {
          if (this.status == 200) {
            const jsonData = JSON.parse(this.responseText);

            if (jsonData.status == "success") {
              var lastUpdate = jsonData["data"];
              document.getElementById("divLastUpdate").style.display = "block";
              document.getElementById("divLastUpdate").innerHTML = "Update " + lotNum + " success! " + lastUpdate;
            } else {
              alert("Update failed: " + jsonData.error);  
            }
          } else {
            alert("Error: " + this.responseText);
          }
        }
      };

      xhr.send(JSON.stringify(postData));
    }

    function PreviewLotIcon(btn) {
      var i = btn.getAttribute("data-i");
      var strIcon = document.getElementById("tbIcon_"+i).value.trim().split(".");
      if (strIcon.length > 1) {
        var url = "https://fontawesome.com/icons/" + strIcon[1] + "?s=solid&f=classic";
        window.open(url, "previewLotIcon");
      } else {
        alert("Invalid icon: " + document.getElementById("tbIcon_"+i).value);
      } 
    }

    GetData();
  </script>
</body>
</html>