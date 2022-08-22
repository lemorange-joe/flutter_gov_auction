<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Push Message</title>
  <link rel="stylesheet" href="css/main.css">
  <style></style>
</head>
<body>
  <div style="float: left"><h2><a href="index.php">« Admin Index</a></h2></div>
  <div style="float:right"><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  <hr style="clear: both"/>
  Push Message
</body>
</html>