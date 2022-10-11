<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/common.php");
include_once ("../class/appdata.php");
$_APP = AppData::getInstance();

if (isset($_POST["refresh"])) {
  $_APP->refresh();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - App Data</title>
  <link rel="stylesheet" href="css/main.css">
  <style></style>
</head>
<body>
  <div class="header bgGreen">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">App Data</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <?php
    foreach ($_APP->auctionItemTypeList as $auctionItemType) {
      echo "<div><div style='display:inline-block;width:50px'>" . $auctionItemType->code . "</div>" . $auctionItemType->description("en") . " | " . $auctionItemType->description("tc") . " | " . $auctionItemType->description("sc") . "</div>";
    }
    ?>
    <br />
    <form method="POST">
      <button type="submit" name="refresh">Refresh Data</button>
    </form>
  </div>
  <script src="js/main.js"></script>
  <script>
    
  </script>
</body>
</html>