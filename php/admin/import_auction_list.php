<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/enum.php");
// include_once ('../include/demo_data_'.strtolower(ItemType::ConfiscatedGoods).'.php');

$auctionNum = isset($_GET["auction_num"]) ? trim($_GET["auction_num"]) : "";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Input Auction List</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <div class="header bgBlue">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Import Auction List (1/2)</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <form id="import_form" action="preview_auction_list.php" method="POST">
      <div style="width:1500px;display:flex;justify-content:space-between">
        <div>
          Auction Num: <input name="auction_num" style="width: 100px" value="<?=$auctionNum?>"/>
          &nbsp;&nbsp;
          <select name="item_type">
            <option selected value="">-- Please Select --</option>
            <option value="<?=ItemType::ConfiscatedGoods?>"><?=ItemType::ConfiscatedGoods?> - 充公物品</option>
            <option value="<?=ItemType::UnclaimedProperties?>"><?=ItemType::UnclaimedProperties?> - 無人認領物品</option>
            <option value="<?=ItemType::UnserviceableStores?>"><?=ItemType::UnserviceableStores?> - 廢棄物品及剩餘物品</option>
            <option value="<?=ItemType::SurplusServiceableStores?>"><?=ItemType::SurplusServiceableStores?> - 仍可使用之廢棄物品及剩餘物品</option>
          </select>
        </div>
        <button type="submit" form="import_form" value="Submit">Submit</button>
      </div>
      <br />
      <textarea name="import_text" style="width: 1500px; height: 600px"></textarea>  
    </form>
  </div>
  <button style="position: fixed; right: 20px; bottom: 20px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
</body>
</html>