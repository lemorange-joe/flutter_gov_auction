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
  <title>Admin - XXX</title>
  <link rel="stylesheet" href="css/main.css">
  <style></style>
</head>
<body>
  <a href="index.php" style="float:left;text-decoration:none">< Index</a>
  <div style="text-align:right"><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  <hr />
</body>
</html>