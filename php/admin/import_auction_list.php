<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/config.php");
include_once ("../include/enum.php");
include_once ("../include/appdata.php");
$_APP = AppData::getInstance();

$auctionNum = isset($_GET["auction_num"]) ? trim($_GET["auction_num"]) : "";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Input Auction List</title>
  <link rel="stylesheet" href="css/main.css?v=<?=$ADMIN_VERSION?>">
</head>
<body>
  <div class="header bgBlue">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Import Auction List (1/2)</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <form id="import_form" action="preview_auction_list.php" method="POST">
      <div style="width:1400px;display:flex;justify-content:space-between">
        <div>
          Auction Num: <input name="auction_num" style="width: 100px" value="<?=$auctionNum?>"/>
          &nbsp;&nbsp;
          <select name="item_type">
            <option selected value="">-- Please Select --</option>
            <?php
              foreach ($_APP->auctionItemTypeList as $auctionItemType) {
                echo "<option value='" . $auctionItemType->code . "'>" . $auctionItemType->code . " - " . $auctionItemType->description("tc") . "</option>";
              }
            ?>    
          </select>
        </div>
        <button id="btnSubmit" class="action-button" onclick="TempDisableButton('btnSubmit');document.getElementById('import_form').submit();">Submit</button>
      </div>
      <br />
      <textarea name="import_text" style="width: 1400px; height: 600px"></textarea>  
    </form>
  </div>
  <button style="position: fixed; right: 20px; bottom: 20px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
  <script src="js/main.js?v=<?=$ADMIN_VERSION?>"></script>
</body>
</html>