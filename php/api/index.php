<?php
session_start();
include_once ('../include/common.php');
include_once ('../include/enum.php');
include_once ('../include/config.php');
include_once ('../include/adodb5/adodb.inc.php');
include_once ('../class/auction.php');
include_once ('../controllers/admin_controller.php');
include_once ('../controllers/auction_controller.php');
include_once ('../controllers/data_controller.php');
include_once ('../controllers/user_controller.php');

if ($ENV == 'dev') {
  ini_set('display_errors', 'on');
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', '0');
  error_reporting(0);
  // error_reporting(E_ALL | E_STRICT);  # ...but do log them
}

$ADODB_CACHE_DIR = $GLOBALS['CACHE_DIR'];

$request = explode('-', $_REQUEST['req']);
$strController = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
$strMethod = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
$param = $request;
$lang = "tc";
if (strtolower($_REQUEST['lang']) == "en") {
  $lang = "en";
} else if (strtolower($_REQUEST['lang']) == "sc") {
  $lang = "sc";
}
$reqStartTime = floor(microtime(true) * 1000);  // timestamp in milliseconds

// $isDeveloper mainly use for previewing unpublished auctions, lots and items
// $isDeveloper use adodb Execute instead of CacheExecute
$isDeveloper = 0; // use int for easy mysql comparison
$headers = getallheaders();
if (isset($headers["gauc-id"])) {
  // $isDeveloper to be tested
  $isDeveloper = $headers["gauc-id"] == $GLOBALS["DEVELOPER_GAUC_ID"] ? 1 : 0;
}
// echo "gaucId: $gaucId, lang: $lang <hr>";

// ========================================================================
// main flow
$controller = new stdClass();

if ($strController == "auction") {
  $controller = new AuctionController();
} else if ($strController == "data") {  
  $controller = new DataController();
} else if ($strController == "admin") {
  $controller = new AdminController();
} else if ($strController == "user") {
  $controller = new UserController();
}

if ($strController == "admin" || $strController == "user") {
  if (!isset($_SESSION["admin_user"])) {
    echo "Login error!";
    exit;
  }

  $conn = new stdClass();
  $conn = ADONewConnection('mysqli');
  $conn->PConnect($GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'] , $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_NAME']);
  $conn->Execute("SET NAMES UTF8");

  $controller->$strMethod($param);

  $conn->close();
} else {
  if (method_exists($controller, $strMethod)) {
    $conn = new stdClass();
    $conn = ADONewConnection('mysqli');
    $conn->PConnect($GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'] , $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_NAME']);
    $conn->Execute("SET NAMES UTF8");
  
    $controller->$strMethod($param);
  
    $conn->close();
  }
}
?>