<?php
include_once ('../include/enum.php');

class AdminController {
  function listAuction() {
    global $conn, $lang;

    $selectSql = "SELECT
                    A.auction_id, A.auction_num, A.start_time, A.auction_pdf_en, A.auction_pdf_tc, A.auction_pdf_sc,
                    A.result_pdf_en, A.result_pdf_tc, A.result_pdf_sc, A.auction_status, A.status, A.last_update,
                    GROUP_CONCAT(C.total ORDER BY C.seq SEPARATOR ', ') as lot_count
                  FROM Auction A
                  LEFT JOIN (
                    SELECT L.auction_id, I.seq, concat(I.code, ': ',  COUNT(*)) as 'total'
                    FROM AuctionLot L
                    INNER JOIN ItemType I ON L.type_id = I.type_id
                    WHERE L.status = 'A'
                    GROUP BY L.auction_id, I.seq, I.code
                  ) as C ON A.auction_id = C.auction_id
                  GROUP BY A.auction_id
                  ORDER BY A.auction_id DESC";

    $result = $conn->Execute($selectSql)->GetRows();
    $rowNum = count($result);

    $output = array();
    for($i = 0; $i < $rowNum; ++$i) {
      $auction = new stdClass();
      $auction->id = $result[$i]["auction_id"];
      $auction->num = $result[$i]["auction_num"];
      $auction->start_time = $result[$i]["start_time"];
      $auction->auction_pdf_en = $result[$i]["auction_pdf_en"];
      $auction->auction_pdf_tc = $result[$i]["auction_pdf_tc"];
      $auction->auction_pdf_sc = $result[$i]["auction_pdf_sc"];
      $auction->result_pdf_en = $result[$i]["result_pdf_en"];
      $auction->result_pdf_tc = $result[$i]["result_pdf_tc"];
      $auction->result_pdf_sc = $result[$i]["result_pdf_sc"];
      $auction->auction_status = $result[$i]["auction_status"];
      $auction->status = $result[$i]["status"];
      $auction->last_update = $result[$i]["last_update"];
      $auction->lot_count = $result[$i]["lot_count"];

      $output[] = $auction;
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function getAuction() {
    global $conn;

    $auctionId = $_GET["id"];
    $itemType = $_GET["type"];
    $output = new stdClass();

    // ------ 1. get auction ------
    $selectSql = "SELECT
                    A.auction_id, A.auction_num, A.start_time, L.address_en, L.address_tc, L.address_sc,
                    A.auction_pdf_en, A.auction_pdf_tc, A.auction_pdf_sc,
                    A.result_pdf_en, A.result_pdf_tc, A.result_pdf_sc, A.auction_status, status, last_update
                  FROM Auction A
                  INNER JOIN Location L ON A.location_id = L.location_id
                  WHERE auction_id = ?";

    $result = $conn->Execute($selectSql, array($auctionId))->GetRows();

    if (count($result) > 0) {
      $output->auction_id = intval($result[0]["auction_id"]);
      $output->auction_num = $result[0]["auction_num"];

      $output->start_time = $result[0]["start_time"];
      $output->address_en = $result[0]["address_en"];
      $output->address_tc = $result[0]["address_tc"];
      $output->address_sc = $result[0]["address_sc"];
      $output->auction_pdf_en = $result[0]["auction_pdf_en"];
      $output->auction_pdf_tc = $result[0]["auction_pdf_tc"];
      $output->auction_pdf_sc = $result[0]["auction_pdf_sc"];
      $output->result_pdf_en = $result[0]["result_pdf_en"];
      $output->result_pdf_tc = $result[0]["result_pdf_tc"];
      $output->result_pdf_sc = $result[0]["result_pdf_sc"];
      $output->auction_status = $result[0]["auction_status"];
      $output->status = $result[0]["status"];
      $output->last_update = $result[0]["last_update"];
      $output->item_pdf_list = array();
      $output->lot_list = array();
    }

    // ------ 2. get item pdf list ------
    $selectSql = "SELECT I.code, L.url_en, L.url_tc, L.url_sc
                  FROM Auction A
                  INNER JOIN ItemListPdf L ON A.auction_id = L.auction_id
                  INNER JOIN ItemType I ON L.type_id = I.type_id
                  WHERE A.auction_id = ?
                  ORDER BY I.seq";

    $result = $conn->Execute($selectSql, array($auctionId))->GetRows();
    $rowNum = count($result);

    for($i = 0; $i < $rowNum; ++$i) {
      $pdfUrl = new StdClass();
      $pdfUrl->type = $result[$i]["code"];
      $pdfUrl->url_en = $result[$i]["url_en"];
      $pdfUrl->url_tc = $result[$i]["url_tc"];
      $pdfUrl->url_sc = $result[$i]["url_sc"];

      $output->item_pdf_list[] = $pdfUrl;
    }

    // ------ 3. get lot list ------
    $selectSql = "SELECT
                    L.lot_id, T.code, L.lot_num, L.seq,
                    gld_file_ref, reference, department_en, department_tc, department_sc,
                    contact_en, contact_tc, contact_sc, number_en, number_tc, number_sc,
                    location_en, location_tc, location_sc, remarks_en, remarks_tc, remarks_sc,
                    L.icon as 'lot_icon', L.photo_url, L.photo_real,
                    L.transaction_currency, L.transaction_price, L.transaction_status, L.status, L.last_update,
                    I.item_id, I.icon as 'item_icon', I.description_en, I.description_tc, I.description_sc, I.quantity, 
                    I.unit_en, I.unit_tc, I.unit_sc
                  FROM Auction A
                  INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                  INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                  INNER JOIN ItemType T ON L.type_id = T.type_id
                  WHERE A.auction_id = ? AND (T.code = ? OR ? = '')
                  ORDER BY L.seq, I.seq";

    $result = $conn->Execute($selectSql, array($auctionId, $itemType, $itemType))->GetRows();
    $rowNum = count($result);

    $curLotNum = "";
    $curLotOutput = new stdClass();
    $curItemList = array();
    for($i = 0; $i < $rowNum; ++$i) {
      if ($curLotNum != $result[$i]["lot_num"]) {
        if ($i > 0) {
          // add existing to the current lot first
          $curLotOutput->item_list = $curItemList;
          $output->lot_list[] = $curLotOutput;
        }

        // prepare to start next lot
        $curLotNum = $result[$i]["lot_num"];
        $curLotOutput = new stdClass();
        $curLotOutput->lot_id = intval($result[$i]["lot_id"]);
        $curLotOutput->code = $result[$i]["code"];
        $curLotOutput->lot_num = $result[$i]["lot_num"];
        $curLotOutput->seq = $result[$i]["seq"];
        $curLotOutput->gld_file_ref = $result[$i]["gld_file_ref"];
        $curLotOutput->reference = $result[$i]["reference"];

        $curLotOutput->department_en = $result[$i]["department_en"];
        $curLotOutput->department_tc = $result[$i]["department_tc"];
        $curLotOutput->department_sc = $result[$i]["department_sc"];
        $curLotOutput->contact_en = $result[$i]["contact_en"];
        $curLotOutput->contact_tc = $result[$i]["contact_tc"];
        $curLotOutput->contact_sc = $result[$i]["contact_sc"];
        $curLotOutput->number_en = $result[$i]["number_en"];
        $curLotOutput->number_tc = $result[$i]["number_tc"];
        $curLotOutput->number_sc = $result[$i]["number_sc"];
        $curLotOutput->location_en = $result[$i]["location_en"];
        $curLotOutput->location_tc = $result[$i]["location_tc"];
        $curLotOutput->location_sc = $result[$i]["location_sc"];
        $curLotOutput->remarks_en = $result[$i]["remarks_en"];
        $curLotOutput->remarks_tc = $result[$i]["remarks_tc"];
        $curLotOutput->remarks_sc = $result[$i]["remarks_sc"];

        $curLotOutput->lot_icon = $result[$i]["lot_icon"];
        $curLotOutput->photo_url = $result[$i]["photo_url"];
        $curLotOutput->photo_real = $result[$i]["photo_real"];
        $curLotOutput->transaction_currency = $result[$i]["transaction_currency"];
        $curLotOutput->transaction_price = $result[$i]["transaction_price"];
        $curLotOutput->transaction_status = $result[$i]["transaction_status"];
        $curLotOutput->status = $result[$i]["status"];
        $curLotOutput->last_update = $result[$i]["last_update"];

        $curItemList = array();
      }

      $curItem = new stdClass();
      $curItem->item_id = intval($result[$i]["item_id"]);
      $curItem->item_icon = $result[$i]["item_icon"];
      $curItem->description_en = $result[$i]["description_en"];
      $curItem->description_tc = $result[$i]["description_tc"];
      $curItem->description_sc = $result[$i]["description_sc"];
      $curItem->quantity = $result[$i]["quantity"];
      $curItem->unit_en = $result[$i]["unit_en"];
      $curItem->unit_tc = $result[$i]["unit_tc"];
      $curItem->unit_sc = $result[$i]["unit_sc"];

      $curItemList[] = $curItem;
    }

    // add the last item
    if ($curLotNum != "") {
      $curLotOutput->item_list = $curItemList;
      $output->lot_list[] = $curLotOutput;
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function updateAuction() {
    global $conn;

    $output = new stdClass();
    $output->status = "failed";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["id"]) || empty($data["id"]) || !ctype_digit($data["id"])) {
        throw new Exception("ID missing!");  
      }
      if (!isset($data["auction_num"]) || empty($data["auction_num"])) {
        throw new Exception("Auction no. missing!");  
      }

      if (!isset($data["start_time"]) || empty($data["start_time"])) {
        throw new Exception("Start time missing!");  
      }
      $testDateTime = new DateTime($data["start_time"]);  // will auto throw error if date time format is invalid

      $id = intval($data["id"]);
      $auctionNum = trim($data["auction_num"]);
      $startTime = $data["start_time"];
      $auctionPdfEn = trim($data["auction_pdf_en"]);
      $auctionPdfTc = trim($data["auction_pdf_tc"]);
      $auctionPdfSc = trim($data["auction_pdf_sc"]);
      $resultPdfEn = trim($data["result_pdf_en"]);
      $resultPdfTc = trim($data["result_pdf_tc"]);
      $resultPdfSc = trim($data["result_pdf_sc"]);
      $auctionStatus = trim($data["auction_status"]);
      $status = trim($data["status"]);

      $updateSql = "UPDATE Auction SET
                      auction_num = ?,
                      start_time = ?,
                      auction_pdf_en = ?,
                      auction_pdf_tc = ?,
                      auction_pdf_sc = ?,
                      result_pdf_en = ?,
                      result_pdf_tc = ?,
                      result_pdf_sc = ?,
                      auction_status = ?,
                      status = ?,
                      last_update = now()
                    WHERE auction_id = ?";

    $result = $conn->Execute($updateSql, array(
      $auctionNum, $startTime, $auctionPdfEn, $auctionPdfTc, $auctionPdfSc, $resultPdfEn, $resultPdfTc, $resultPdfSc, $auctionStatus, $status, $id
    ));

      $output->status = "success";
    } catch (Exception $e) {
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function createAuction() {
    global $conn;

    $output = new stdClass();
    $output->status = "failed";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["auction_num"]) || empty($data["auction_num"])) {
        throw new Exception("Auction no. missing!");  
      }

      if (!isset($data["start_time"]) || empty($data["start_time"])) {
        throw new Exception("Start time missing!");  
      }
      $testDateTime = new DateTime($data["start_time"]);  // will auto throw error if date time format is invalid

      $auctionNum = trim($data["auction_num"]);
      $startTime = $data["start_time"];
      $auctionPdfEn = trim($data["auction_pdf_en"]);
      $auctionPdfTc = trim($data["auction_pdf_tc"]);
      $auctionPdfSc = trim($data["auction_pdf_sc"]);
      $resultPdfEn = trim($data["result_pdf_en"]);
      $resultPdfTc = trim($data["result_pdf_tc"]);
      $resultPdfSc = trim($data["result_pdf_sc"]);
      $auctionStatus = trim($data["auction_status"]);
      $status = trim($data["status"]);

      $insertSql = "INSERT INTO Auction (
                      auction_num, start_time, location_id, auction_pdf_en, auction_pdf_tc, auction_pdf_sc, 
                      result_pdf_en, result_pdf_tc, result_pdf_sc, auction_status, status, last_update
                    ) VALUES (
                      ?, ?, 1, ?, ?, ?, 
                      ?, ?, ?, ?, ?, now()
                    );";

    $result = $conn->Execute($insertSql, array(
      $auctionNum, $startTime, $auctionPdfEn, $auctionPdfTc, $auctionPdfSc, $resultPdfEn, $resultPdfTc, $resultPdfSc, $auctionStatus, $status
    ));

      $output->status = "success";
    } catch (Exception $e) {
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function importAuction() {
    global $conn;

    $output = new stdClass();
    $output->status = "failed";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["auction_num"]) || empty($data["auction_num"])) {
        throw new Exception("Auction no. missing!");  
      }

