<?php
require_once ("../class/obfuscation.php");
include_once ("../include/config.php");
include_once ("../include/common.php");

class AuctionController {
  /*** Public API ***/
  function list($param) {
    // quick api to return the list of available auctions
    global $conn, $lang, $isDeveloper;

    $output = new StdClass();
    $output->status = "fail";

    try {
      $selectSql = "SELECT
                      A.auction_id, A.auction_num, A.start_time, A.collection_deadline, A.auction_pdf_$lang as 'auction_pdf',
                      A.result_pdf_$lang as 'result_pdf', A.remarks_$lang as 'remarks', A.auction_status, status, A.last_update,
                      (SELECT COUNT(lot_id) FROM AuctionLot L WHERE L.auction_id = A.auction_id AND L.status = ?) as 'lot_count',
                      (SELECT SUM(transaction_price) FROM AuctionLot L WHERE L.auction_id = A.auction_id AND L.status = ?) as 'transaction_total'
                    FROM Auction A
                    WHERE (A.status = ? OR (1 = ? AND A.status = ?))
                    ORDER BY A.start_time DESC";

      $result = array();
      if ($isDeveloper) {
        $result = $conn->Execute($selectSql, array(Status::Active, Status::Active, Status::Active, $isDeveloper, Status::Pending))->GetRows();
      } else {
        $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array(Status::Active, Status::Active, Status::Active, $isDeveloper, Status::Pending))->GetRows();
      }
      $rowNum = count($result);

      $data = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $data[] = new Auction(
          intval($result[$i]["auction_id"]),
          $result[$i]["auction_num"],
          $result[$i]["start_time"],
          $result[$i]["collection_deadline"],
          "",
          $result[$i]["auction_pdf"],
          $result[$i]["result_pdf"],
          $result[$i]["remarks"],
          Obfuscation::changeAuctionLotCount(intval($result[$i]["lot_count"]), intval($result[$i]["auction_id"])),
          Obfuscation::changeAuctionTransactionTotal(intval($result[$i]["transaction_total"]), intval($result[$i]["auction_id"])),
          $result[$i]["auction_status"],
          $result[$i]["status"],
          $result[$i]["last_update"],
        );
      }

