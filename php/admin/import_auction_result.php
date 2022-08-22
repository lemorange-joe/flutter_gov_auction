<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/enum.php");
// include_once ('../include/demo_result_data.php');

$auctionNum = isset($_GET["auction_num"]) ? trim($_GET["auction_num"]) : "";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Input Auction Result</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <div style="float: left"><h2><a href="index.php">« Admin Index</a></h2></div>
  <div style="float:right"><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  <hr style="clear: both"/>
  <form id="import_form" action="preview_auction_result.php" method="POST">
    <div style="width:500px;display:flex;justify-content:space-between">
      <div>
        Auction Num: <input name="auction_num" style="width: 100px" value="<?=$auctionNum?>"/>
      </div>
      <button type="submit" form="import_form" value="Submit">Submit</button>
    </div>
    <br />
    <textarea name="import_text" style="width: 500px; height: 600px"></textarea>  
  </form>
</body>
</html>