<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/enum.php");
include_once ('../include/demo_data_'.strtolower(ItemType::ConfiscatedGoods).'.php');
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Input Auction List</title>
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
  <form id="import_form" action="preview_auction_list.php" method="POST">
    <div style="width:1500px;display:flex;justify-content:space-between">
    <div>
      <select name="item_type">
        <option value="">-- Please Select --</option>
        <option selected value="<?=ItemType::ConfiscatedGoods?>">[C]充公物品</option>
        <option value="<?=ItemType::UnclaimedProperties?>">[UP]無人認領物品</option>
        <option value="<?=ItemType::UnserviceableStores?>">[M]廢棄物品及剩餘物品</option>
        <option value="<?=ItemType::SurplusServiceableStores?>">[MS]仍可使用之廢棄物品及剩餘物品</option>
      </select>
      &nbsp;&nbsp;Auction Num: <input name="auction_num" style="width: 100px" value="3/2022" />
    </div>
    <button type="submit" form="import_form" value="Submit">Submit</button>
    </div>
    <textarea name="import_text" style="width: 1500px; height: 600px"><?=$DEMO_AUCTION_PDF?></textarea>  
  </form>
</body>
</html>