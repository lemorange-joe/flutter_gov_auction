<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Manage Auction</title>
  <style>
    table, th, td {
      border: 1px solid;
      border-collapse: collapse;
    }

    .form-row {
      display: flex;
    }

    .form-row div:first-child {
      width: 150px;
    }

    .form-row .separate {
      margin-top: 8px;
    }

    #newForm input {
      width: 100px;
    }

    #newForm input.long {
      width: 550px;
    }
  </style>
</head>
<body>
  <a href="index.php" style="float:left;text-decoration:none">< Index</a>
  <div style="text-align:right"><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  <hr />
  <table>
    <thead>
      <tr>
        <th style="width: 50px">ID</th>
        <th style="width: 100px">Auction No.</th>
        <th style="width: 100px">Start Time</th>
        <th style="width: 300px">Auction PDF</th>
        <th style="width: 300px">Result PDF</th>
        <th style="width: 150px">Auction Status</th>
        <th style="width: 80px">Status</th>
        <th style="width: 100px"></th>
      </tr>
    </thead>
    <tbody id="tblAuction">
    </tbody>
  </table>
  <hr />
  <span style="text-decoration: underline">New</span>
  <div id="newForm">
    <div class="form-row">
      <div>Auction No.</div>
      <div><input id="tbNewAuctionNum" /></div>
    </div>
    <div class="form-row">
      <div>Start Time</div>
      <div><input id="tbNewStartTime" /></div>
    </div>
    <div class="form-row separate">
      <div>Auction PDF (EN)</div>
      <div><input id="tbNewAuctionPdfEn" class="long" /></div>
    </div>
    <div class="form-row">
      <div>Auction PDF (TC)</div>
      <div><input id="tbNewAuctionPdfTc" class="long" /></div>
    </div>
    <div class="form-row">
      <div>Auction PDF (SC)</div>
      <div><input id="tbNewAuctionPdfSc" class="long" /></div>
    </div>
    <div class="form-row separate">
      <div>Result PDF (EN)</div>
      <div><input id="tbNewResultPdfEn" class="long" /></div>
    </div>
    <div class="form-row">
      <div>Result PDF (TC)</div>
      <div><input id="tbNewResultPdfTc" class="long" /></div>
    </div>
    <div class="form-row">
      <div>Result PDF (SC)</div>
      <div><input id="tbNewResultPdfSc" class="long" /></div>
    </div>
    <div class="form-row separate">
      <div>Auction Status</div>
      <div>
        <select id="ddlNewAuctionStatus">
        <option value="P" selected>Pending</option>
        <option value="C">Confirmed</option>
        <option value="X">Cancelled</option>
        <option value="F">Finished</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div>Status</div>
      <div>
        <select id="ddlNewStatus">
          <option value="A">Active</option>
          <option value="I" selected>Inactive</option>
        </select>
      </div>
    </div>
  </div>
  <button onclick="CreateAuction()">Create</button>
  <script>
    function GetDdl(id, selectedValue, type) {
      var select = document.createElement("select");
      var option;
      var values;

      if (type == "AuctionStatus") {
        values = {
          "P": "Pending",
          "C": "Confirmed",
          "X": "Cancelled",
          "F": "Finished",
        };
      } else if (type == "Status") {
        values = {
          "A": "Active",
          "I": "Inactive",
        };
      }
      
      select.setAttribute("id", id);
      Object.keys(values).forEach(function(k) {
        var option = document.createElement("option");
        option.value = k;
        option.textContent = values[k];
        if (selectedValue == k) {
          option.setAttribute("selected", "selected");
        }
        select.appendChild(option);
      });

      return select;
    }

    function GetTextBox(id, value, type, width) {
      var input = document.createElement("input");
      input.setAttribute("id", id);
      input.setAttribute("type", type);
      input.setAttribute("value", value);
      if (width) {
        input.style.width = width+"px";
      }
      return input;
    }

    function CreateAuction() {
      var auctionData = {
        "auction_num": document.getElementById("tbNewAuctionNum").value,
        "start_time": document.getElementById("tbNewStartTime").value,
        "auction_pdf_en": document.getElementById("tbNewAuctionPdfEn").value,
        "auction_pdf_tc": document.getElementById("tbNewAuctionPdfTc").value,
        "auction_pdf_sc": document.getElementById("tbNewAuctionPdfSc").value,
        "result_pdf_en": document.getElementById("tbNewResultPdfEn").value,
        "result_pdf_tc": document.getElementById("tbNewResultPdfTc").value,
        "result_pdf_sc": document.getElementById("tbNewResultPdfSc").value,
        "auction_status": document.getElementById("ddlNewAuctionStatus").value,
        "status": document.getElementById("ddlNewStatus").value,
      };

      console.log(auctionData);
    }

    function UpdateAuction(i) {
      var tr = document.getElementById("tblAuction").getElementsByTagName("tr")[i];
      var i = tr.getAttribute("data-index");

      if (!i) {
        alert('Index not found!');
        return;
      }

      var auctionData = {
        "id": document.getElementById("tbAuctionId_"+i).value,
        "auction_num": document.getElementById("tbAuctionNum_"+i).value,
        "start_time": document.getElementById("tbAuctionStartTime_"+i).value,
        "auction_pdf_en": document.getElementById("tbAuctionPdfEn_"+i).value,
        "auction_pdf_tc": document.getElementById("tbAuctionPdfTc_"+i).value,
        "auction_pdf_sc": document.getElementById("tbAuctionPdfSc_"+i).value,
        "result_pdf_en": document.getElementById("tbResultPdfEn_"+i).value,
        "result_pdf_tc": document.getElementById("tbResultPdfTc_"+i).value,
        "result_pdf_sc": document.getElementById("tbResultPdfSc_"+i).value,
        "auction_status": document.getElementById("ddlAuctionStatus_"+i).value,
        "status": document.getElementById("ddlStatus_"+i).value,
      };

      console.log(auctionData);
    }

    function GetData() {
      var apiUrl = '/en/api/admin-listAuction';
      var xhr = new XMLHttpRequest();
      
      xhr.open("GET", apiUrl, true);
      xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const jsonData = JSON.parse(this.responseText);
          
          if (Array.isArray(jsonData)) {
            var tblAuction = document.getElementById("tblAuction");
            
            for (var i = 0; i < jsonData.length; ++i) {
              const curAuction = jsonData[i];

              var link = document.createElement('a');
              link.appendChild(document.createTextNode(curAuction.id));
              link.href = "manage_auction.php?id="+curAuction.id;

              var row = tblAuction.insertRow();
              row.setAttribute("data-index", i);
              var td0 = row.insertCell(0)
              td0.appendChild(link);
              td0.appendChild(GetTextBox("tbAuctionId_"+i, curAuction.id, "hidden"));
              row.insertCell(1).appendChild(GetTextBox("tbAuctionNum_"+i, curAuction.num, "text", 60));
              row.insertCell(2).appendChild(GetTextBox("tbAuctionStartTime_"+i, curAuction.start_time, "text", 130));              
              var td3 = row.insertCell(3);
              td3.appendChild(GetTextBox("tbAuctionPdfEn_"+i, curAuction.auction_pdf_en, "text", 550));
              td3.appendChild(GetTextBox("tbAuctionPdfTc_"+i, curAuction.auction_pdf_tc, "text", 550));
              td3.appendChild(GetTextBox("tbAuctionPdfSc_"+i, curAuction.auction_pdf_sc, "text", 550));
              var td4 = row.insertCell(4);
              td4.appendChild(GetTextBox("tbResultPdfEn_"+i, curAuction.result_pdf_en, "text", 550));
              td4.appendChild(GetTextBox("tbResultPdfTc_"+i, curAuction.result_pdf_tc, "text", 550));
              td4.appendChild(GetTextBox("tbResultPdfSc_"+i, curAuction.result_pdf_sc, "text", 550));
              row.insertCell(5).appendChild(GetDdl("ddlAuctionStatus_"+i, curAuction.auction_status, "AuctionStatus"));
              row.insertCell(6).appendChild(GetDdl("ddlStatus_"+i, curAuction.status, "Status"));
              var btnUpdate = document.createElement("button");
              btnUpdate.innerHTML = "Update";
              btnUpdate.onclick = function () {
                UpdateAuction(this.parentNode.parentNode.rowIndex - 1);
              };
              row.insertCell(7).appendChild(btnUpdate);

              var row2 = tblAuction.insertRow();
              var cell = row2.insertCell(0);
              cell.setAttribute("colspan", 8);
              cell.setAttribute("style", "height: 30px;vertical-align: top");
              cell.appendChild(document.createTextNode("Last Update: " + curAuction.last_update));
            }
          }
        }
      }
      
      xhr.send();
    }

    GetData();
  </script>
</body>
</html>