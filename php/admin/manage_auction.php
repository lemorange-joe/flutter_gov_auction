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
    }

    .form-row {
      display: flex;
    }

    .form-row div:first-child {
      width: 150px;
    }

    #newForm input {
      width: 100px;
    }

    #newForm input.long {
      width: 300px;
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
      <div><input id="tbAuctionNum" /></div>
    </div>
    <div class="form-row">
      <div>Start Time</div>
      <div><input id="tbStartTime" /></div>
    </div>
    <div class="form-row">
      <div>Auction PDF (EN)</div>
      <div><input id="tbAuctionPdfEn" class="long" /></div>
    </div>
    <div class="form-row">
      <div>Auction PDF (TC)</div>
      <div><input id="tbAuctionPdfTc" class="long" /></div>
    </div>
    <div class="form-row">
      <div>Auction PDF (SC)</div>
      <div><input id="tbAuctionPdfSc" class="long" /></div>
    </div>
    <div class="form-row">
      <div>Result PDF (EN)</div>
      <div><input id="tbResultPdfEn" class="long" /></div>
    </div>
    <div class="form-row">
      <div>Result PDF (TC)</div>
      <div><input id="tbResultPdfTc" class="long" /></div>
    </div>
    <div class="form-row">
      <div>Result PDF (SC)</div>
      <div><input id="tbResultPdfSc" class="long" /></div>
    </div>
    <div class="form-row">
      <div>Auction Status</div>
      <div><input id="tbAuctionStatus" /></div>
    </div>
    <div class="form-row">
      <div>Status</div>
      <div><input id="tbStatus" /></div>
    </div>
  </div>
  <button onclick="CreateAuction()">Create</button>
  <script>
    function CreateAuction() {
      console.log("Create auction");
    }

    function UpdateAuction(i) {
      console.log("Auction: " + i);
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
              var row = tblAuction.insertRow();
              row.insertCell(0).appendChild(document.createTextNode(curAuction.id));
              row.insertCell(1).appendChild(document.createTextNode(curAuction.num));
              row.insertCell(2).appendChild(document.createTextNode(curAuction.start_time));
              var td3 = row.insertCell(3);
              td3.appendChild(document.createTextNode(curAuction.auction_pdf_en));
              td3.appendChild(document.createTextNode(curAuction.auction_pdf_tc));
              td3.appendChild(document.createTextNode(curAuction.auction_pdf_sc));
              var td4 = row.insertCell(4);
              td4.appendChild(document.createTextNode(curAuction.result_pdf_en));
              td4.appendChild(document.createTextNode(curAuction.result_pdf_tc));
              td4.appendChild(document.createTextNode(curAuction.result_pdf_sc));
              row.insertCell(5).appendChild(document.createTextNode(curAuction.auction_status));
              row.insertCell(6).appendChild(document.createTextNode(curAuction.status));
              var btnUpdate = document.createElement("button");
              btnUpdate.innerHTML = "Update";
              btnUpdate.onclick = function () {
                UpdateAuction(this.parentNode.parentNode.rowIndex);
              };
              row.insertCell(7).appendChild(btnUpdate);
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