      if (!isset($data["type"]) || empty($data["type"])) {
        throw new Exception("Type missing!");  
      }

      if (!isset($data["lots"]) || empty($data["lots"])) {
        throw new Exception("Lot data missing!");  
      }

      $auctionId = 0;
      $typeId = 0;
      $auctionNum = trim($data["auction_num"]);
      $type = trim($data["type"]);
      $lots = $data["lots"];

      $selectSql = "SELECT A.auction_id, I.type_id FROM Auction A, ItemType I WHERE A.auction_num = ? AND I.code = ?";
      $result = $conn->Execute($selectSql, array($auctionNum, $type))->GetRows();
      if (count($result)) {
        $auctionId = intval($result[0]["auction_id"]);
        $typeId = intval($result[0]["type_id"]);
      }

      if ($auctionId == 0 || $typeId == 0) {
        throw new Exception("Auction no.: $auctionNum or type: $type not exists!");
      }

      for ($i = 0; $i < Count($lots); ++$i){
        $curLot = $lots[$i];
        $lotNum = trim($curLot["lot_num"]);
        $gldFileRef = trim($curLot["gld_file_ref"]);
        $ref = trim($curLot["ref"]);
        $deptEn = trim($curLot["dept_en"]);
        $deptTc = trim($curLot["dept_tc"]);
        $deptSc = str_chinese_simp(trim($curLot["dept_tc"]));
        $contactEn = trim($curLot["contact_en"]);
        $contactTc = trim($curLot["contact_tc"]);
        $contactSc = str_chinese_simp(trim($curLot["contact_tc"]));
        $numberEn = trim($curLot["number_en"]);
        $numberTc = trim($curLot["number_tc"]);
        $numberSc = str_chinese_simp(trim($curLot["number_tc"]));
        $locationEn = trim($curLot["location_en"]);
        $locationTc = trim($curLot["location_tc"]);
        $locationSc = str_chinese_simp(trim($curLot["location_tc"]));
        $remarksEn = trim($curLot["remarks_en"]);
        $remarksTc = trim($curLot["remarks_tc"]);
        $remarksSc = str_chinese_simp(trim($curLot["remarks_tc"]));
        $items = $curLot["items"];

        $insertSql = "INSERT INTO AuctionLot (
                        auction_id, type_id, lot_num, seq, gld_file_ref, reference, department_en, department_tc, department_sc,
                        contact_en, contact_tc, contact_sc, number_en, number_tc, number_sc, 
                        location_en, location_tc, location_sc, remarks_en, remarks_tc, remarks_sc, 
                        icon, photo_url, photo_real,
                        transaction_currency, transaction_price, transaction_status,
                        status, last_update
                      )
                      SELECT
                        ?, ?, ?, COUNT(*) + 1, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, 
                        'fontawesome.box', '', 0,
                        '', 0, ?,
                        ?, now()
                      FROM AuctionLot
                      WHERE auction_id = ?";
        
