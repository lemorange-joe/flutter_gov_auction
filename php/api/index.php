<?php
include_once ('../include/common.php');
include_once ('../include/enum.php');
include_once ('../include/config.php');
include_once ('../include/adodb5/adodb.inc.php');
include_once ('../class/auction.php');

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

$conn = new stdClass();
// ========================================================================
// define controllers

class AdminController {

}

class AuctionController {
  function list($param) {
    // quick api to return the list of available auctions
    // main purpose is to compare version
    global $conn, $lang;

    $selectSql = "SELECT
                    auction_id, auction_num, start_time, auction_pdf_$lang as 'auction_pdf',
                    result_pdf_$lang as 'result_pdf', auction_status, version, status, last_update 
                  FROM Auction
                  WHERE status = ?
                  ORDER BY start_time DESC";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array(Status::Active))->GetRows();
    $rowNum = count($result);

    $output = array();
    for($i = 0; $i < $rowNum; ++$i) {
      $output[] = new Auction(
        intval($result[$i]["auction_id"]),
        $result[$i]["auction_num"],
        $result[$i]["start_time"],
        "",
        $result[$i]["auction_pdf"],
        $result[$i]["result_pdf"],
        $result[$i]["auction_status"],
        $result[$i]["version"],
        $result[$i]["status"],
        $result[$i]["last_update"],
      );
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE), $GLOBALS['auctionJsonFieldMapping']);
  }

  function details($param) {
    // get the auction details and items by joining related tables
    global $conn, $lang;

    $auctionId = !empty($param) && is_array($param) ? intval($param[0]) : 0;
    if ($auctionId == 0) return;

    $auctionId = intval($param[0]);
    $auction = $this->getAuction($auctionId);
    if ($auction != null) {
      $auction->itemPdfList = $this->getAuctionPdfList($auctionId);
      $auction->lotList = $this->getAuctionLotList($auctionId);
    }

    echo json_change_key(json_encode($auction, JSON_UNESCAPED_UNICODE), $GLOBALS['auctionJsonFieldMapping']);
  }

  function search($param) {
    // pre: $auctionId, $keyword, $type
    // use $keyword to search the auction lot and items within the auction id
    global $conn, $lang;

    if (count($param) < 2 || empty($param[1])) {
      echo "[]";
      return;
    }

    list($auctionId, $keyword, $type) = array_pad($param, 3, "");
    $selectSql = "SELECT
                    A.auction_id, A.start_time, A.auction_status, L.lot_id, T.code, L.photo_url, L.photo_real, L.transaction_currency, L.transaction_price, L.transaction_status,
                    I.icon, I.description_$lang as 'description', I.quantity, I.unit_$lang as 'unit'
                  FROM Auction A
                  INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                  INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                  INNER JOIN ItemType T ON L.type_id = T.type_id
                  WHERE L.auction_id = ? AND L.status = ? AND (T.code = ? OR ? = '') AND (I.description_en LIKE ? OR I.description_tc LIKE ? OR I.description_sc LIKE ?)
                  ORDER BY L.seq, I.seq";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId, Status::Active, $type, $type, "%".GetSafeMySqlString($keyword)."%", "%".GetSafeMySqlString($keyword)."%", "%".GetSafeMySqlString($keyword)."%"))->GetRows();
    $rowNum = count($result);

    $output = array();
    for($i = 0; $i < $rowNum; ++$i) {
      $output[] = new AuctionSearch(
        intval($result[$i]["auction_id"]),
        $result[$i]["start_time"],
        $result[$i]["auction_status"],
        intval($result[$i]["lot_id"]),
        $result[$i]["code"],
        $result[$i]["photo_url"],
        $result[$i]["photo_real"],
        $result[$i]["transaction_currency"],
        $result[$i]["transaction_price"],
        $result[$i]["transaction_status"],
        $result[$i]["icon"],
        $result[$i]["description"],
        $result[$i]["quantity"],
        $result[$i]["unit"],
        0
      );
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE), $GLOBALS['auctionJsonFieldMapping']);
  }

  function related($param) {
    // pre: $itemId
    // use $itemId to search related items in other lots or auctions
    global $conn, $lang;

    $itemId = array_shift($param);
    
    $selectSql = "SELECT
                    A.auction_id, A.start_time, A.auction_status, L.lot_id, T.code, L.photo_url, L.photo_real, L.transaction_currency, L.transaction_price, L.transaction_status,
                    I.icon, I.description_$lang as 'description', I.quantity, I.unit_$lang as 'unit'
                  FROM Auction A
                  INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                  INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                  INNER JOIN ItemType T ON L.type_id = T.type_id
                  WHERE I.item_id <> ? AND A.status = ? AND L.status = ? AND EXISTS (
                    SELECT 1 
                    FROM AuctionItem I0
                    INNER JOIN AuctionLot L0 ON I0.lot_id = L0.lot_id
                    WHERE I0.item_id = ? AND L0.lot_id <> L.lot_id AND (I.description_en = I0.description_en OR I.description_tc = I0.description_tc OR I.description_sc = I0.description_sc)
                  )
                  ORDER BY A.start_time DESC, L.seq, I.seq";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($itemId, Status::Active, Status::Active, $itemId))->GetRows();
    $rowNum = count($result);

    $output = array();
    for($i = 0; $i < $rowNum; ++$i) {
      $output[] = new AuctionSearch(
        intval($result[$i]["auction_id"]),
        $result[$i]["start_time"],
        $result[$i]["auction_status"],
        intval($result[$i]["lot_id"]),
        $result[$i]["code"],
        $result[$i]["photo_url"],
        $result[$i]["photo_real"],
        $result[$i]["transaction_currency"],
        $result[$i]["transaction_price"],
        $result[$i]["transaction_status"],
        $result[$i]["icon"],
        $result[$i]["description"],
        $result[$i]["quantity"],
        $result[$i]["unit"],
        0
      );
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE), $GLOBALS['auctionJsonFieldMapping']);
  }

  private function getAuction($auctionId) {
    global $conn, $lang;

    $selectSql = "SELECT
                    A.auction_id, A.auction_num, A.start_time, L.address_$lang as 'address', A.auction_pdf_$lang as 'auction_pdf',
                    A.result_pdf_$lang as 'result_pdf', A.auction_status, version, status, last_update 
                  FROM Auction A
                  INNER JOIN Location L ON A.location_id = L.location_id
                  WHERE auction_id = ?";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId))->GetRows();

    $output = null;
    if (count($result) > 0) {
      $output = new Auction(
        intval($result[0]["auction_id"]),
        $result[0]["auction_num"],
        $result[0]["start_time"],
        $result[0]["address"],
        $result[0]["auction_pdf"],
        $result[0]["result_pdf"],
        $result[0]["auction_status"],
        $result[0]["version"],
        $result[0]["status"],
        $result[0]["last_update"],
      );
    }

    return $output;
  }

  private function getAuctionPdfList($auctionId) {
    global $conn, $lang;

    $selectSql = "SELECT I.code, L.url_$lang as 'url'
                  FROM Auction A
                  INNER JOIN ItemListPdf L ON A.auction_id = L.auction_id
                  INNER JOIN ItemType I ON L.type_id = I.type_id
                  WHERE A.auction_id = ?
                  ORDER BY I.seq";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId))->GetRows();
    $rowNum = count($result);

    $output = array();
    for($i = 0; $i < $rowNum; ++$i) {
      $pdfUrl = new StdClass();
      $pdfUrl->type = $result[$i]["code"];
      $pdfUrl->url = $result[$i]["url"];

      $output[] = $pdfUrl;
    }

    return $output;
  }

  private function getAuctionLotList($auctionId) {
    global $conn, $lang;

    $selectSql = "SELECT
                    L.lot_id, T.code, L.lot_num, L.icon as 'lot_icon', L.photo_url, L.photo_real, L.transaction_currency, L.transaction_price, L.transaction_status, L.status, L.last_update,
                    I.item_id, I.icon as 'item_icon', I.description_$lang as 'description', I.quantity, I.unit_$lang as 'unit'
                  FROM Auction A
                  INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                  INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                  INNER JOIN ItemType T ON L.type_id = T.type_id
                  WHERE A.auction_id = ? AND L.status = ?
                  ORDER BY L.seq, I.seq";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId, Status::Active))->GetRows();
    $rowNum = count($result);

    $output = array();
    $curLotNum = "";
    $curLot = null;
    $curItemList = array();
    for($i = 0; $i < $rowNum; ++$i) {
      if ($curLotNum != $result[$i]["lot_num"]) {
        if ($i > 0) {
          // add existing to the current lot first
          $curLot->itemList = $curItemList;
          $output[] = $curLot;
        }

        // prepare to start next lot
        $curLotNum = $result[$i]["lot_num"];
        $curLot = new AuctionLot(
          $result[$i]["lot_id"],
          $result[$i]["code"],
          $result[$i]["lot_num"],
          $result[$i]["lot_icon"],
          $result[$i]["photo_url"],
          $result[$i]["photo_real"],
          $result[$i]["transaction_currency"],
          $result[$i]["transaction_price"],
          $result[$i]["transaction_status"],
          $result[$i]["status"],
          $result[$i]["last_update"],
          0,
        );
        $curItemList = array();
      }

      $curItemList[] = new AuctionItem(
        $result[$i]["item_id"],
        $result[$i]["item_icon"],
        $result[$i]["description"],
        $result[$i]["quantity"],
        $result[$i]["unit"],
        0,
      );
    }

    // add the last item
    if ($curLotNum != "") {
      $curLot->itemList = $curItemList;
      $output[] = $curLot;
    }

    return $output;
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
$headers = getallheaders();
Debug_var_dump($headers["gauc-id"]);
echo "lang: $lang";
echo "<hr>";

$controller = new stdClass();

if ($strController == "auction") {
  $controller = new AuctionController();
} else if ($strController == "data") {  
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