      $output->status = "success";
      if ($GLOBALS["ENCRYPT_API_DATA"]) {
        $secret = GenRandomString($GLOBALS["AES_SECRET_LENGTH"]);
        $strData = json_change_key(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        
        $output->data = Base64Aes256Encrypt($strData, $secret);
        $output->key = $secret;
      } else {
        $output->data = $data;
      }

      if (isset($_GET["debug"])) {
        $this->appendDebugData($output);
      }
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  /*** Public API ***/
  function details($param) {
    // get the auction details and items by joining related tables
    global $conn, $lang;

    $output = new StdClass();
    $output->status = "fail";

    $auctionId = !empty($param) && is_array($param) ? intval($param[0]) : 0;
    if ($auctionId == 0) {
      $output->message = "Invalid id!";
      echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
      return;
    }

    try {
      $auctionId = intval($param[0]);
      $auction = $this->getAuction($auctionId);
      if ($auction != null) {
        $auction->itemPdfList = $this->getAuctionPdfList($auctionId);
        $auction->lotList = $this->getAuctionLotList($auctionId);
      }

      $output->status = "success";
      if ($GLOBALS["ENCRYPT_API_DATA"]) {
        $secret = GenRandomString($GLOBALS["AES_SECRET_LENGTH"]);
        $strData = json_change_key(json_encode($auction, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        
        $output->data = Base64Aes256Encrypt($strData, $secret);
        $output->key = $secret;
      } else {
        $output->data = $auction;
      }

      if (isset($_GET["debug"])) {
        $this->appendDebugData($output);
      }
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  /*** Public API ***/
  function lot($param) {
    global $conn, $lang, $isDeveloper;

    $output = new StdClass();
    $output->status = "fail";

    $lotId = !empty($param) && is_array($param) ? intval($param[0]) : 0;

    try {
      if ($lotId == 0) {
        $output->message = "Invalid id!";
        echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        return;
      }

      // select inspection dates first
      $selectSql = "SELECT I.lot_id, I.inspection_day, I.inspection_start_time, I.inspection_end_time, I.typhoon_start_time, I.typhoon_end_time
                    FROM InspectionDate I
                    LEFT JOIN AuctionLot L ON I.lot_id = L.lot_id
                    WHERE L.lot_id = ? OR I.lot_id = 0
                    ORDER BY I.lot_id DESC, CASE
                      WHEN I.inspection_day = 7 THEN 0
                      ELSE I.inspection_day
                    END, I.inspection_start_time";
      
      $inspectionDateResult = array();
      if ($isDeveloper) {
        $inspectionDateResult = $conn->Execute($selectSql, array($lotId))->GetRows();
      } else {
        $inspectionDateResult = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($lotId))->GetRows();
      }
      $rowNum = count($inspectionDateResult);

      if ($rowNum > 0) {
        $specialInspection = $inspectionDateResult[0]["lot_id"] == $lotId;
      } else {
        $specialInspection = false;
      }

      $inspectionDateList = array();
      for($i = 0; $i < $rowNum; ++$i) {
        if ($inspectionDateResult[$i]["lot_id"] == $lotId || !$specialInspection) {
          $inspectionDateList[] = new InspectionDate(
            $inspectionDateResult[$i]["inspection_day"],
            $inspectionDateResult[$i]["inspection_start_time"],
            $inspectionDateResult[$i]["inspection_end_time"],
            $inspectionDateResult[$i]["typhoon_start_time"],
            $inspectionDateResult[$i]["typhoon_end_time"],
          );
        }
      }

      // select the auction lot and items
      $selectSql = "SELECT
                      L.lot_id, A.start_time, A.auction_status, T.code, L.lot_num, 
                      L.gld_file_ref, L.reference, L.department_$lang as 'department', L.contact_$lang as 'contact', L.number_$lang as 'number', 
                      L.location_$lang as 'location', L.remarks_$lang as 'remarks', L.item_condition_$lang as 'item_condition', L.description_en, L.description_tc, L.description_sc,
                      L.featured, L.icon as 'lot_icon', L.photo_url, L.photo_real, L.photo_author, L.photo_author_url,
                      L.transaction_currency, L.transaction_price, L.transaction_status, L.status, L.last_update,
                      I.item_id, I.icon as 'item_icon', I.description_$lang as 'description', I.quantity, I.unit_$lang as 'unit'
                    FROM Auction A
                    INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                    INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                    INNER JOIN ItemType T ON L.type_id = T.type_id
                    WHERE L.lot_id = ? AND (A.status = ? OR (1 = ? AND A.status = ?)) AND (L.status = ? OR (1 = ? AND L.status = ?))
                    ORDER BY I.seq";

      $result = array();
      if ($isDeveloper) {
        $result = $conn->Execute($selectSql, array(
          $lotId, Status::Active, $isDeveloper, Status::Pending, Status::Active, $isDeveloper, Status::Pending
        ))->GetRows();
      } else {
        $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array(
          $lotId, Status::Active, $isDeveloper, Status::Pending, Status::Active, $isDeveloper, Status::Pending
        ))->GetRows();
      }
      
      if (count($result) == 0) {
        $output->message = "Lot ID not found!";
        echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        return;
      }
                
      $photoUrl = $result[0]["photo_url"];
      if (!empty(trim($photoUrl)) && strpos($photoUrl, "http://") === false && strpos($photoUrl, "https://") === false) {
        $photoUrl = $GLOBALS["AUCTION_IMAGE_ROOT_URL"] . $photoUrl;
      }

      $tranStatus = $result[0]["auction_status"] == AuctionStatus::Finished ? $result[0]["transaction_status"] : TransactionStatus::Pending;

      $auctionLot = new AuctionLot(
        intval($result[0]["lot_id"]),
        $result[0]["start_time"],
        $result[0]["code"],
        $result[0]["lot_num"],
        $result[0]["gld_file_ref"],
        $result[0]["reference"],
        $result[0]["department"],
        $result[0]["contact"],
        $result[0]["number"],
        $result[0]["location"],
        $result[0]["remarks"],
        $result[0]["item_condition"],
        $result[0]["featured"],
        $result[0]["lot_icon"],
        $photoUrl,
        $result[0]["photo_real"],
        $result[0]["photo_author"],
        $result[0]["photo_author_url"],
        $result[0]["description_en"],
        $result[0]["description_tc"],
        $result[0]["description_sc"],
        $result[0]["transaction_currency"],
        $result[0]["transaction_price"],
        $tranStatus,
        $result[0]["status"],
        $result[0]["last_update"]
      );

      $rowNum = count($result);
      $itemList = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $itemList[] = new AuctionItem(
          intval($result[$i]["item_id"]),
          $result[$i]["item_icon"],
          $result[$i]["description"],
          $result[$i]["quantity"],
          $result[$i]["unit"]
        );
      }

      $auctionLot->specialInspection = intval($specialInspection);
      $auctionLot->inspectionDateList = $inspectionDateList;
      $auctionLot->itemList = $itemList;
      
      $output->status = "success";
      if ($GLOBALS["ENCRYPT_API_DATA"]) {
        $secret = GenRandomString($GLOBALS["AES_SECRET_LENGTH"]);
        $strData = json_change_key(json_encode($auctionLot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        
        $output->data = Base64Aes256Encrypt($strData, $secret);
        $output->key = $secret;
      } else {
        $output->data = $data;
      }

      if (isset($_GET["debug"])) {
        $this->appendDebugData($output);
      }
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  /*** Public API ***/
  function search($param) {
    // pre: $auctionId, $keyword, $type
    // use $keyword to search the auction lot and items within the auction id
    global $conn, $lang, $isDeveloper;

    $output = new StdClass();
    $output->status = "fail";

    if (count($param) < 2 || empty($param[1])) {
      $output->message = "Invalid parameters!";
      echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
      return;
    }

    try {
      // TODO(joe): review the SQL after searchGrid is done
      list($auctionId, $keyword, $type) = array_pad($param, 3, "");
      $selectSql = "SELECT
                      A.auction_id, A.start_time, A.auction_status, L.lot_id, T.code, L.featured, L.photo_url, L.photo_real, 
                      L.photo_author, L.photo_author_url, L.transaction_currency, L.transaction_price, L.transaction_status,
                      I.icon, I.description_$lang as 'description', I.quantity, I.unit_$lang as 'unit'
                    FROM Auction A
                    INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                    INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                    INNER JOIN ItemType T ON L.type_id = T.type_id
                    WHERE L.auction_id = ? AND L.status = ? AND (T.code = ? OR ? = '') AND (I.description_en LIKE ? OR I.description_tc LIKE ? OR I.description_sc LIKE ?)
                    ORDER BY L.lot_num, I.seq";

      $result = array();
      if ($isDeveloper) {
        $result = $conn->Execute($selectSql, array($auctionId, Status::Active, $type, $type, "%".GetSafeMySqlString($keyword)."%", "%".GetSafeMySqlString($keyword)."%", "%".GetSafeMySqlString($keyword)."%"))->GetRows();
      } else {
        $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId, Status::Active, $type, $type, "%".GetSafeMySqlString($keyword)."%", "%".GetSafeMySqlString($keyword)."%", "%".GetSafeMySqlString($keyword)."%"))->GetRows();
      }
      $rowNum = count($result);

      $data = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $photoUrl = $result[$i]["photo_url"];
        if (!empty(trim($photoUrl)) && strpos($photoUrl, "http://") === false && strpos($photoUrl, "https://") === false) {
          $photoUrl = $GLOBALS["AUCTION_IMAGE_ROOT_URL"] . $photoUrl;
        }

        $tranStatus = $result[$i]["auction_status"] == AuctionStatus::Finished ? $result[$i]["transaction_status"] : TransactionStatus::Pending;

        $data[] = new AuctionItemSearch(
          intval($result[$i]["auction_id"]),
          $result[$i]["start_time"],
          $result[$i]["auction_status"],
          intval($result[$i]["lot_id"]),
          $result[$i]["code"],
          $result[$i]["featured"],
          $photoUrl,
          $result[$i]["photo_real"],
          $result[$i]["photo_author"],
          $result[$i]["photo_author_url"],
          $result[$i]["transaction_currency"],
          $result[$i]["transaction_price"],
          $tranStatus,
          $result[$i]["icon"],
          $result[$i]["description"],
          $result[$i]["quantity"],
          $result[$i]["unit"]
        );
      }

      $output->status = "success";
      if ($GLOBALS["ENCRYPT_API_DATA"]) {
        $secret = GenRandomString($GLOBALS["AES_SECRET_LENGTH"]);
        $strData = json_change_key(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        
        $output->data = Base64Aes256Encrypt($strData, $secret);
        $output->key = $secret;
      } else {
        $output->data = $data;
      }

      if (isset($_GET["debug"])) {
        $this->appendDebugData($output);
      }
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  /*** Public API ***/
  function grid($param) {
    // pre: $type, $count
    global $conn, $lang, $isDeveloper;

    $output = new StdClass();
    $output->status = "fail";

    if (count($param) < 2) {
      $output->message = "Invalid parameters!";
      echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
      return;
    }

    try {
      include_once ("data_controller.php");
      $categoryKeywordList = DataController::$searchGridCategoryKeywords;

      $tranStatus = "";
      $searchKeywordList = array();
      $searchDescriptionTc = "";
      if (array_key_exists($param[0], $categoryKeywordList)) {
        $searchKeywordList = $categoryKeywordList[$param[0]]["query"];
      }

      if (empty($searchKeywordList)) {
        $tranStatus = TransactionStatus::Sold;
      } else {
        $keywordCount = count($searchKeywordList);

        $searchDescriptionTc = "AND (";
        for ($i = 0; $i < $keywordCount; ++$i) {
          $searchDescriptionTc .= "L.description_tc LIKE '" . $searchKeywordList[$i] . "'" . ($i < $keywordCount - 1 ? " OR " : "");
        }
        $searchDescriptionTc .= ")";
      }

      $count = intval($param[1]);

      $selectSql = "SELECT
                      A.auction_id, A.auction_num, A.start_time, A.auction_status, L.lot_id, L.lot_num, T.code, 
                      L.description_$lang as 'description', L.featured, L.icon, 
                      L.photo_url, L.photo_real, L.photo_author, L.photo_author_url,
                      L.transaction_currency, L.transaction_price, L.transaction_status
                    FROM Auction A
                    INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                    INNER JOIN ItemType T ON L.type_id = T.type_id
                    WHERE A.status = ? AND L.status = ? AND (? = '' OR L.transaction_status = ?) ". $searchDescriptionTc . "
                    ORDER BY A.start_time DESC, T.seq, L.lot_num
                    LIMIT 0, ?";

      $result = array();
      if ($isDeveloper) {
        $result = $conn->Execute($selectSql, array(Status::Active, Status::Active, $tranStatus, $tranStatus, $count))->GetRows();
      } else {
        $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array(Status::Active, Status::Active, $tranStatus, $tranStatus, $count))->GetRows();
      }
      $rowNum = count($result);

      $data = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $photoUrl = $result[$i]["photo_url"];
        if (!empty(trim($photoUrl)) && strpos($photoUrl, "http://") === false && strpos($photoUrl, "https://") === false) {
          $photoUrl = $GLOBALS["AUCTION_IMAGE_ROOT_URL"] . $photoUrl;
        }

        $tranStatus = $result[$i]["auction_status"] == AuctionStatus::Finished ? $result[$i]["transaction_status"] : TransactionStatus::Pending;

        $data[] = new AuctionLotSearch(
          intval($result[$i]["auction_id"]),
          $result[$i]["auction_num"],
          $result[$i]["start_time"],
          $result[$i]["auction_status"],
          intval($result[$i]["lot_id"]),
          $result[$i]["lot_num"],
          $result[$i]["code"],
          $result[$i]["description"],
          $result[$i]["featured"],
          $result[$i]["icon"],
          $photoUrl,
          $result[$i]["photo_real"],
          $result[$i]["photo_author"],
          $result[$i]["photo_author_url"],
          $result[$i]["transaction_currency"],
          $result[$i]["transaction_price"],
          $tranStatus
        );
      }

      $output->status = "success";
      if ($GLOBALS["ENCRYPT_API_DATA"]) {
        $secret = GenRandomString($GLOBALS["AES_SECRET_LENGTH"]);
        $strData = json_change_key(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        
        $output->data = Base64Aes256Encrypt($strData, $secret);
        $output->key = $secret;
      } else {
        $output->data = $data;
      }

      if (isset($_GET["debug"])) {
        $this->appendDebugData($output);
      }
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  /*** Public API ***/
  function relatedLots($param) {
    // pre: $lotId, $page (starting from 1), $pageSize
    // use $logId to search lots in other auctions that have any same auction items
    global $conn, $lang, $isDeveloper;

    $output = new StdClass();
    $output->status = "fail";

    if (count($param) < 3) {
      $output->message = "Invalid parameters!";
      echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
      return;
    }

    try {
      $lotId = intval(array_shift($param));
      $page = intval(array_shift($param));
      $pageSize = intval(array_shift($param));
      $start = ($page - 1) * $pageSize;
      
      $selectSql = "SELECT
                      A.auction_id, A.auction_num, A.start_time, A.auction_status, L.lot_id, L.lot_num, T.code, 
                      L.description_$lang as 'description', L.featured, L.icon, 
                      L.photo_url, L.photo_real, L.photo_author, L.photo_author_url,
                      L.transaction_currency, L.transaction_price, L.transaction_status
                    FROM Auction A
                    INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                    INNER JOIN ItemType T ON L.type_id = T.type_id
                    INNER JOIN
                    (
                      SELECT DISTINCT L1.lot_id
                      FROM AuctionLot L1
                      INNER JOIN AuctionItem I1 ON L1.lot_id = I1.lot_id
                      INNER JOIN AuctionItem I0 ON (I1.search_keyword_$lang = I0.search_keyword_$lang)
                      INNER JOIN AuctionLot L0 ON I0.lot_id = L0.lot_id
                      WHERE L1.lot_id <> ? AND L0.lot_id = ? AND L1.status = ? AND I1.item_id <> I0.item_id
                    ) as T
                    ON T.lot_id = L.lot_id
                    WHERE A.status = ?
                    ORDER BY A.start_time, T.seq, L.lot_num DESC
                    LIMIT ?, ?";

      $result = array();
      if ($isDeveloper) {
        $result = $conn->Execute($selectSql, array($lotId, $lotId, Status::Active, Status::Active, $start, $pageSize))->GetRows();
      } else {
        $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($lotId, $lotId, Status::Active, Status::Active, $start, $pageSize))->GetRows();
      }
      $rowNum = count($result);

      $data = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $photoUrl = $result[$i]["photo_url"];
        if (!empty(trim($photoUrl)) && strpos($photoUrl, "http://") === false && strpos($photoUrl, "https://") === false) {
          $photoUrl = $GLOBALS["AUCTION_IMAGE_ROOT_URL"] . $photoUrl;
        }

        $tranStatus = $result[$i]["auction_status"] == AuctionStatus::Finished ? $result[$i]["transaction_status"] : TransactionStatus::Pending;

        $data[] = new AuctionLotSearch(
          intval($result[$i]["auction_id"]),
          $result[$i]["auction_num"],
          $result[$i]["start_time"],
          $result[$i]["auction_status"],
          intval($result[$i]["lot_id"]),
          $result[$i]["lot_num"],
          $result[$i]["code"],
          $result[$i]["description"],
          $result[$i]["featured"],
          $result[$i]["icon"],
          $photoUrl,
          $result[$i]["photo_real"],
          $result[$i]["photo_author"],
          $result[$i]["photo_author_url"],
          $result[$i]["transaction_currency"],
          $result[$i]["transaction_price"],
          $tranStatus
        );
      }

      $output->status = "success";
      if ($GLOBALS["ENCRYPT_API_DATA"]) {
        $secret = GenRandomString($GLOBALS["AES_SECRET_LENGTH"]);
        $strData = json_change_key(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        
        $output->data = Base64Aes256Encrypt($strData, $secret);
        $output->key = $secret;
      } else {
        $output->data = $data;
      }

      if (isset($_GET["debug"])) {
        $this->appendDebugData($output);
      }
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  /*** Public API ***/
  function relatedItems($param) {
    // pre: $itemId, $page (starting from 1), $pageSize
    // use $itemId to search related items in other lots or auctions
    global $conn, $lang, $isDeveloper;

    $output = new StdClass();
    $output->status = "fail";

    if (count($param) < 2) {
      $output->message = "Invalid parameters!";
      echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
      return;
    }

    try {
      $itemId = intval(array_shift($param));
      $page = intval(array_shift($param));
      $pageSize = intval(array_shift($param));
      $start = ($page - 1) * $pageSize;

      $selectSql = "SELECT
                      A.auction_id, A.start_time, A.auction_status, L.lot_id, T.code, L.featured, L.photo_url, L.photo_real, 
                      L.photo_author, L.photo_author_url, L.transaction_currency, L.transaction_price, L.transaction_status,
                      I.icon, I.description_$lang as 'description', I.quantity, I.unit_$lang as 'unit'
                    FROM Auction A
                    INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                    INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                    INNER JOIN ItemType T ON L.type_id = T.type_id
                    INNER JOIN AuctionItem I0 ON I.description_en = I0.description_en OR I.description_tc = I0.description_tc OR I.description_sc = I0.description_sc
                    INNER JOIN AuctionLot L0 ON I0.lot_id = L0.lot_id
                    WHERE I0.item_id = ? AND I.item_id <> ? AND A.status = ? AND L.status = ?
                    ORDER BY A.start_time DESC, L.lot_num, I.seq
                    LIMIT ?, ?";

      $result = array();
      if ($isDeveloper) {
        $result = $conn->Execute($selectSql, array($itemId, $itemId, Status::Active, Status::Active, $start, $pageSize))->GetRows();
      } else {
        $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($itemId, $itemId, Status::Active, Status::Active, $start, $pageSize))->GetRows();
      }
      $rowNum = count($result);

      $data = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $photoUrl = $result[$i]["photo_url"];
        if (!empty(trim($photoUrl)) && strpos($photoUrl, "http://") === false && strpos($photoUrl, "https://") === false) {
          $photoUrl = $GLOBALS["AUCTION_IMAGE_ROOT_URL"] . $photoUrl;
        }

        $tranStatus = $result[$i]["auction_status"] == AuctionStatus::Finished ? $result[$i]["transaction_status"] : TransactionStatus::Pending;

        $data[] = new AuctionItemSearch(
          intval($result[$i]["auction_id"]),
          $result[$i]["start_time"],
          $result[$i]["auction_status"],
          intval($result[$i]["lot_id"]),
          $result[$i]["code"],
          $result[$i]["featured"],
          $photoUrl,
          $result[$i]["photo_real"],
          $result[$i]["photo_author"],
          $result[$i]["photo_author_url"],
          $result[$i]["transaction_currency"],
          $result[$i]["transaction_price"],
          $tranStatus,
          $result[$i]["icon"],
          $result[$i]["description"],
          $result[$i]["quantity"],
          $result[$i]["unit"]
        );
      }

      $output->status = "success";
      if ($GLOBALS["ENCRYPT_API_DATA"]) {
        $secret = GenRandomString($GLOBALS["AES_SECRET_LENGTH"]);
        $strData = json_change_key(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        
        $output->data = Base64Aes256Encrypt($strData, $secret);
        $output->key = $secret;
      } else {
        $output->data = $data;
      }

      if (isset($_GET["debug"])) {
        $this->appendDebugData($output);
      }
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  private function getAuction($auctionId) {
    global $conn, $lang, $isDeveloper;

    $selectSql = "SELECT
                    A.auction_id, A.auction_num, A.start_time, A.collection_deadline, L.address_$lang as 'address', A.auction_pdf_$lang as 'auction_pdf',
                    A.result_pdf_$lang as 'result_pdf', A.remarks_$lang as 'remarks', A.auction_status, status, last_update,
                    (SELECT COUNT(lot_id) FROM AuctionLot L WHERE L.auction_id = A.auction_id AND L.status = ?) as 'lot_count',
                    (SELECT SUM(transaction_price) FROM AuctionLot L WHERE L.auction_id = A.auction_id AND L.status = ?) as 'transaction_total'
                  FROM Auction A
                  INNER JOIN Location L ON A.location_id = L.location_id
                  WHERE auction_id = ?";

    $result = array();
    if ($isDeveloper) {
      $result = $conn->Execute($selectSql, array(Status::Active, Status::Active, $auctionId))->GetRows();
    } else {
      $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array(Status::Active, Status::Active, $auctionId))->GetRows();
    }

    $output = null;
    if (count($result) > 0) {
      $output = new Auction(
        intval($result[0]["auction_id"]),
        $result[0]["auction_num"],
        $result[0]["start_time"],
        $result[0]["collection_deadline"],
        $result[0]["address"],
        $result[0]["auction_pdf"],
        $result[0]["result_pdf"],
        $result[0]["remarks"],
        Obfuscation::changeAuctionLotCount(intval($result[0]["lot_count"]), intval($result[0]["auction_id"])),
        Obfuscation::changeAuctionTransactionTotal(intval($result[0]["transaction_total"]), intval($result[0]["auction_id"])),
        $result[0]["auction_status"],
        $result[0]["status"],
        $result[0]["last_update"],
      );
    }

    return $output;
  }

  private function getAuctionPdfList($auctionId) {
    global $conn, $lang, $isDeveloper;

    $selectSql = "SELECT I.code, L.url_$lang as 'url'
                  FROM Auction A
                  INNER JOIN ItemListPdf L ON A.auction_id = L.auction_id
                  INNER JOIN ItemType I ON L.type_id = I.type_id
                  WHERE A.auction_id = ?
                  ORDER BY I.seq";

    $result = array();
    if ($isDeveloper) {
      $result = $conn->Execute($selectSql, array($auctionId))->GetRows();
    } else {
      $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId))->GetRows();
    }
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

  private function getInspectionDateList($lotId, $inspectionDateList) {
    $output = array();
    $rowNum = count($inspectionDateList);

    for ($i = 0; $i < $rowNum; ++$i) {
      $curInspectionDate = $inspectionDateList[$i];

      if ($curInspectionDate["lot_id"] == $lotId) {
        $output[] = new InspectionDate(
          $curInspectionDate["inspection_day"],
          $curInspectionDate["inspection_start_time"],
          $curInspectionDate["inspection_end_time"],
          $curInspectionDate["typhoon_start_time"],
          $curInspectionDate["typhoon_end_time"]);
      }
    }

    return $output;
  }

  private function getAuctionLotList($auctionId) {
    global $conn, $lang, $isDeveloper;

    // select all inspection dates of the auction id first
    // then assign back to the lot programatically
    $selectSql = "SELECT T.lot_id, T.inspection_day, T.inspection_start_time, T.inspection_end_time, T.typhoon_start_time, T.typhoon_end_time
                  FROM (
                    SELECT I.lot_id, I.inspection_day, I.inspection_start_time, I.inspection_end_time, I.typhoon_start_time, I.typhoon_end_time
                      FROM InspectionDate I
                      INNER JOIN AuctionLot L ON I.lot_id = L.lot_id
                    WHERE L.auction_id = ?
                      UNION 
                    SELECT I0.lot_id, I0.inspection_day, I0.inspection_start_time, I0.inspection_end_time, I0.typhoon_start_time, I0.typhoon_end_time
                    FROM InspectionDate I0
                    WHERE I0.lot_id = 0
                  ) as T
                  ORDER BY T.lot_id, CASE
                    WHEN T.inspection_day = 7 THEN 0
                    ELSE T.inspection_day
                  END, T.inspection_start_time";
    
    $inspectionDateResult = array();
    if ($isDeveloper) {
      $inspectionDateResult = $conn->Execute($selectSql, array($auctionId))->GetRows();
    } else {
      $inspectionDateResult = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId))->GetRows();
    }
    $defaultInspectionDateList = $this->getInspectionDateList(0, $inspectionDateResult);

    $selectSql = "SELECT
                    L.lot_id, A.start_time, A.auction_status, T.code, L.lot_num, 
                    L.gld_file_ref, L.reference, L.department_$lang as 'department', L.contact_$lang as 'contact', L.number_$lang as 'number', 
                    L.location_$lang as 'location', L.remarks_$lang as 'remarks', L.item_condition_$lang as 'item_condition', L.description_en, L.description_tc, L.description_sc,
                    L.featured, L.icon as 'lot_icon', L.photo_url, L.photo_real, L.photo_author, L.photo_author_url,
                    L.transaction_currency, L.transaction_price, L.transaction_status, L.status, L.last_update,
                    I.item_id, I.icon as 'item_icon', I.description_$lang as 'description', I.quantity, I.unit_$lang as 'unit'
                  FROM AuctionLot L
                  INNER JOIN Auction A on L.auction_id = A.auction_id
                  INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                  INNER JOIN ItemType T ON L.type_id = T.type_id
                  WHERE L.auction_id = ? AND (L.status = ? OR (1 = ? AND L.status = ?))
                  ORDER BY T.seq, L.lot_num, I.seq";

    $result = array();
    if ($isDeveloper) {
      $result = $conn->Execute($selectSql, array($auctionId, Status::Active, $isDeveloper, Status::Pending))->GetRows();
    } else {
      $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId, Status::Active, $isDeveloper, Status::Pending))->GetRows();
    }
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
          $curInspectionDateList = $this->getInspectionDateList($curLot->id, $inspectionDateResult);
          if (count($curInspectionDateList) == 0) {
            $curLot->specialInspection = 0;
            $curLot->inspectionDateList = $defaultInspectionDateList;
          } else {
            $curLot->specialInspection = 1;
            $curLot->inspectionDateList = $curInspectionDateList;
          }
          
          $output[] = $curLot;
        }

        // prepare to start next lot
        $curLotNum = $result[$i]["lot_num"];
        
        $photoUrl = $result[$i]["photo_url"];
        if (!empty(trim($photoUrl)) && strpos($photoUrl, "http://") === false && strpos($photoUrl, "https://") === false) {
          $photoUrl = $GLOBALS["AUCTION_IMAGE_ROOT_URL"] . $photoUrl;
        }

        $tranStatus = $result[$i]["auction_status"] == AuctionStatus::Finished ? $result[$i]["transaction_status"] : TransactionStatus::Pending;

        $curLot = new AuctionLot(
          intval($result[$i]["lot_id"]),
          $result[$i]["start_time"],
          $result[$i]["code"],
          $result[$i]["lot_num"],
          $result[$i]["gld_file_ref"],
          $result[$i]["reference"],
          $result[$i]["department"],
          $result[$i]["contact"],
          $result[$i]["number"],
          $result[$i]["location"],
          $result[$i]["remarks"],
          $result[$i]["item_condition"],
          $result[$i]["featured"],
          $result[$i]["lot_icon"],
          $photoUrl,
          $result[$i]["photo_real"],
          $result[$i]["photo_author"],
          $result[$i]["photo_author_url"],
          $result[$i]["description_en"],
          $result[$i]["description_tc"],
          $result[$i]["description_sc"],
          $result[$i]["transaction_currency"],
          $result[$i]["transaction_price"],
          $tranStatus,
          $result[$i]["status"],
          $result[$i]["last_update"]
        );
        $curItemList = array();
      }

      $curItemList[] = new AuctionItem(
        intval($result[$i]["item_id"]),
        $result[$i]["item_icon"],
        $result[$i]["description"],
        $result[$i]["quantity"],
        $result[$i]["unit"]
      );
    }

    // add the last item
    if ($curLotNum != "") {
      $curLot->itemList = $curItemList;
      $curInspectionDateList = $this->getInspectionDateList($curLot->id, $inspectionDateResult);
      if (count($curInspectionDateList) == 0) {
        $curLot->specialInspection = 0;
        $curLot->inspectionDateList = $defaultInspectionDateList;
      } else {
        $curLot->specialInspection = 1;
        $curLot->inspectionDateList = $curInspectionDateList;
      }
      $output[] = $curLot;
    }

    return $output;
  }

  private function appendDebugData(&$output) {
    global $reqStartTime;

    $reqEndTime = floor(microtime(true) * 1000);
    $output->requestStart = date('Y/m/d H:i:s', floor($reqStartTime/1000));
    $output->elapsed = $reqEndTime - $reqStartTime;
  }
}
?>