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
    <?php
      $importText = $_POST["import_text"];
      
      $adminImport = new AdminImport();
      $adminImport->parseResultData($importText);
    ?>
    <button onclick="ImportData()">Import</button>&nbsp;&nbsp;&nbsp;&nbsp;<a href="import_auction_result.php">Cancel</a>
    <script>
      function ImportData() {
        var i = 0;
        var lotList = [];

        while (document.getElementById("tbLotNum_"+i)) {        
          lotList.push({
            "lot_num": document.getElementById("tbLotNum_"+i).value,
            "price": document.getElementById("tbPrice_"+i).value,
          });

          ++i;
        }

        console.log(document.getElementById("tbAuctionNum").value);
        console.log(lotList);
      }
    </script>
  <?php
  }
  ?>
</body>
</html>