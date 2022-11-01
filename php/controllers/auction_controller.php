<?php
class AuctionController {
  function list($param) {
    // quick api to return the list of available auctions
    global $conn, $lang, $isDeveloper;

    $output = new StdClass();
    $output->status = "fail";

    try {
      $selectSql = "SELECT
                      auction_id, auction_num, start_time, auction_pdf_$lang as 'auction_pdf',
                      result_pdf_$lang as 'result_pdf', remarks_$lang as 'remarks', auction_status, status, last_update 
                    FROM Auction
                    WHERE (status = ? OR (1 = ? AND status = ?))
                    ORDER BY start_time DESC";

      $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array(Status::Active, $isDeveloper, Status::Pending))->GetRows();
      $rowNum = count($result);

      $data = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $data[] = new Auction(
          intval($result[$i]["auction_id"]),
          $result[$i]["auction_num"],
          $result[$i]["start_time"],
          "",
          $result[$i]["auction_pdf"],
          $result[$i]["result_pdf"],
          $result[$i]["remarks"],
          $result[$i]["auction_status"],
          $result[$i]["status"],
          $result[$i]["last_update"],
        );
      }

      $output->status = "success";
      $output->data = $data;
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

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
      $output->data = $auction;
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  function search($param) {
    // pre: $auctionId, $keyword, $type
    // use $keyword to search the auction lot and items within the auction id
    global $conn, $lang;

    $output = new StdClass();
    $output->status = "fail";

    if (count($param) < 2 || empty($param[1])) {
      $output->message = "Invalid parameters!";
      echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
      return;
    }

    try {
      list($auctionId, $keyword, $type) = array_pad($param, 3, "");
      $selectSql = "SELECT
                      A.auction_id, A.start_time, A.auction_status, L.lot_id, T.code, L.featured, L.photo_url, L.photo_real, 
                      L.photo_author_$lang as 'photo_author', L.photo_author_url, L.transaction_currency, L.transaction_price, L.transaction_status,
                      I.icon, I.description_$lang as 'description', I.quantity, I.unit_$lang as 'unit'
                    FROM Auction A
                    INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                    INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                    INNER JOIN ItemType T ON L.type_id = T.type_id
                    WHERE L.auction_id = ? AND L.status = ? AND (T.code = ? OR ? = '') AND (I.description_en LIKE ? OR I.description_tc LIKE ? OR I.description_sc LIKE ?)
                    ORDER BY L.lot_num, I.seq";

      $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId, Status::Active, $type, $type, "%".GetSafeMySqlString($keyword)."%", "%".GetSafeMySqlString($keyword)."%", "%".GetSafeMySqlString($keyword)."%"))->GetRows();
      $rowNum = count($result);