        $result = $conn->Execute($insertSql, array(
          $auctionId, $typeId, $lotNum, $gldFileRef, $ref, $deptEn, $deptTc, $deptSc,
          $contactEn, $contactTc, $contactSc, $numberEn, $numberTc, $numberSc,
          $locationEn, $locationTc, $locationSc, $remarksEn, $remarksTc, $remarksSc,
          TransactionStatus::NotSold,
          Status::Active,
          $auctionId
        ));
        $lastId = $conn->insert_Id();

        $this->importLotItems($lastId, $items);
      }

      $output->data = new StdClass();
      $output->data->id = $auctionId;
      $output->data->type = $type;
      $output->status = "success";
    } catch (Exception $e) {
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function updateAuctionItemPdf() {
    global $conn;

    $output = new stdClass();
    $output->status = "failed";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["id"]) || empty($data["id"]) || !ctype_digit($data["id"])) {
        throw new Exception("ID missing!");  
      }
      
      $auctionId = intval($data["id"]);
      $itemPdfList = ($data["item_pdf_list"]);

      $deleteSql = "DELETE FROM ItemListPdf WHERE auction_id = ?;";
      $result = $conn->Execute($deleteSql, array($auctionId));

      for ($i = 0; $i < Count($itemPdfList); ++$i) {
        $type = $itemPdfList[$i]["type"];
        $urlEn = $itemPdfList[$i]["url_en"];
        $urlTc = $itemPdfList[$i]["url_tc"];
        $urlSc = $itemPdfList[$i]["url_sc"];
        $insertSql = "INSERT INTO ItemListPdf (auction_id, type_id, url_en, url_tc, url_sc)
                      SELECT ?, I.type_id, ?, ?, ?
                      FROM ItemType I
                      WHERE I.code = ?;";
        $result = $conn->Execute($insertSql, array($auctionId, $urlEn, $urlTc, $urlSc, $type));
      }
      
      $output->status = "success";
    } catch (Exception $e) {
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function updateAuctionLot() {
    global $conn;

    $output = new stdClass();
    $output->status = "failed";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["auction_id"]) || empty($data["auction_id"]) || !ctype_digit($data["auction_id"])) {
        throw new Exception("Auction ID missing!");  
      }
      if (!isset($data["lot_id"]) || empty($data["lot_id"]) || !ctype_digit($data["lot_id"])) {
        throw new Exception("Lot ID missing!");  
      }
      
      $lotId = intval($data["lot_id"]);
      if ($lotId == 0) {
        $insertSql = "INSERT INTO AuctionLot (
                        auction_id, type_id, lot_num, seq, gld_file_ref, reference, department_en, department_tc, department_sc,
                        contact_en, contact_tc, contact_sc, number_en, number_tc, number_sc,
                        location_en, location_tc, location_sc, remarks_en, remarks_tc, remarks_sc,
                        icon, photo_url, photo_real, transaction_currency, transaction_price, transaction_status,
                        status, last_update
                      )
                      SELECT ?, I.type_id, ?, ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?, ?,
                      ?, now()
                      FROM ItemType I
                      WHERE code = ?;";

        $result = $conn->Execute($insertSql, array(
          $data["auction_id"], $data["lot_num"], $data["seq"], trim($data["gld_file_ref"]), trim($data["reference"]), trim($data["department_en"]), trim($data["department_tc"]), trim($data["department_sc"]),
          trim($data["contact_en"]), trim($data["contact_tc"]), trim($data["contact_sc"]), trim($data["number_en"]), trim($data["number_tc"]), trim($data["number_sc"]),
          trim($data["location_en"]), trim($data["location_tc"]), trim($data["location_sc"]), trim($data["remarks_en"]), trim($data["remarks_tc"]), trim($data["remarks_sc"]),
          trim($data["lot_icon"]), trim($data["photo_url"]), trim($data["photo_real"]), trim($data["transaction_currency"]), trim($data["transaction_price"]), trim($data["transaction_status"]),
          trim($data["status"]), trim($data["item_code"])
        ));
        $lotId = $conn->insert_Id();
      } else {
        $updateSql = "UPDATE AuctionLot SET
                        type_id = (SELECT type_id FROM ItemType WHERE code = ?),
                        lot_num = ?, seq = ?, gld_file_ref = ?, reference = ?,
                        department_en = ?, department_tc = ?, department_sc = ?,
                        contact_en = ?, contact_tc = ?, contact_sc = ?,
                        number_en = ?, number_tc = ?, number_sc = ?,
                        location_en = ?, location_tc = ?, location_sc = ?,
                        remarks_en = ?, remarks_tc = ?, remarks_sc = ?,
                        icon = ?, photo_url = ?, photo_real = ?,
                        transaction_currency = ?, transaction_price = ?, transaction_status = ?,
                        status = ?, last_update = now()
                      WHERE lot_id = ?;";

        $result = $conn->Execute($updateSql, array(
          trim($data["item_code"]),
          trim($data["lot_num"]), trim($data["seq"]), trim($data["gld_file_ref"]), trim($data["reference"]),
          trim($data["department_en"]), trim($data["department_tc"]), trim($data["department_sc"]),
          trim($data["contact_en"]), trim($data["contact_tc"]), trim($data["contact_sc"]),
          trim($data["number_en"]), trim($data["number_tc"]), trim($data["number_sc"]),
          trim($data["location_en"]), trim($data["location_tc"]), trim($data["location_sc"]),
          trim($data["remarks_en"]), trim($data["remarks_tc"]), trim($data["remarks_sc"]),
          trim($data["lot_icon"]), trim($data["photo_url"]), $data["photo_real"],
          trim($data["transaction_currency"]), trim($data["transaction_price"]), trim($data["transaction_status"]),
          trim($data["status"]),
          $lotId
        ));
      }
      
      // delete the existing items first, then add back
      $items = $data["item_list"];
      $deleteSql = "DELETE FROM AuctionItem WHERE lot_id = ?";
      $result = $conn->Execute($deleteSql, array($lotId));
      $this->addLotItems($lotId, $items);

      $output->status = "success";
    } catch (Exception $e) {
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function addLotItems($lotId, $items) {
    global $conn;

    for ($i = 0; $i < Count($items); ++$i) {
      $curItem = $items[$i];

      $icon = trim($curItem["icon"]);
      $descriptionEn = trim($curItem["description_en"]);
      $descriptionTc = trim($curItem["description_tc"]);
      $descriptionSc = trim($curItem["description_sc"]);
      $quantity = trim($curItem["quantity"]);
      $unitEn = trim($curItem["unit_en"]);
      $unitTc = trim($curItem["unit_tc"]);
      $unitSc = trim($curItem["unit_sc"]);
      $insertSql = "INSERT INTO AuctionItem (
                      lot_id, seq, icon, description_en, description_tc, description_sc, 
                      quantity, unit_en, unit_tc, unit_sc
                    ) VALUES (
                      ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, ?
                    );";
      
      $result = $conn->Execute($insertSql, array(
        $lotId, $i+1, $icon, $descriptionEn, $descriptionTc, $descriptionSc, 
        $quantity, $unitEn, $unitTc, $unitSc 
      ));
    }
  }

  function importLotItems($lotId, $items) {
    global $conn;

    for ($i = 0; $i < Count($items); ++$i) {
      $curItem = $items[$i];

      $descriptionEn = trim($curItem["description_en"]);
      $descriptionTc = trim($curItem["description_tc"]);
      $descriptionSc = str_chinese_simp(trim($curItem["description_tc"]));
      $quantity = trim($curItem["quantity"]);
      $unitEn = trim($curItem["unit_en"]);
      $unitTc = trim($curItem["unit_tc"]);
      $unitSc = str_chinese_simp(trim($curItem["unit_tc"]));
      $insertSql = "INSERT INTO AuctionItem (
                      lot_id, seq, icon, description_en, description_tc, description_sc, 
                      quantity, unit_en, unit_tc, unit_sc
                    ) VALUES (
                      ?, ?, 'fontawesome.box', ?, ?, ?,
                      ?, ?, ?, ?
                    );";
      
      $result = $conn->Execute($insertSql, array(
        $lotId, $i+1, $descriptionEn, $descriptionTc, $descriptionSc, 
        $quantity, $unitEn, $unitTc, $unitSc 
      ));
    }
  }

  function importResult() {
    global $conn;

    $output = new stdClass();
    $output->status = "failed";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["auction_num"]) || empty($data["auction_num"])) {
        throw new Exception("Auction no. missing!");  
      }

      if (!isset($data["lots"]) || empty($data["lots"])) {
        throw new Exception("Lot data missing!");  
      }

      $auctionId = 0;
      $auctionNum = trim($data["auction_num"]);
      $lots = $data["lots"];

      $selectSql = "SELECT A.auction_id FROM Auction A WHERE A.auction_num = ?";
      $result = $conn->Execute($selectSql, array($auctionNum))->GetRows();
      if (count($result)) {
        $auctionId = intval($result[0]["auction_id"]);
      }

      if ($auctionId == 0) {
        throw new Exception("Auction no.: $auctionNum not exists!");
      }

      for ($i = 0; $i < Count($lots); ++$i){
        $lotNum = trim($lots[$i]["lot_num"]);
        $price = floatval($lots[$i]["price"]);

        $updateSql = "UPDATE AuctionLot SET
                        transaction_currency = ?,
                        transaction_price = ?,
                        transaction_status = ?
                      WHERE auction_id = ? AND lot_num = ? ";
        
        $result = $conn->Execute($updateSql, array(
          "HKD", $price, TransactionStatus::Sold, $auctionId, $lotNum
        ));
      }

      $output->data = new StdClass();
      $output->data->id = $auctionId;
      $output->status = "success";
    } catch (Exception $e) {
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function listPush() {
    global $conn;

    $output = new stdClass();
    
    try {
      $selectSql = "SELECT push_id, title_en, title_tc, title_sc, body_en, body_tc, body_sc, push_date, status FROM PushHistory ORDER BY push_id DESC";

      $result = $conn->Execute($selectSql)->GetRows();
      $rowNum = count($result);

      $output = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $push = new stdClass();
        $push->id = $result[$i]["push_id"];
        $push->title_en = $result[$i]["title_en"];
        $push->title_tc = $result[$i]["title_tc"];
        $push->title_sc = $result[$i]["title_sc"];
        $push->body_en = $result[$i]["body_en"];
        $push->body_tc = $result[$i]["body_tc"];
        $push->body_sc = $result[$i]["body_sc"];
        $push->push_date = $result[$i]["push_date"];
        $push->status = $result[$i]["status"];

        $output[] = $push;
      }
    } catch (Exception $e) {
      $output->status = "failed";
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function sendPush() {
    global $conn;

    //for testing only!!!
    sleep(2);

    $output = new stdClass();
    $output->status = "failed";
    
    try{
      $data = json_decode(file_get_contents('php://input'), true);

      $titleEn = trim($data["title_en"]);
      $titleTc = trim($data["title_tc"]);
      $titleSc = trim($data["title_sc"]);
      $bodyEn = trim($data["body_en"]);
      $bodyTc = trim($data["body_tc"]);
      $bodySc = trim($data["body_sc"]);

      $insertSql = "INSERT INTO PushHistory (title_en, title_tc, title_sc, body_en, body_tc, body_sc, push_date, status) 
                    VALUES (
                      ?, ?, ?, ?, ?, ?, now(), ?
                    );";

      $result = $conn->Execute($insertSql, array($titleEn, $titleTc, $titleSc, $bodyEn, $bodyTc, $bodySc, PushStatus::Sending));

      $output->status = "success";
    } catch (Exception $e) {
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }
}
?>