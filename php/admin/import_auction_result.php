<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/enum.php");
include_once ('../include/demo_result_data.php');
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Input Auction Result</title>
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
  <form id="import_form" action="preview_auction_result.php" method="POST">
    <div style="width:500px;display:flex;justify-content:space-between">
    <div>
      Auction Num: <input name="auction_num" style="width: 100px" value="3/2022" />
    </div>
    <button type="submit" form="import_form" value="Submit">Submit</button>
    </div>
    <textarea name="import_text" style="width: 500px; height: 600px"><?=$DEMO_AUCTION_RESULT_PDF?></textarea>  
  </form>
</body>
</html>