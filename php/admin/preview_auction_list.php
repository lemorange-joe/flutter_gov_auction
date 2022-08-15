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
  if (!isset($_POST["item_type"]) || !isset($_POST["auction_num"]) || empty($_POST["item_type"])|| empty($_POST["auction_num"])) {
  ?>
    Missing data!
    <br /><br />
    <a href="import_auction_list.php">Back</a>
  <?php
  } else {
  ?>
    <div style="width:400px;display:flex;justify-content:space-between">
      <div>
      <span style="display:inline-block;width:100px">Item type:</span><input id="tbItemType" value="<?=$_POST["item_type"]?>" style="width:60px" />
      <br /><span style="display:inline-block;width:100px">Num:</span><input id="tbAuctionNum" value="<?=$_POST["auction_num"]?>" style="width:60px" />
      </div>
      <a href="import_auction_list.php">Cancel</a>
    </div>
    <hr />
    <?php
      $itemType = $_POST["item_type"];
      $importText = $_POST["import_text"];
      
      $adminImport = new AdminImport();
      $adminImport->parseData($itemType, $importText);
    ?>
    <button onclick="ImportData()">Import</button>&nbsp;&nbsp;&nbsp;&nbsp;<a href="import_auction_list.php">Cancel</a>
    <script>
      function ImportData() {
        var i = 0;
        var auctionData = {
          "type": document.getElementById("tbItemType").value,
          "auction_num": document.getElementById("tbAuctionNum").value,
          "lots": [],
        };

        while (document.getElementById("tbLotNum_"+i)) {
          var j = 0;
          var itemList = [];
          while (document.getElementById("tbItem_"+i+"_"+j)) {
            var itemLines = document.getElementById("tbItem_"+i+"_"+j).value.split("\n");
            
            itemList.push({
              "description_en": itemLines.length > 0 ? itemLines[0] : "",
              "description_tc": itemLines.length > 1 ? itemLines[1] : "",
              "quantity": itemLines.length > 2 ? itemLines[2] : "",
              "unit_en": itemLines.length > 3 ? itemLines[3] : "",
              "unit_tc": itemLines.length > 4 ? itemLines[4] : "",
            });

            ++j;
          }
          
          var curLot = {
            "lot_num": document.getElementById("tbLotNum_"+i).value,
            "gld_file_ref": document.getElementById("tbGldRef_"+i).value,
            "ref": document.getElementById("tbRef_"+i).value,
            "dept_en": document.getElementById("tbDeptEn_"+i).value,
            "dept_tc": document.getElementById("tbDeptTc_"+i).value,
            "contact_en": document.getElementById("tbContactEn_"+i).value,
            "contact_tc": document.getElementById("tbContactTc_"+i).value,
            "number_en": document.getElementById("tbNumberEn_"+i).value,
            "number_tc": document.getElementById("tbNumberTc_"+i).value,
            "location_en": document.getElementById("tbLocationEn_"+i).value,
            "location_tc": document.getElementById("tbLocationTc_"+i).value,
            "remarks_en": document.getElementById("tbRemarksEn_"+i).value,
            "remarks_tc": document.getElementById("tbRemarksTc_"+i).value,
            "items": itemList,
          };

          auctionData.lots.push(curLot);
          
          ++i;
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../en/api/admin-importAuction");
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

        xhr.send(JSON.stringify(auctionData));
      }
    </script>
  <?php
  }
  ?>
</body>
</html>