<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/enum.php");
include_once ("../class/admin_import.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Preview Auction List</title>
  <style>
    textarea {
      white-space: pre;
      overflow-wrap: normal;
      overflow-x: scroll;
    }
  </style>
</head>
<body>
  <a href="index.php" style="float:left;text-decoration:none">< Index</a>
  <div style="text-align:right"><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  <hr />
  <?php
  if (!isset($_POST["auction_num"]) || empty($_POST["auction_num"])) {
  ?>
    Missing data!
    <br /><br />
    <a href="import_auction_result.php">Back</a>
  <?php
  } else {
  ?>
    Auction Num: <input id="tbAuctionNum" value="<?=$_POST["auction_num"]?>">&nbsp;&nbsp;&nbsp;&nbsp;<a href="import_auction_result.php">Cancel</a>
    <hr />
    <div id="divData">
    <?php
      $importText = $_POST["import_text"];
      
      $adminImport = new AdminImport();
      $total = $adminImport->parseResultData($importText);
    ?>
    </div>
    <div style="display: flex; justify-content: space-between; width: 250px; margin-top: 10px">
      <button id="btnAdd" data-row="<?=$total?>" onclick="AddDataRow()">+</button>
      <button id="btnImport" onclick="ImportData()">Import</button>
      <a href="import_auction_result.php">Cancel</a>
    </div>
    <script>
      function AddDataRow() {
        var rowNum = parseInt(document.getElementById("btnAdd").getAttribute("data-row"));

        var newRowHtml = "<div>";
        newRowHtml += "<span style='display:inline-block;width:40px'>" + (rowNum + 1) + ".</span>";
        newRowHtml += "<input id='tbLotNum_" + rowNum + "' type='text' style='width: 100px' />&nbsp;&nbsp;";
        newRowHtml += "<input id='tbPrice_" + rowNum + "' type='text' style='width: 200px' />";
        newRowHtml += "</div>";
        document.getElementById("divData").insertAdjacentHTML("afterend", newRowHtml);

        document.getElementById("btnAdd").setAttribute("data-row", rowNum+1);
      }

      function ImportData() {
        document.getElementById("btnImport").setAttribute("disabled", "disabled");
        setTimeout(function() {
          document.getElementById("btnImport").removeAttribute("disabled");
        }, 5000);

        var i = 0;
        var lotList = [];

        while (document.getElementById("tbLotNum_"+i)) {        
          var lotNum = document.getElementById("tbLotNum_"+i).value.trim();
          var price = document.getElementById("tbPrice_"+i).value.trim();
          if (lotNum != "" && price != "") {
            lotList.push({
              "lot_num": lotNum,
              "price": price,
            });
          }

          ++i;
        }

        var resultData = {
          "auction_num": document.getElementById("tbAuctionNum").value,
          "lots": lotList,
        };
        
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../en/api/admin-importResult");
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
            if (this.status == 200) {
              const jsonData = JSON.parse(this.responseText);

              if (jsonData.status == "success") {
                window.location = "auction_details.php?id=" + jsonData.data.id + "&type=" + jsonData.data.type;
              } else {
                alert("Update failed: " + jsonData.error);  
              }
            } else {
              alert("Error: " + this.responseText);
            }
          }
        };

        xhr.send(JSON.stringify(resultData));
      }
    </script>
  <?php
  }
  ?>
</body>
</html>