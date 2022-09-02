<?php
include_once ("../include/config.php");

class UserController {
  function validateLogin($username, $password) {
    global $conn, $LOGIN_SALT;

    $output = false;
    $hashedPassword = md5("$username-$password-$LOGIN_SALT");

    $selectSql = "SELECT password FROM SystemUser WHERE username = ? AND status = 'A'";
    
    $result = $conn->Execute($selectSql, array($username))->GetRows();

    if (count($result) > 0) {
      $output = strcmp($hashedPassword, $result[0]["password"]) === 0;
    }

    return $output;
  }

  function changePassword() {
    global $conn, $LOGIN_SALT;

    $output = new stdClass();
    $output->status = "fail";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($_SESSION["admin_user"])) {
        throw new Exception("user not logged!");  
      }

      if (!isset($data["old_password"]) || empty($data["old_password"]) ||
          !isset($data["new_password"]) || empty($data["new_password"])) {
        throw new Exception("password missing!");  
      }

      $validPassword = false;
      $username = $_SESSION["admin_user"];
      $oldHashedPassword = md5("$username-".$data["old_password"]."-$LOGIN_SALT");
      $newHashedPassword = md5("$username-".$data["new_password"]."-$LOGIN_SALT");

      $selectSql = "SELECT password FROM SystemUser WHERE username = ? AND status = 'A'";
      $result = $conn->Execute($selectSql, array($username))->GetRows();
      if (count($result) > 0) {
        $validPassword = strcmp($oldHashedPassword, $result[0]["password"]) === 0;
      }

      if ($validPassword) {
        $updateSql = "UPDATE SystemUser SET password = ? WHERE username = ? AND status = 'A'";
        $conn->Execute($updateSql, array($newHashedPassword, $username));

        $output->status = "success";
      } else {
        throw new Exception("wrong old password!");
      }      
    }
    catch (Exception $e) {
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

}
?>