<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/enum.php");

$id = $_GET["id"];
$type = isset($_GET["type"]) ? $_GET["type"] : "";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Auction Details</title>
  <style></style>
</head>
<body>
  <a href="index.php" style="float:left;text-decoration:none">< Index</a>
  <div style="text-align:right"><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  <hr />
  <a href="auction_list.php">Auction List</a>&nbsp;&nbsp;
  <select onchange="GetData(<?=$id?>, this.value)">
    <option value="" <?=$type=="" ? "selected" : ""?>>All</option>
    <option value="<?=ItemType::ConfiscatedGoods?>" <?=$type==ItemType::ConfiscatedGoods ? "selected" : ""?>>[<?=ItemType::ConfiscatedGoods?>] 充公物品</option>
    <option value="<?=ItemType::UnclaimedProperties?>" <?=$type==ItemType::UnclaimedProperties ? "selected" : ""?>>[<?=ItemType::UnclaimedProperties?>] 無人認領物品</option>
    <option value="<?=ItemType::UnserviceableStores?>" <?=$type==ItemType::UnserviceableStores ? "selected" : ""?>>[<?=ItemType::UnserviceableStores?>] 廢棄物品及剩餘物品</option>
    <option value="<?=ItemType::SurplusServiceableStores?>" <?=$type==ItemType::SurplusServiceableStores ? "selected" : ""?>>[<?=ItemType::SurplusServiceableStores?>] 仍可使用之廢棄物品及剩餘物品</option>
  </select>

  <script>
    function GetData(auctionId, type)
    {
      var apiUrl = '../en/api/admin-getAuction?id=' + auctionId + "&type=" + type;
      var xhr = new XMLHttpRequest();
      
      xhr.open("GET", apiUrl);
      xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const jsonData = JSON.parse(this.responseText);
          console.log(jsonData);
        }
      }

      xhr.send();
    }

    GetData(<?=$id?>, "<?=$type?>");
  </script>
</body>
</html>