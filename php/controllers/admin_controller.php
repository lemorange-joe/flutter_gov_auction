<?php
include_once ('../class/push_manager.php');
include_once ('../class/push_result.php');
include_once ('../include/enum.php');
include_once ('../include/common.php');

class AdminController {
  function listAuction() {
    global $conn;

    $selectSql = "SELECT T.*, GROUP_CONCAT(C2.total ORDER BY C2.seq SEPARATOR ', ') as item_count
                    FROM (
                      SELECT
                        A.auction_id, A.auction_num, A.start_time, A.auction_pdf_en, A.auction_pdf_tc, A.auction_pdf_sc,
                        A.result_pdf_en, A.result_pdf_tc, A.result_pdf_sc, A.remarks_en, A.remarks_tc, A.remarks_sc, 
                        A.auction_status, A.status, A.last_update,
                        (
                          SELECT COUNT(*)
                          FROM AuctionLot L1
                          WHERE A.auction_id = L1.auction_id AND L1.featured = 1
                        ) as featured_count,
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
                    ) as T LEFT JOIN (
                      SELECT L.auction_id, I.seq, concat(I.code, ': ',  COUNT(*)) as 'total'
                      FROM AuctionLot L
                      INNER JOIN ItemType I ON L.type_id = I.type_id
                      INNER JOIN AuctionItem AI ON L.lot_id = AI.lot_id
                      WHERE L.status = 'A'
                      GROUP BY L.auction_id, I.seq, I.code
                    ) as C2 ON T.auction_id = C2.auction_id
                    GROUP BY T.auction_id
                    ORDER BY T.auction_id DESC";

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
      $auction->remarks_en = $result[$i]["remarks_en"];
      $auction->remarks_tc = $result[$i]["remarks_tc"];
      $auction->remarks_sc = $result[$i]["remarks_sc"];
      $auction->auction_status = $result[$i]["auction_status"];
      $auction->status = $result[$i]["status"];
      $auction->last_update = $result[$i]["last_update"];
      $auction->featured_count = $result[$i]["featured_count"];
      $auction->lot_count = $result[$i]["lot_count"];
      $auction->item_count = $result[$i]["item_count"];

      $output[] = $auction;
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function getAuction($param) {
    global $conn;

    $auctionId = $param[0];
    $itemType = $param[1];
    $output = new stdClass();

    // ------ 1. get auction ------
    $selectSql = "SELECT
                    A.auction_id, A.auction_num, A.start_time, L.address_en, L.address_tc, L.address_sc,
                    A.auction_pdf_en, A.auction_pdf_tc, A.auction_pdf_sc,
                    A.result_pdf_en, A.result_pdf_tc, A.result_pdf_sc, 
                    A.remarks_en, A.remarks_tc, A.remarks_sc, 
                    A.auction_status, status, last_update
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
      $output->remarks_en = $result[0]["remarks_en"];
      $output->remarks_tc = $result[0]["remarks_tc"];
      $output->remarks_sc = $result[0]["remarks_sc"];
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
                    location_en, location_tc, location_sc, L.remarks_en, L.remarks_tc, L.remarks_sc,
                    item_condition_en, item_condition_tc, item_condition_sc,
                    L.featured, L.icon as 'lot_icon', L.photo_url, L.photo_real,
                    L.transaction_currency, L.transaction_price, L.transaction_status, L.status, L.last_update,
                    I.item_id, I.icon as 'item_icon', I.description_en, I.description_tc, I.description_sc,
                    I.quantity, I.unit_en, I.unit_tc, I.unit_sc
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
        $curLotOutput->item_code = $result[$i]["code"];
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
        $curLotOutput->item_condition_en = $result[$i]["item_condition_en"];
        $curLotOutput->item_condition_tc = $result[$i]["item_condition_tc"];
        $curLotOutput->item_condition_sc = $result[$i]["item_condition_sc"];

        $curLotOutput->featured = $result[$i]["featured"];
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

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function updateAuction() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

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
      $remarksEn = trim($data["remarks_en"]);
      $remarksTc = trim($data["remarks_tc"]);
      $remarksSc = trim($data["remarks_sc"]);
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
                      remarks_en = ?,
                      remarks_tc = ?,
                      remarks_sc = ?,
                      auction_status = ?,
                      status = ?,
                      last_update = now()
                    WHERE auction_id = ?";

    $result = $conn->Execute($updateSql, array(
      $auctionNum, $startTime, $auctionPdfEn, $auctionPdfTc, $auctionPdfSc, $resultPdfEn, $resultPdfTc, $resultPdfSc, 
      $remarksEn, $remarksTc, $remarksSc, $auctionStatus, $status, $id
    ));

      $output->status = "success";
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function createAuction() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

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
      $remarksEn = trim($data["remarks_en"]);
      $remarksTc = trim($data["remarks_tc"]);
      $remarksSc = trim($data["remarks_sc"]);
      $auctionStatus = trim($data["auction_status"]);
      $status = trim($data["status"]);

      $insertSql = "INSERT INTO Auction (
                      auction_num, start_time, location_id, auction_pdf_en, auction_pdf_tc, auction_pdf_sc, 
                      result_pdf_en, result_pdf_tc, result_pdf_sc, remarks_en, remarks_tc, remarks_sc, 
                      auction_status, status, last_update
                    ) VALUES (
                      ?, ?, 1, ?, ?, ?, 
                      ?, ?, ?, ?, ?, ?, 
                      ?, ?, now()
                    );";

    $result = $conn->Execute($insertSql, array(
      $auctionNum, $startTime, $auctionPdfEn, $auctionPdfTc, $auctionPdfSc, 
      $resultPdfEn, $resultPdfTc, $resultPdfSc, $remarksEn, $remarksTc, $remarksSc, 
      $auctionStatus, $status
    ));

      $output->status = "success";
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function importAuction() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

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
        $itemConditionEn = trim($curLot["item_condition_en"]);
        $itemConditionTc = trim($curLot["item_condition_tc"]);
        $itemConditionSc = str_chinese_simp(trim($curLot["item_condition_tc"]));
        $items = $curLot["items"];

        $insertSql = "INSERT INTO AuctionLot (
                        auction_id, type_id, lot_num, seq, gld_file_ref, reference, department_en, department_tc, department_sc,
                        contact_en, contact_tc, contact_sc, number_en, number_tc, number_sc,
                        location_en, location_tc, location_sc, remarks_en, remarks_tc, remarks_sc,
                        item_condition_en, item_condition_tc, item_condition_sc,
                        featured, icon, photo_url, photo_real,
                        transaction_currency, transaction_price, transaction_status,
                        status, last_update
                      )
                      SELECT
                        ?, ?, ?, COUNT(*) + 1, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, 
                        0, 'fontawesome.box', '', 0,
                        '', 0, ?,
                        ?, now()
                      FROM AuctionLot
                      WHERE auction_id = ?";
        
