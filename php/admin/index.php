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
  <link rel="stylesheet" href="css/main.css">
  <style>
    .admin-block {
      display: inline-block;
      vertical-align: top;
      border: solid 1px #444;
      width: 250px;
      padding: 8px;
      margin: 0 30px 30px 0;
    }

    .admin-block h3 {
      font-size: 16px;
      color: #444;
      margin: 0 0 20px 0;
    }

    .admin-block a {
      color: #37f;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <?php
  if (isset($_SESSION["admin_user"])) {
  ?>
    <div class="header"> 
      <div></div>
      <div><h2>Admin</h2></div>
      <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
    </div>
    <div class="body">
      <div class="admin-block bgBlue" style="width: 400px">
        <h3>Data Input</h3>
        <a href="auction_list.php">Auction List</a>
        <br /><br />
        <a href="import_auction_list.php">Import Auction List</a>
        <br /><br />
        <a href="import_auction_result.php">Import Auction Result</a>
        <div class="remarks">
          <ol>
            <li>Create "Auction" in <u>Auction List</u></li>
            <li><u>Import Auction List</u> / <u>Auction List</u> click "Import Items" link <br />to import "Auction Lot" and "Auction Item"</li>
            <li>Update "Auction" status in <u>Auction List</u></li>
            <li><u>Import Auction Result</u> to import auction result</li>
          </ol>
        </div>
      </div>
      <div class="admin-block bgGreen">
        <h3>Inspect</h3>
        <a href="view_api.php">View API</a>
      </div>
      <div class="admin-block bgYellow">
        <h3>Notification</h3>
        <a href="push_message.php">Push Message</a>
      </div>
    </div>
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
