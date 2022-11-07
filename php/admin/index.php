<?php
include_once ("../include/config.php");
include_once ('../include/adodb5/adodb.inc.php');
include_once ("../controllers/user_controller.php");
date_default_timezone_set("Asia/Hong_Kong");
session_start();

$loginError = "";
if (isset($_POST["username"]) && isset($_POST["password"])) {
  if (empty($_POST["username"]) || empty($_POST["password"])) {
    $loginError = "Username or password empty!";
  } else {
    $username = strtolower($_POST["username"]);
    $password = $_POST["password"];
    
    
    $conn = new stdClass();
    $conn = ADONewConnection('mysqli');
    $conn->PConnect($GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'] , $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_NAME']);
    $conn->Execute("SET NAMES UTF8");

    $user = new UserController();
    $validate = $user->validateLogin($username, $password);

    $conn->close();

    if ($validate) {
      $_SESSION["admin_user"] = $username;
    } else {
      $loginError = "Wrong username or password!<br />" . Date("Y-m-d H:i:s") ;
    }
  }
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
      <div><?=$_SESSION["admin_user"]?> | <a href="#" onclick="OpenChangePassword()" style="text-decoration: none; color: #666; font-size: 24px; vertical-align: middle">⚙</a> | <a href="logout.php">Logout</a></div>
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
        <br /><br />
        <a href="view_pdf.php">View PDF Files</a>
        <br /><br />
        <a href="app_data.php">View / Refresh App Data</a>
      </div>
      <div class="admin-block bgYellow">
        <h3>App Data & Notification</h3>
        <a href="app_info.php">App Info</a>
        <br /><br />
        <a href="push_message.php">Push Message</a>
        <br /><br />
        <a href="notice_link.php">Notice Links</a>
      </div>
      <div class="admin-block bgPurple">
        <h3>Supportive Administration</h3>
        <a href="manage_keyword_image.php">Manage Keyword Image</a>
        <br /><br />
        <a href="manage_lot_icon.php">Manage Lot Icon</a>
        <br /><br />
        <a href="manage_hot_search.php">Manage Hot Search</a>
        <br /><br />
        <a href="batch_update_lot_description.php">Batch Update Lot Descriptions</a>
      </div>
    </div>
    <div id="divChangePassword" style="display: none;position: absolute;right: 50px; top: 50px; width: 240px; height: 150px; padding: 10px 30px 20px 30px; background-color: #fff; border: solid 2px #444">
      <div style="margin: 5px 0 15px 0; font-size: 16px; font-weight: bold">Change Password</div>
      <input id="tbOldPassword" type="password" style="width: 180px; margin-bottom: 5px" placeholder="Old Password">
      <input id="tbNewPassword" type="password" style="width: 180px; margin-bottom: 5px" placeholder="New Password">
      <input id="tbConfirmPassword" type="password" style="width: 180px; margin-bottom: 20px" placeholder="Confirm Password">
      <button id="btnSubmit" onclick="SubmitPassword()">Submit</button>
      <button style="margin-left: 20px" onclick="document.getElementById('divChangePassword').style.display='none'">Cancel</button>
    </div>
    <hr style="clear: both; margin: 0 5px 20px 5px" />
    <div style="font-size: 11px; padding: 0 10px">
      Before Launch:
      <ul style="margin-top: 3px; padding-left: 20px;">
        <li>select featured</li>
        <li>set photos for featured lots</li>
        <li>update fontawesome icons</li>
      </ul>
    </div>
    <script src="js/main.js"></script>
    <script>
      function OpenChangePassword() {
        document.getElementById("tbOldPassword").value = "";
        document.getElementById("tbNewPassword").value = "";
        document.getElementById("tbConfirmPassword").value = "";
        document.getElementById("divChangePassword").style.display = "block";
      }

      function SubmitPassword() {
        TempDisableButton("btnSubmit");

        var oldPassword = document.getElementById("tbOldPassword").value.trim();
        var newPassword = document.getElementById("tbNewPassword").value.trim();
        var confirmPassword = document.getElementById("tbConfirmPassword").value.trim();

        if (oldPassword == "") {
          alert("Please input old password!");
          return;
        }

        if (newPassword == "") {
          alert("Please input new password!");
          return;
        }

        if (confirmPassword == "") {
          alert("Please input confirm password!");
          return;
        }

        if (newPassword != confirmPassword) {
          alert("New password and confirm password do not match!");
          return;
        }
        
        var passwordData = {
          old_password: oldPassword,
          new_password: newPassword,
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../en/api/user-changePassword");
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
            if (this.status == 200) {
              const jsonData = JSON.parse(this.responseText);

              if (jsonData.status == "success") {
                document.getElementById("divChangePassword").style.display = "none";
                alert("Change password succcess!");
              } else {
                alert("Change password failed: " + jsonData.error);  
              }
            } else {
              alert("Error: " + this.responseText);
            }
          }
        };

        xhr.send(JSON.stringify(passwordData));
      }
    </script>
  <?php
  } else {
  ?>
    <form method="post">
      <div style="width: 250px; margin: 150px auto; background-color: #f3f3f3; padding: 18px 30px">
        <div style="font-size: 18px; font-weight: bold; margin-bottom: 10px">Admin</div>
        <input name="username" style="display: block; width: 150px; margin-bottom: 5px" placeholder="Username" autofocus />
        <input type="password" name="password" style="display: block; width: 150px; margin-bottom: 5px" placeholder="Password"/>        
        <?php
          if (!empty($loginError))
          {
            echo "<div style='font-size: 14px;color: #f33'>$loginError</div>";
          }
        ?>
        <button type="submit" style="margin-top: 15px">Login</button>
      </div>
    </form>
  <?php
  }
  ?>
</body>
</html>
