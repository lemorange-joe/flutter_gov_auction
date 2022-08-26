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
      <div class="admin-block bgPurple">
        <h3>Supportive Administration</h3>
        <a href="manage_keyword_image.php">Manage Keyword Image</a>
      </div>
    </div>
  <?php
  } else {
  ?>
    <form method="post">
      <div style="width: 180px; margin: 150px auto; background-color: #f3f3f3; padding: 18px 30px">
        <div style="font-size: 18px; font-weight: bold; margin-bottom: 10px">Login</div>
        <input name="username" style="display: block; width: 150px; margin-bottom: 5px" placeholder="Username" autofocus />
        <input type="password" name="password" style="display: block; width: 150px; margin-bottom: 5px" placeholder="Password"/>        
        <button type="submit" >Submit</button>
      </div>
    </form>
  <?php
  }
  ?>
</body>
</html>
