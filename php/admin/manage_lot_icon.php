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
  <link rel="stylesheet" href="css/main.css">
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
  </style>
</head>
<body>
  <div class="header bgPurple">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Auction Lot Icon</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div id="divLastUpdate" style="display: none; position: fixed; top: 0; right: 0; color: #080; background-color: #fff; padding: 8px; border: solid 1px #888; border-top-width: 0; border-right-width: 0" onclick="this.style.display='none'"></div>
  <div class="body">
    <div style="height: 40px;line-height: 30px;">
      <span style="text-decoration: underline">Search</span>
      <div style="display: inline-block; margin-left: 10px">
        Auction ID: <input id="tbAuctionId" type="number" style="width: 50px" value="0" min="0"/>
      </div>
      <input id="chkShowAll" type="checkbox" style="margin-left: 10px" />
      <label for="chkShowAll">Show All</label>
      <input id="tbKeyword" type="text" style="width: 120px; margin-left: 10px" onchange="GetData()" placeholder="Input Keyword" />
      <div style="display: inline-block; margin-left: 10px">
        Page: <input id="tbPage" type="number" style="width: 30px" value="1" min="1"/>
      </div>
      <button style="margin-left:30px" onclick="GetData()">Get</button>
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
  <div class="remarks" style="padding-left: 8px">
    *If lot description is empty, go to <a href="batch_update_lot_description.php">Batch Update Lot Descriptions</a> to update the descriptions first.
  </div>
  <button style="position: fixed; right: 20px; bottom: 140px; width:36px; height: 36px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
  <button style="position: fixed; right: 20px; bottom: 100px; width:36px; height: 36px; font-size: 20px" onmouseover="AutoScroll(-12)" onmouseout="StopScroll()">▲</button>
  <button style="position: fixed; right: 20px; bottom: 60px; width:36px; height: 36px; font-size: 20px" onmouseover="AutoScroll(12)" onmouseout="StopScroll()">▼</button>
  <button style="position: fixed; right: 20px; bottom: 20px; width:36px; height: 36px; font-size: 20px" onclick="window.scrollTo(0, document.body.scrollHeight)">⟱</button>
  <script src="js/main.js"></script>
  <script>
    const statusMapping = {
      "A": "Active",
      "P": "Pending",
      "I": "Inactive",
    };
    function GetData() {
      document.getElementById("tblLotIcon").innerHTML = "<tr><td colspan='10' style='text-align: center; height: 80px'>Loading...</td></tr>";

      var auctionId = document.getElementById("tbAuctionId").value;
      var showAll = document.getElementById("chkShowAll").checked;
      var keyword = document.getElementById("tbKeyword").value.trim();
      var page = document.getElementById("tbPage").value;

      var apiUrl = "../en/api/admin-listLotIcon";
      apiUrl += "-" + auctionId;
      apiUrl += "-" + (showAll ? "Y" : "N");
      if (keyword.length > 0) {
        apiUrl += "-" + encodeURIComponent(keyword);
      }
      apiUrl += "-" + page;
      
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
      
      for (var i = 0; i < lotIconList.length; ++i) {
        const lotIcon = lotIconList[i];
        
        var row = tblLotIcon.insertRow();
        var td0 = row.insertCell(0)
        td0.appendChild(document.createTextNode((i+1)));
        td0.classList.add("center");

        var td1 = row.insertCell(1);
        td1.appendChild(document.createTextNode(lotIcon.start_time.substr(0, 10)));
        td1.classList.add("center");

        row.insertCell(2).appendChild(document.createTextNode(lotIcon.lot_num));
        
        var td3 = row.insertCell(3);
        td3.appendChild(document.createTextNode(lotIcon.featured ? "Y": "N"));
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