        $result = $conn->Execute($insertSql, array(
          $auctionId, $typeId, $lotNum, $gldFileRef, $ref, $deptEn, $deptTc, $deptSc,
          $contactEn, $contactTc, $contactSc, $numberEn, $numberTc, $numberSc,
          $locationEn, $locationTc, $locationSc, $remarksEn, $remarksTc, $remarksSc,
          $itemConditionEn, $itemConditionTc, $itemConditionSc,
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
      $output->status = "error";
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function updateAuctionItemPdf() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

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
      $output->status = "error";
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function updateAuctionLotFeatured(){
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["lot_id"]) || empty($data["lot_id"]) || !ctype_digit($data["lot_id"])) {
        throw new Exception("Lot ID missing!");  
      }
      if (!isset($data["featured"])) {
        throw new Exception("Featured missing!");  
      }
      
      $lotId = intval($data["lot_id"]);
      $featured = intval($data["featured"]);
      
      $updateSql = "UPDATE AuctionLot SET featured = ?, last_update = now() WHERE lot_id = ?";
      $result = $conn->Execute($updateSql, array($featured, $lotId));

      $selectSql = "SELECT last_update FROM AuctionLot WHERE lot_id = ?";
      $result = $conn->Execute($selectSql, array($lotId))->GetRows();

      if (Count($result) > 0) {
        $output->status = "success";
        $output->data = $result[0]['last_update'];
      }      
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function updateAuctionLot() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

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
                        item_condition_en, item_condition_tc, item_condition_sc,
                        featured, icon, photo_url, photo_real, transaction_currency, transaction_price, transaction_status,
                        status, last_update
                      )
                      SELECT ?, I.type_id, ?, ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, 
                      ?, ?, ?, ?, ?, ?, ?,
                      ?, now()
                      FROM ItemType I
                      WHERE code = ?;";

