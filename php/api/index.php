<?php
include_once ('../include/common.php');
include_once ('../include/config.php');
include_once ('../include/adodb5/adodb.inc.php');

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

echo 'controller: ' . $strController;
echo '<br>method: ' . $strMethod;
echo '<hr />';

$conn = new stdClass();

// ========================================================================
// define classes

class AuctionController {
  function list($param) {
    global $conn;

    $selectSql = "SELECT auction_id, auction_num, start_time FROM Auction";
    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql)->GetRows();

    Debug_var_dump($result);
  }

  function details($param) {
    global $conn;

    $auctionId = !empty($param) && is_array($param) ? intval($param[0]) : 0;
    if ($auctionId == 0) return;

    $selectSql = "SELECT auction_id, location_id, version, status FROM Auction";
    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql)->GetRows();

    echo "auction_id: $auctionId";
    Debug_var_dump($result);
  }
}

class DataController {
  function getAppInfo($param) {
    echo 'getAppInfo: ' . date('Y/m/d H:i:s');
    Debug_var_dump($param);
  }
}

// ========================================================================
// main flow

$controller = new stdClass();

if ($strController == "auction") {
  $controller = new AuctionController();
}
else if ($strController == "data") {  
  $controller = new DataController();
}

if (method_exists($controller, $strMethod)) {
  $conn = ADONewConnection('mysqli');
  $conn->PConnect($GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'] , $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_NAME']);
  $conn->Execute("SET NAMES UTF8");

  $controller->$strMethod($param);

  $conn->close();
}
?>