      $data = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $photoUrl = $result[$i]["photo_url"];
        if (!empty(trim($photoUrl)) && strpos($photoUrl, "http://") === false && strpos($photoUrl, "https://") === false) {
          $photoUrl = $GLOBALS["AUCTION_IMAGE_ROOT_URL"] . $photoUrl;
        }

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
          $result[$i]["transaction_status"],
          $result[$i]["icon"],
          $result[$i]["description"],
          $result[$i]["quantity"],
          $result[$i]["unit"],
          0
        );
      }

      $output->status = "success";
      $output->data = $data;
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  function relatedLots($param) {
    // pre: $lotId, $page (starting from 1)
    // use $logId to search lots in other auctions that have any same auction items
    global $conn, $lang;

    $output = new StdClass();
    $output->status = "fail";

    if (count($param) < 2) {
      $output->message = "Invalid parameters!";
      echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
      return;
    }

    try {
      $lotId = intval(array_shift($param));
      $page = intval(array_shift($param));
      $pageSize = $GLOBALS["RELATED_RECORD_PAGE_SIZE"];
      $start = ($page - 1) * $pageSize;
      
      $selectSql = "SELECT
                      A.auction_id, A.start_time, A.auction_status, L.lot_id, T.code, L.lot_num, L.description_$lang as 'lot_description', 
                      L.featured, L.icon, L.photo_url, L.photo_real, L.photo_author_$lang as 'photo_author', L.photo_author_url, 
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

      $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($lotId, $lotId, Status::Active, Status::Active, $start, $pageSize))->GetRows();
      $rowNum = count($result);

      $data = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $photoUrl = $result[$i]["photo_url"];
        if (!empty(trim($photoUrl)) && strpos($photoUrl, "http://") === false && strpos($photoUrl, "https://") === false) {
          $photoUrl = $GLOBALS["AUCTION_IMAGE_ROOT_URL"] . $photoUrl;
        }

        $data[] = new AuctionLotSearch(
          intval($result[$i]["auction_id"]),
          $result[$i]["start_time"],
          $result[$i]["auction_status"],
          intval($result[$i]["lot_id"]),
          $result[$i]["code"],
          $result[$i]["lot_num"],
          $result[$i]["lot_description"],
          $result[$i]["featured"],
          $result[$i]["icon"],
          $photoUrl,
          $result[$i]["photo_real"],
          $result[$i]["photo_author"],
          $result[$i]["photo_author_url"],
          $result[$i]["transaction_currency"],
          $result[$i]["transaction_price"],
          $result[$i]["transaction_status"],
          0
        );
      }

      $output->status = "success";
      $output->data = $data;
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  function relatedItems($param) {
    // pre: $itemId, $page (starting from 1)
    // use $itemId to search related items in other lots or auctions
    global $conn, $lang;

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
      $pageSize = $GLOBALS["RELATED_RECORD_PAGE_SIZE"];
      $start = ($page - 1) * $pageSize;

      $selectSql = "SELECT
                      A.auction_id, A.start_time, A.auction_status, L.lot_id, T.code, L.featured, L.photo_url, L.photo_real, 
                      L.photo_author_$lang as 'photo_author', L.photo_author_url, L.transaction_currency, L.transaction_price, L.transaction_status,
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

      $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($itemId, $itemId, Status::Active, Status::Active, $start, $pageSize))->GetRows();
      $rowNum = count($result);

      $data = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $photoUrl = $result[$i]["photo_url"];
        if (!empty(trim($photoUrl)) && strpos($photoUrl, "http://") === false && strpos($photoUrl, "https://") === false) {
          $photoUrl = $GLOBALS["AUCTION_IMAGE_ROOT_URL"] . $photoUrl;
        }

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
          $result[$i]["transaction_status"],
          $result[$i]["icon"],
          $result[$i]["description"],
          $result[$i]["quantity"],
          $result[$i]["unit"],
          0
        );
      }

      $output->status = "success";
      $output->data = $data;
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  private function getAuction($auctionId) {
    global $conn, $lang;

    $selectSql = "SELECT
                    A.auction_id, A.auction_num, A.start_time, L.address_$lang as 'address', A.auction_pdf_$lang as 'auction_pdf',
                    A.result_pdf_$lang as 'result_pdf', A.remarks_$lang as 'remarks', A.auction_status, status, last_update 
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
        $result[0]["remarks"],
        $result[0]["auction_status"],
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

  private function getInspectionDateList($lotId, $inspectionDateList) {
    $output = array();
    $rowNum = count($inspectionDateList);

    for ($i = 0; $i < $rowNum; ++$i) {
      $curInspectionDate = $inspectionDateList[$i];

      if ($curInspectionDate["lot_id"] == $lotId) {
        $output[] = new InspectionDate($curInspectionDate["inspection_day"], $curInspectionDate["inspection_start_time"], $curInspectionDate["inspection_end_time"]);
      }
    }

    return $output;
  }

  private function getAuctionLotList($auctionId) {
    global $conn, $lang, $isDeveloper;

    // select all inspection dates of the auction id first
    // then assign back to the lot programatically
    $selectSql = "SELECT I.lot_id, I.inspection_day, I.inspection_start_time, I.inspection_end_time
                  FROM InspectionDate I
                  INNER JOIN AuctionLot L ON I.lot_id = L.lot_id
                  WHERE L.auction_id = ?
                  ORDER BY L.lot_id, CASE
                    WHEN I.inspection_day = 7 THEN 0
                    ELSE I.inspection_day
                  END";
    $inspectionDateResult = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId))->GetRows();

    $selectSql = "SELECT
                    L.lot_id, T.code, L.lot_num, 
                    L.gld_file_ref, L.reference, L.department_$lang as 'department', L.contact_$lang as 'contact', L.number_$lang as 'number', 
                    L.location_$lang as 'location', L.remarks_$lang as 'remarks', L.item_condition_$lang as 'item_condition', L.description_en, L.description_tc, L.description_sc,
                    L.featured, L.icon as 'lot_icon', L.photo_url, L.photo_real, L.photo_author_$lang as 'photo_author', L.photo_author_url,
                    L.transaction_currency, L.transaction_price, L.transaction_status, L.status, L.last_update,
                    I.item_id, I.icon as 'item_icon', I.description_$lang as 'description', I.quantity, I.unit_$lang as 'unit'
                  FROM Auction A
                  INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                  INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                  INNER JOIN ItemType T ON L.type_id = T.type_id
                  WHERE A.auction_id = ? AND (L.status = ? OR (1 = ? OR L.status = ?))
                  ORDER BY L.lot_num, I.seq";

    $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array($auctionId, Status::Active, $isDeveloper, Status::Pending))->GetRows();
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
          $curLot->inspectionDateList = $this->getInspectionDateList($curLot->id, $inspectionDateResult);
          $output[] = $curLot;
        }

        // prepare to start next lot
        $curLotNum = $result[$i]["lot_num"];
        
        $photoUrl = $result[$i]["photo_url"];
        if (!empty(trim($photoUrl)) && strpos($photoUrl, "http://") === false && strpos($photoUrl, "https://") === false) {
          $photoUrl = $GLOBALS["AUCTION_IMAGE_ROOT_URL"] . $photoUrl;
        }

        $curLot = new AuctionLot(
          intval($result[$i]["lot_id"]),
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
          $result[$i]["transaction_status"],
          $result[$i]["status"],
          $result[$i]["last_update"],
          0,
        );
        $curItemList = array();
      }

      $curItemList[] = new AuctionItem(
        intval($result[$i]["item_id"]),
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
      $curLot->inspectionDateList = $this->getInspectionDateList($curLot->id, $inspectionDateResult);
      $output[] = $curLot;
    }

    return $output;
  }
}
?>