        $result = $conn->Execute($insertSql, array(
          $data["auction_id"], $data["lot_num"], $data["seq"], trim($data["gld_file_ref"]), trim($data["reference"]), trim($data["department_en"]), trim($data["department_tc"]), trim($data["department_sc"]),
          trim($data["contact_en"]), trim($data["contact_tc"]), trim($data["contact_sc"]), trim($data["number_en"]), trim($data["number_tc"]), trim($data["number_sc"]),
          trim($data["location_en"]), trim($data["location_tc"]), trim($data["location_sc"]), trim($data["remarks_en"]), trim($data["remarks_tc"]), trim($data["remarks_sc"]),
          trim($data["item_condition_en"]), trim($data["item_condition_tc"]), trim($data["item_condition_sc"]),
          trim($data["featured"]), trim($data["lot_icon"]), trim($data["photo_url"]), trim($data["photo_real"]), trim($data["transaction_currency"]), trim($data["transaction_price"]), trim($data["transaction_status"]),
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
                        item_condition_en = ?, item_condition_tc = ?, item_condition_sc = ?,
                        featured = ?, icon = ?, photo_url = ?, photo_real = ?,
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
          trim($data["item_condition_en"]), trim($data["item_condition_tc"]), trim($data["item_condition_sc"]),
          $data["featured"], trim($data["lot_icon"]), trim($data["photo_url"]), $data["photo_real"],
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
      $output->status = "error";
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
    $output->status = "fail";

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
      $output->status = "error";
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function listPush($param) {
    global $conn;

    $start = isset($param) && is_array($param) && count($param) >= 1 ? trim($param[0]) : 0;
    $size = isset($param) && is_array($param) && count($param) >= 2 ? trim($param[1]) : 3;

    $output = new stdClass();
    
    try {
      $selectSql = "SELECT
                      push_id, title_en, title_tc, title_sc, body_en, body_tc, body_sc, push_date, 
                      result_en, status_en, last_sent_en, result_tc, status_tc, last_sent_tc, result_sc, status_sc, last_sent_sc,
                      status
                    FROM PushHistory ORDER BY push_id DESC LIMIT ?, ?";

      $result = $conn->Execute($selectSql, array($start, $size))->GetRows();
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
        
        $push->result_en = $result[$i]["result_en"];
        $push->status_en = $result[$i]["status_en"];
        $push->last_sent_en = $result[$i]["last_sent_en"];
        $push->result_tc = $result[$i]["result_tc"];
        $push->status_tc = $result[$i]["status_tc"];
        $push->last_sent_tc = $result[$i]["last_sent_tc"];
        $push->result_sc = $result[$i]["result_sc"];
        $push->status_sc = $result[$i]["status_sc"];
        $push->last_sent_sc = $result[$i]["last_sent_sc"];
        
        $push->status = $result[$i]["status"];

        $output[] = $push;
      }
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function resendPush($param) {
    global $conn, $PUSH_PASSWORD_HASHED;

    $output = new stdClass();
    $output->status = "fail";
    
    try{
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["push_id"]) || trim($data["push_id"]) == "" || 
          !isset($data["lang"]) || trim($data["lang"]) == "" || 
          !isset($data["push_password"]) || trim($data["push_password"]) == "") {
            throw new Exception("Data is empty!");
          }

      $pushId = intval($data["push_id"]);
      $lang = $data["lang"];
      $pushPassword = md5($data["push_password"]);

      if (strcmp($pushPassword, $PUSH_PASSWORD_HASHED) !== 0) {
        throw new Exception("Wrong push password!");
      }

      $selectSql = "SELECT title_$lang as 'title', body_$lang as 'body' FROM PushHistory WHERE push_id = ?";
      $result = $conn->Execute($selectSql, array($pushId))->GetRows();

      if (count($result) > 0) {
        $title = $result[0]["title"];
        $body = $result[0]["body"];

        $pushManager = new PushManager();
        $pushResult = $pushManager->resend($lang, $title, $body);
        $pushSuccess = strpos(strtolower($pushResult), "error") === false;
        $pushSent = GetCurrentLocalTime();

        $updateSql = "UPDATE PushHistory SET 
                        result_$lang = ?, status_$lang = ?, last_sent_$lang = ?
                      WHERE push_id = ?";
        $result = $conn->Execute($updateSql, array(
          $pushResult, $pushSuccess ? PushStatus::Sent : PushStatus::Failed, FormatMysqlDateTime($pushSent),
          $pushId
        ));

        $output->status = "success";
      } else {
        $output->message = "Push ID: $pushId not found!";
      }
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function sendPush() {
    global $conn, $PUSH_PASSWORD_HASHED;

    $output = new stdClass();
    $output->status = "fail";
    
    try{
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["title_en"]) || trim($data["title_en"]) == "" || 
          !isset($data["title_tc"]) || trim($data["title_tc"]) == "" || 
          !isset($data["title_sc"]) || trim($data["title_sc"]) == "" || 
          !isset($data["body_en"]) || trim($data["body_en"]) == "" || 
          !isset($data["body_tc"]) || trim($data["body_tc"]) == "" || 
          !isset($data["body_sc"]) || trim($data["body_sc"]) == "" ||
          !isset($data["push_password"]) || trim($data["push_password"]) == "") {
            throw new Exception("Data is empty!");
          }

      $pushPassword = md5($data["push_password"]);

      if (strcmp($pushPassword, $PUSH_PASSWORD_HASHED) !== 0) {
        throw new Exception("Wrong push password!");
      }

      $pushData = new PushData(
        trim($data["title_en"]), trim($data["title_tc"]), trim($data["title_sc"]), 
        trim($data["body_en"]), trim($data["body_tc"]), trim($data["body_sc"])
      );

      $insertSql = "INSERT INTO PushHistory (
                      title_en, title_tc, title_sc, body_en, body_tc, body_sc, push_date, 
                      result_en, status_en, last_sent_en,
                      result_tc, status_tc, last_sent_tc,
                      result_sc, status_sc, last_sent_sc,
                      status
                    ) VALUES (
                      ?, ?, ?, ?, ?, ?, now(), 
                      '', '', '1900-01-01',
                      '', '', '1900-01-01',
                      '', '', '1900-01-01',
                      ?
                    );";

      $result = $conn->Execute($insertSql, array(
        $pushData->titleEn, $pushData->titleTc, $pushData->titleSc, $pushData->bodyEn, $pushData->bodyTc, $pushData->bodySc,
        PushStatus::Sending
      ));

      $pushId = $conn->insert_Id();

      if ($pushId > 0) {
        $pushManager = new PushManager();
        $pushResult = $pushManager->send($pushId, $pushData);

        $updateSql = "UPDATE PushHistory SET 
                        result_en = ?, status_en = ?, last_sent_en = ?,
                        result_tc = ?, status_tc = ?, last_sent_tc = ?,
                        result_sc = ?, status_sc = ?, last_sent_sc = ?,
                        status = ?
                      WHERE push_id = ?";
        $result = $conn->Execute($updateSql, array(
          $pushResult->resultEn, $pushResult->successEn ? PushStatus::Sent : PushStatus::Failed, FormatMysqlDateTime($pushResult->sentEn),
          $pushResult->resultTc, $pushResult->successTc ? PushStatus::Sent : PushStatus::Failed, FormatMysqlDateTime($pushResult->sentTc),
          $pushResult->resultSc, $pushResult->successSc ? PushStatus::Sent : PushStatus::Failed, FormatMysqlDateTime($pushResult->sentSc),
          $pushResult->success() ? PushStatus::Sent : PushStatus::Failed,
          $pushId
        ));
      }

      $output->status = "success";
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function listKeywordImage($param) {
    global $conn;

    $keyword = isset($param) && is_array($param) && count($param) >= 1 ? trim($param[0]) : "";

    $selectSql = "SELECT
                    keyword_image_id, keyword_en, keyword_tc, image_url
                  FROM KeywordImage
                  WHERE ? = '' OR keyword_en LIKE ? OR keyword_tc LIKE ?";

    $result = $conn->Execute($selectSql, array(
      $keyword, "%".$keyword."%", "%".$keyword."%"
    ))->GetRows();
    $rowNum = count($result);

    $output = array();
    for($i = 0; $i < $rowNum; ++$i) {
      $keywordImage = new stdClass();
      $keywordImage->id = $result[$i]["keyword_image_id"];
      $keywordImage->keyword_en = $result[$i]["keyword_en"];
      $keywordImage->keyword_tc = $result[$i]["keyword_tc"];
      $keywordImage->image_url = $result[$i]["image_url"];
      
      $output[] = $keywordImage;
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function getKeywordImageUrl($param) {
    global $conn;

    $keyword = isset($param) && is_array($param) && count($param) >= 1 ? trim($param[0]) : "";

    $output = array();
    if (!empty($keyword)) {
      $selectSql = "SELECT image_url FROM KeywordImage WHERE keyword_en LIKE ? OR keyword_tc LIKE ?";

      $result = $conn->Execute($selectSql, array("%".$keyword."%", "%".$keyword."%"))->GetRows();
      $rowNum = count($result);

      for($i = 0; $i < $rowNum; ++$i) {
        $output[] = $result[$i]["image_url"];
      }
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function createKeywordImage() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["image_url"]) || empty($data["image_url"])) {
        throw new Exception("Image URL missing!");  
      }

      $keywordEn = empty(trim($data["keyword_en"])) ? "-" : trim($data["keyword_en"]);
      $keywordTc = empty(trim($data["keyword_tc"])) ? "-" : trim($data["keyword_tc"]);
      $imageUrl = trim($data["image_url"]);

      $insertSql = "INSERT INTO KeywordImage (keyword_en, keyword_tc, image_url) 
                    VALUES (?, ?, ?)";
      
      $result = $conn->Execute($insertSql, array($keywordEn, $keywordTc, $imageUrl));

      $output->status = "success";
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function deleteKeywordImage() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["id"]) || empty($data["id"])) {
        throw new Exception("Image URL missing!");  
      }

      $id = trim($data["id"]);
      $deleteSql = "DELETE FROM KeywordImage WHERE keyword_image_id = ?";
      $result = $conn->Execute($deleteSql, array($id));

      $output->status = "success";
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function getAppInfo() {
    global $conn;

    $output = new stdClass();
    $selectSql = "SELECT data_version, news_en, news_tc, news_sc, last_update FROM AppInfo ORDER BY id DESC LIMIT 1";

    $result = $conn->Execute($selectSql)->GetRows();
    $rowNum = count($result);

    if (count($result) > 0) {
      $output->data_version = $result[0]["data_version"];
      $output->news_en = $result[0]["news_en"];
      $output->news_tc = $result[0]["news_tc"];
      $output->news_sc = $result[0]["news_sc"];
      $output->last_update = $result[0]["last_update"];
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function saveAppInfo() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["data_version"]) || empty($data["data_version"])) {
        throw new Exception("Data Version missing!");  
      }

      $dataVersion = trim($data["data_version"]);
      $newsEn = $data["news_en"];
      $newsTc = $data["news_tc"];
      $newsSc = $data["news_sc"];

      $updateSql = "UPDATE AppInfo SET 
                      data_version = ?, news_en = ?, news_tc = ?, news_sc = ?, last_update = now()
                    WHERE id = 1";

      $result = $conn->Execute($updateSql, array(
        $dataVersion, $newsEn, $newsTc, $newsSc
      ));
  
        $output->status = "success";
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
}
?>