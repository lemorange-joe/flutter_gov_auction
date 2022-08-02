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

$conn = new stdClass();

// ========================================================================
// define classes

class AuctionController {
  function list($param) {
    // quick api to return the list of available auctions
    // main purpose is to compare version
    global $conn;

    //!!! CHECK STATUS !!!
    $selectSql = "SELECT
                    auction_id, auction_num, start_time, item_list_pdf_en, item_list_pdf_tc, item_list_pdf_sc, 
                    result_pdf_en, result_pdf_tc, result_pdf_sc, auction_status, version, status, last_update 
                  FROM Auction";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql)->GetRows();
    $rowNum = count($result);
    $output = array();
    for($i = 0; $i < $rowNum; ++$i) {
      $output[] = new Auction(
        intval($result[$i]["auction_id"]),
        $result[$i]["auction_num"],
        $result[$i]["start_time"],
        $result[$i]["item_list_pdf_en"],
        $result[$i]["item_list_pdf_tc"],
        $result[$i]["item_list_pdf_sc"],
        $result[$i]["result_pdf_en"],
        $result[$i]["result_pdf_tc"],
        $result[$i]["result_pdf_sc"],
        $result[$i]["auction_status"],
        $result[$i]["version"],
        $result[$i]["status"],
        $result[$i]["last_update"],
      );
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function details($param) {
    // get the auction details and items by joining related tables
    global $conn;

    //!!! CHECK STATUS !!!
    $auctionId = !empty($param) && is_array($param) ? intval($param[0]) : 0;
    if ($auctionId == 0) return;

    $auctionId = intval($param[0]);
    $auction = $this->getAuction($auctionId);
    if ($auction != null) {
      $auction->location = $this->getAuctionLocation($auctionId);
      $auction->itemPdfList = $this->getAuctionPdfList($auctionId);
      $auction->lotList = $this->getAuctionLotList($auctionId);
    }

    echo json_encode($auction, JSON_UNESCAPED_UNICODE);
  }

  private function getAuction($auctionId) {
    global $conn;

    //!!! CHECK STATUS !!!
    $selectSql = "SELECT
                    auction_id, auction_num, start_time, item_list_pdf_en, item_list_pdf_tc, item_list_pdf_sc, 
                    result_pdf_en, result_pdf_tc, result_pdf_sc, auction_status, version, status, last_update 
                  FROM Auction
                  WHERE auction_id = ?";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId))->GetRows();

    $output = null;
    if (count($result) > 0) {
      $output = new Auction(
        intval($result[0]["auction_id"]),
        $result[0]["auction_num"],
        $result[0]["start_time"],
        $result[0]["item_list_pdf_en"],
        $result[0]["item_list_pdf_tc"],
        $result[0]["item_list_pdf_sc"],
        $result[0]["result_pdf_en"],
        $result[0]["result_pdf_tc"],
        $result[0]["result_pdf_sc"],
        $result[0]["auction_status"],
        $result[0]["version"],
        $result[0]["status"],
        $result[0]["last_update"],
      );
    }

    return $output;
  }

  private function getAuctionLocation($auctionId) {
    global $conn;

    $selectSql = "SELECT
                    L.address_en, L.address_tc, L.address_sc
                  FROM Auction A
                  INNER JOIN Location L ON A.location_id = L.location_id
                  WHERE auction_id = ?";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId))->GetRows();

    $output = array(
      "en" => "",
      "tc" => "",
      "sc" => "",
    );

    if (count($result) > 0) {
      $output = array(
        "en" => $result[0]["address_en"],
        "tc" => $result[0]["address_tc"],
        "sc" => $result[0]["address_sc"],
      );
    }

    return $output;
  }

  private function getAuctionPdfList($auctionId) {
    global $conn;

    $selectSql = "SELECT I.code, L.url_en, L.url_tc, L.url_sc
                  FROM Auction A
                  INNER JOIN AuctionListPdf L ON A.auction_id = L.auction_id
                  INNER JOIN ItemType I ON L.type_id = I.type_id
                  WHERE A.auction_id = 1=?
                  ORDER BY I.seq";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId))->GetRows();
    $rowNum = count($result);
    $output = array();

    for($i = 0; $i < $rowNum; ++$i) {
      $pdfUrl = new StdClass();

      $pdfUrl->type = $result[$i]["code"];
      $pdfUrl->url = array(
        "en" => $result[$i]["url_en"],
        "tc" => $result[$i]["url_tc"],
        "sc" => $result[$i]["url_sc"],
      );

      $output[] = $pdfUrl;
    }

    return $output;
  }

  private function getAuctionLotList($auctionId) {
    global $conn;

    $selectSql = "SELECT
                    L.lot_id, T.code, L.lot_num, L.icon as 'lot_icon', L.photo_url, L.photo_real, L.transaction_currency, L.transaction_price, L.transaction_status, L.last_update,
                    I.item_id, I.icon as 'item_icon', I.description_en, I.description_tc, I.description_sc, I.quantity, I.unit_en, I.unit_tc, I.unit_sc
                  FROM Auction A
                  INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                  INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                  INNER JOIN ItemType T ON L.type_id = T.type_id
                  WHERE A.auction_id = ?
                  ORDER BY L.seq, I.seq";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId))->GetRows();
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
          $result[$i]["last_update"],
        );
        $curItemList = array();
      }

      $curItemList[] = new AuctionItem(
        $result[$i]["item_id"],
        $result[$i]["item_icon"],
        $result[$i]["description_en"],
        $result[$i]["description_tc"],
        $result[$i]["description_sc"],
        $result[$i]["quantity"],
        $result[$i]["unit_en"],
        $result[$i]["unit_tc"],
        $result[$i]["unit_sc"],
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