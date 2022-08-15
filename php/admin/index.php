<?php
  session_start();

  if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $_SESSION["admin_user"] = $username;
  }
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Index</title>
</head>
<body>
  <?php
  if (isset($_SESSION["admin_user"])) {
  ?>
    <div style="text-align:right"><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
    <hr />
    <a href="auction_list.php">Auction List</a>
    <br /><br />
    <a href="input_auction_list.php">Input Auction List</a>
    <br /><br />
    <a href="input_auction_result.php">Input Auction Result</a>
  <?php
  } else {
  ?>
    <form method="post">
      Username: <input name="username" autofocus />
      <br />Password: <input type="password" name="password" />
      <br /><button type="submit">Submit</button>
    </form>
  <?php
  }
  ?>
</body>
</html>
