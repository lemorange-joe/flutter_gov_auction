<?php
class AuctionController {
  function list($param) {
    // quick api to return the list of available auctions
    global $conn, $lang;

    $selectSql = "SELECT
                    auction_id, auction_num, start_time, auction_pdf_$lang as 'auction_pdf',
                    result_pdf_$lang as 'result_pdf', remarks_$lang as 'remarks', auction_status, status, last_update 
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
        $result[$i]["remarks"],
        $result[$i]["auction_status"],
        $result[$i]["status"],
        $result[$i]["last_update"],
      );
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  function details($param) {
    // get the auction details and items by joining related tables
    global $conn, $lang;

    $auctionId = !empty($param) && is_array($param) ? intval($param[0]) : 0;
    if ($auctionId == 0) {
      echo "{}";
      return;
    }

    $auctionId = intval($param[0]);
    $auction = $this->getAuction($auctionId);
    if ($auction != null) {
      $auction->itemPdfList = $this->getAuctionPdfList($auctionId);
      $auction->lotList = $this->getAuctionLotList($auctionId);
    }

    echo json_change_key(json_encode($auction, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
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

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
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

  private function getAuctionLotList($auctionId) {
    global $conn, $lang;

    $selectSql = "SELECT
                    L.lot_id, T.code, L.lot_num, 
                    L.gld_file_ref, L.reference, L.department_$lang as 'department', L.contact_$lang as 'contact', L.number_$lang as 'number', 
                    L.location_$lang as 'location', L.remarks_$lang as 'remarks', L.item_condition_$lang as 'item_condition', 
                    L.icon as 'lot_icon', L.photo_url, L.photo_real, L.transaction_currency, L.transaction_price, L.transaction_status, L.status, L.last_update,
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
      $output[] = $curLot;
    }

    return $output;
  }
}
?>