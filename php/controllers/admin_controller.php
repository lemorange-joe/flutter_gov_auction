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
                        A.auction_id, A.auction_num, A.start_time, A.collection_deadline, A.auction_pdf_en, A.auction_pdf_tc, A.auction_pdf_sc,
                        A.result_pdf_en, A.result_pdf_tc, A.result_pdf_sc, A.remarks_en, A.remarks_tc, A.remarks_sc, 
                        A.auction_status, A.status, A.last_update,
                        (
                          SELECT COUNT(*)
                          FROM AuctionLot L1
                          WHERE A.auction_id = L1.auction_id AND L1.featured = 1
                        ) as featured_count,
                        GROUP_CONCAT(C.total ORDER BY C.seq SEPARATOR ', ') as lot_count,
                        (
                          SELECT COUNT(*)
                          FROM AuctionLot L2
                          WHERE A.auction_id = L2.auction_id AND L2.transaction_status = ?
                        ) as sold_count
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

    $result = $conn->Execute($selectSql, array(TransactionStatus::Sold))->GetRows();
    $rowNum = count($result);

    $output = array();
    for($i = 0; $i < $rowNum; ++$i) {
      $auction = new stdClass();
      $auction->id = $result[$i]["auction_id"];
      $auction->num = $result[$i]["auction_num"];
      $auction->start_time = $result[$i]["start_time"];
      $auction->collection_deadline = $result[$i]["collection_deadline"];
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
      $auction->sold_count = $result[$i]["sold_count"];

      $output[] = $auction;
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function getAuction($param) {
    global $conn;

    $auctionId = $param[0];
    $featuredOnly = $param[1];
    $itemType = $param[2];
    $output = new stdClass();

    // ------ 1. get auction ------
    $selectSql = "SELECT
                    A.auction_id, A.auction_num, A.start_time, A.collection_deadline, L.address_en, L.address_tc, L.address_sc,
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
      $output->collection_deadline = $result[0]["collection_deadline"];
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
                    L.lot_id, T.code, L.lot_num,
                    gld_file_ref, reference, department_en, department_tc, department_sc,
                    contact_en, contact_tc, contact_sc, number_en, number_tc, number_sc,
                    location_en, location_tc, location_sc, L.remarks_en, L.remarks_tc, L.remarks_sc,
                    item_condition_en, item_condition_tc, item_condition_sc,
                    L.description_en as 'lot_description_en', L.description_tc as 'lot_description_tc', L.description_sc as 'lot_description_sc',
                    L.featured, L.icon as 'lot_icon', L.photo_url, L.photo_real, L.photo_author, L.photo_author_url, 
                    L.transaction_currency, L.transaction_price, L.transaction_status, L.status, L.last_update,
                    I.item_id, I.icon as 'item_icon', I.description_en, I.description_tc, I.description_sc,
                    I.quantity, I.unit_en, I.unit_tc, I.unit_sc
                  FROM Auction A
                  INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                  INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                  INNER JOIN ItemType T ON L.type_id = T.type_id
                  WHERE A.auction_id = ? AND (L.featured = 1 OR ? = 0) AND (T.code = ? OR ? = '')
                  ORDER BY T.seq, L.lot_num, I.seq";

    $result = $conn->Execute($selectSql, array($auctionId, $featuredOnly, $itemType, $itemType))->GetRows();
    $rowNum = count($result);

    // 4. select all inspection dates of the auction id
    // then assign back to the lot programatically
    $selectSql = "SELECT I.inspection_id, I.lot_id, I.inspection_day, I.inspection_start_time, I.inspection_end_time, I.typhoon_start_time, I.typhoon_end_time
                  FROM InspectionDate I
                  INNER JOIN AuctionLot L ON I.lot_id = L.lot_id
                  WHERE L.auction_id = ?
                  ORDER BY L.lot_id, CASE
                    WHEN I.inspection_day = 7 THEN 0
                    ELSE I.inspection_day
                  END";
    $inspectionDateResult = $conn->Execute($selectSql, array($auctionId))->GetRows();

    $curLotNum = "";
    $curLotOutput = new stdClass();
    $curItemList = array();
    for($i = 0; $i < $rowNum; ++$i) {
      if ($curLotNum != $result[$i]["lot_num"]) {
        if ($i > 0) {
          // add existing to the current lot first
          $curLotOutput->item_list = $curItemList;
          $curLotOutput->inspection_date_list = $this->getInspectionDateList($curLotOutput->lot_id, $inspectionDateResult);
          // no need check whether it is special inspection date for admin
          $output->lot_list[] = $curLotOutput;
        }

        // prepare to start next lot
        $curLotNum = $result[$i]["lot_num"];
        $curLotOutput = new stdClass();
        $curLotOutput->lot_id = intval($result[$i]["lot_id"]);
        $curLotOutput->item_code = $result[$i]["code"];
        $curLotOutput->lot_num = $result[$i]["lot_num"];
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
        $curLotOutput->lot_description_en = $result[$i]["lot_description_en"];
        $curLotOutput->lot_description_tc = $result[$i]["lot_description_tc"];
        $curLotOutput->lot_description_sc = $result[$i]["lot_description_sc"];

        $curLotOutput->featured = $result[$i]["featured"];
        $curLotOutput->lot_icon = $result[$i]["lot_icon"];
        $curLotOutput->photo_url = $result[$i]["photo_url"];
        $curLotOutput->photo_real = $result[$i]["photo_real"];
        $curLotOutput->photo_author = $result[$i]["photo_author"];
        $curLotOutput->photo_author_url = $result[$i]["photo_author_url"];

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
      $curLotOutput->inspection_date_list = $this->getInspectionDateList($curLotOutput->lot_id, $inspectionDateResult);
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
      $collectionDeadline = $data["collection_deadline"];
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
                      collection_deadline = ?,
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
      $auctionNum, $startTime, $collectionDeadline, $auctionPdfEn, $auctionPdfTc, $auctionPdfSc, $resultPdfEn, $resultPdfTc, $resultPdfSc, 
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
      $collectionDeadline = $data["collection_deadline"];
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
                      auction_num, start_time, collection_deadline, location_id, auction_pdf_en, auction_pdf_tc, auction_pdf_sc, 
                      result_pdf_en, result_pdf_tc, result_pdf_sc, remarks_en, remarks_tc, remarks_sc, 
                      auction_status, status, last_update
                    ) VALUES (
                      ?, ?, ?, 1, ?, ?, ?, 
                      ?, ?, ?, ?, ?, ?, 
                      ?, ?, now()
                    );";

    $result = $conn->Execute($insertSql, array(
      $auctionNum, $startTime, $collectionDeadline, $auctionPdfEn, $auctionPdfTc, $auctionPdfSc, 
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

      $conn->Execute("SET session group_concat_max_len=15000");

      for ($i = 0; $i < count($lots); ++$i){
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

        $descriptionEn = "";
        $descriptionTc = "";
        for ($j = 0; $j < count($items); ++$j) {
          $curItem = $items[$j];
          $descriptionEn .= CommonGetLotDescription($curItem["description_en"], $curItem["quantity"], $curItem["unit_en"], "en") . ", ";
          $descriptionTc .= CommonGetLotDescription($curItem["description_tc"], $curItem["quantity"], $curItem["unit_tc"], "tc") . ", ";
        }
        $descriptionEn = rtrim($descriptionEn, ", ");
        $descriptionTc = rtrim($descriptionTc, ", ");
        $descriptionSc = str_chinese_simp($descriptionTc);

        $item0SearchKeywordEn = count($items) > 0 ? CommonGetSearchKeyword($items[0]["description_en"], "en") : "xxx";
        $item0SearchKeywordTc = count($items) > 0 ? CommonGetSearchKeyword($items[0]["description_tc"], "tc") : "xxx";
        $keywordPhotoAuthor = $this->getKeywordPhotoAuthor($item0SearchKeywordEn, $item0SearchKeywordTc);

        $insertSql = "INSERT INTO AuctionLot (
                        auction_id, type_id, lot_num, gld_file_ref, reference, department_en, department_tc, department_sc,
                        contact_en, contact_tc, contact_sc, number_en, number_tc, number_sc,
                        location_en, location_tc, location_sc, remarks_en, remarks_tc, remarks_sc,
                        item_condition_en, item_condition_tc, item_condition_sc,
                        description_en, description_tc, description_sc,
                        featured, icon, photo_url, photo_real,
                        photo_author, photo_author_url, 
                        transaction_currency, transaction_price, transaction_status,
                        status, last_update
                      ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, 
                        ?, ?, ?,
                        0, 'fontawesome.box', ?, 0,
                        ?, ?, 
                        '', 0, ?,
                        ?, now()
                      )";
        
        $result = $conn->Execute($insertSql, array(
          $auctionId, $typeId, $lotNum, $gldFileRef, $ref, $deptEn, $deptTc, $deptSc,
          $contactEn, $contactTc, $contactSc, $numberEn, $numberTc, $numberSc,
          $locationEn, $locationTc, $locationSc, $remarksEn, $remarksTc, $remarksSc,
          $itemConditionEn, $itemConditionTc, $itemConditionSc,
          $descriptionEn, $descriptionTc, $descriptionSc, 
          $keywordPhotoAuthor->photoUrl,
          $keywordPhotoAuthor->author, $keywordPhotoAuthor->authorUrl, 
          TransactionStatus::NotSold,
          Status::Active
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

  function submitInspectionDate() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["auction_num"]) || empty($data["auction_num"])) {
        throw new Exception("Auction no. missing!");  
      }

      $auctionId = 0;
      $auctionNum = trim($data["auction_num"]);
      
      $selectSql = "SELECT auction_id FROM Auction WHERE auction_num = ?";
      $result = $conn->Execute($selectSql, array($auctionNum))->GetRows();
      if (count($result)) {
        $auctionId = intval($result[0]["auction_id"]);
      }

      if ($auctionId == 0) {
        throw new Exception("Auction no.: $auctionNum not exists!");
      }

      $this->importInspectionDate($auctionId, $data["inspection_list"]);

      $output->data = new StdClass();
      $output->data->id = $auctionId;
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

  function updateAuctionLotFeatured() {
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

      if (count($result) > 0) {
        $output->status = "success";
        $output->data = $result[0]['last_update'];
      }      
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function updateAuctionLotIcon() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["lot_id"]) || empty($data["lot_id"]) || !ctype_digit($data["lot_id"])) {
        throw new Exception("Lot ID missing!");  
      }
      if (!isset($data["icon"])) {
        throw new Exception("Icon missing!");  
      }
      
      $lotId = intval($data["lot_id"]);
      $icon = $data["icon"];
      
      $updateSql = "UPDATE AuctionLot SET icon = ?, last_update = now() WHERE lot_id = ?";
      $result = $conn->Execute($updateSql, array($icon, $lotId));

      $selectSql = "SELECT last_update FROM AuctionLot WHERE lot_id = ?";
      $result = $conn->Execute($selectSql, array($lotId))->GetRows();

      if (count($result) > 0) {
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
      if (!isset($data["lot_id"]) || !ctype_digit($data["lot_id"])) {
        throw new Exception("Lot ID missing!");  
      }
      
      $lotId = intval($data["lot_id"]);
      $items = $data["item_list"];

      // build the lot description from items first
      $descriptionEn = "";
      $descriptionTc = "";
      $descriptionSc = "";
      for ($i = 0; $i < count($items); ++$i) {
        $curItem = $items[$i];
        $descriptionEn .= CommonGetLotDescription($curItem["description_en"], $curItem["quantity"], $curItem["unit_en"], "en") . ", ";
        $descriptionTc .= CommonGetLotDescription($curItem["description_tc"], $curItem["quantity"], $curItem["unit_tc"], "tc") . ", ";
        $descriptionSc .= CommonGetLotDescription($curItem["description_sc"], $curItem["quantity"], $curItem["unit_sc"], "sc") . ", ";
      }
      $descriptionEn = rtrim($descriptionEn, ", ");
      $descriptionTc = rtrim($descriptionTc, ", ");
      $descriptionSc = rtrim($descriptionSc, ", ");

      if ($lotId == 0) {
        $insertSql = "INSERT INTO AuctionLot (
                        auction_id, type_id, lot_num, gld_file_ref, reference, department_en, department_tc, department_sc,
                        contact_en, contact_tc, contact_sc, number_en, number_tc, number_sc,
                        location_en, location_tc, location_sc, remarks_en, remarks_tc, remarks_sc,
                        item_condition_en, item_condition_tc, item_condition_sc,
                        description_en, description_tc, description_sc,
                        featured, icon, photo_url, photo_real, 
                        photo_author, photo_author_url,  
                        transaction_currency, transaction_price, transaction_status,
                        status, last_update
                      ) 
                      SELECT ?, I.type_id, ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, 
                      ?, ?, ?, 
                      ?, ?, ?, ?, 
                      ?, ?, 
                      ?, ?, ?,
                      ?, now()
                      FROM ItemType I
                      WHERE code = ?;";

        $result = $conn->Execute($insertSql, array(
          intval($data["auction_id"]), trim($data["lot_num"]), trim($data["gld_file_ref"]), trim($data["reference"]), trim($data["department_en"]), trim($data["department_tc"]), trim($data["department_sc"]),
          trim($data["contact_en"]), trim($data["contact_tc"]), trim($data["contact_sc"]), trim($data["number_en"]), trim($data["number_tc"]), trim($data["number_sc"]),
          trim($data["location_en"]), trim($data["location_tc"]), trim($data["location_sc"]), trim($data["remarks_en"]), trim($data["remarks_tc"]), trim($data["remarks_sc"]),
          trim($data["item_condition_en"]), trim($data["item_condition_tc"]), trim($data["item_condition_sc"]),
          $descriptionEn, $descriptionTc, $descriptionSc, 
          intval($data["featured"]), trim($data["lot_icon"]), trim($data["photo_url"]), intval($data["photo_real"]), 
          trim($data["photo_author"]), trim($data["photo_author_url"]), 
          trim($data["transaction_currency"]), trim($data["transaction_price"]), trim($data["transaction_status"]),
          trim($data["status"]), trim($data["item_code"])
        ));

        $lotId = $conn->insert_Id();
      } else {
        $updateSql = "UPDATE AuctionLot SET
                        type_id = (SELECT type_id FROM ItemType WHERE code = ?),
                        lot_num = ?, gld_file_ref = ?, reference = ?,
                        department_en = ?, department_tc = ?, department_sc = ?,
                        contact_en = ?, contact_tc = ?, contact_sc = ?,
                        number_en = ?, number_tc = ?, number_sc = ?,
                        location_en = ?, location_tc = ?, location_sc = ?,
                        remarks_en = ?, remarks_tc = ?, remarks_sc = ?,
                        item_condition_en = ?, item_condition_tc = ?, item_condition_sc = ?,
                        description_en = ?, description_tc = ?, description_sc = ?, 
                        featured = ?, icon = ?, photo_url = ?, photo_real = ?,
                        photo_author = ?, photo_author_url = ?, 
                        transaction_currency = ?, transaction_price = ?, transaction_status = ?,
                        status = ?, last_update = now()
                      WHERE lot_id = ?;";

        $result = $conn->Execute($updateSql, array(
          trim($data["item_code"]),
          trim($data["lot_num"]), trim($data["gld_file_ref"]), trim($data["reference"]),
          trim($data["department_en"]), trim($data["department_tc"]), trim($data["department_sc"]),
          trim($data["contact_en"]), trim($data["contact_tc"]), trim($data["contact_sc"]),
          trim($data["number_en"]), trim($data["number_tc"]), trim($data["number_sc"]),
          trim($data["location_en"]), trim($data["location_tc"]), trim($data["location_sc"]),
          trim($data["remarks_en"]), trim($data["remarks_tc"]), trim($data["remarks_sc"]),
          trim($data["item_condition_en"]), trim($data["item_condition_tc"]), trim($data["item_condition_sc"]),
          $descriptionEn, $descriptionTc, $descriptionSc,
          $data["featured"], trim($data["lot_icon"]), trim($data["photo_url"]), $data["photo_real"],
          $data["photo_author"], $data["photo_author_url"],
          trim($data["transaction_currency"]), trim($data["transaction_price"]), trim($data["transaction_status"]),
          trim($data["status"]),
          $lotId
        ));
      }

      if ($lotId == 0) {
        throw new Exception("Failed! lot id: 0");
      }
      
      // delete the existing items first, then add back
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
      $searchKeywordEn = CommonGetSearchKeyword($descriptionEn, "en");
      $searchKeywordTc = CommonGetSearchKeyword($descriptionTc, "tc");
      $searchKeywordSc = CommonGetSearchKeyword($descriptionSc, "sc");
      $insertSql = "INSERT INTO AuctionItem (
                      lot_id, seq, icon, description_en, description_tc, description_sc, 
                      quantity, unit_en, unit_tc, unit_sc,
                      search_keyword_en, search_keyword_tc, search_keyword_sc
                    ) VALUES (
                      ?, ?, ?, ?, ?, ?,
                      ?, ?, ?, ?,
                      ?, ?, ?
                    );";
      
      $result = $conn->Execute($insertSql, array(
        $lotId, $i+1, $icon, $descriptionEn, $descriptionTc, $descriptionSc, 
        $quantity, $unitEn, $unitTc, $unitSc,
        $searchKeywordEn, $searchKeywordTc, $searchKeywordSc
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
      $searchKeywordEn = CommonGetSearchKeyword($descriptionEn, "en");
      $searchKeywordTc = CommonGetSearchKeyword($descriptionTc, "tc");
      $searchKeywordSc = CommonGetSearchKeyword($descriptionSc, "sc");

      $insertSql = "INSERT INTO AuctionItem (
                      lot_id, seq, icon, description_en, description_tc, description_sc, 
                      quantity, unit_en, unit_tc, unit_sc,
                      search_keyword_en, search_keyword_tc, search_keyword_sc
                    ) VALUES (
                      ?, ?, 'fontawesome.box', ?, ?, ?,
                      ?, ?, ?, ?,
                      ?, ?, ?
                    );";
      
      $result = $conn->Execute($insertSql, array(
        $lotId, $i+1, $descriptionEn, $descriptionTc, $descriptionSc, 
        $quantity, $unitEn, $unitTc, $unitSc,
        $searchKeywordEn, $searchKeywordTc, $searchKeywordSc
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

  function importInspectionDate($auctionId, $inspectionList) {
    global $conn;

    for ($i = 0; $i < count($inspectionList); ++$i) {
      $curInspection = $inspectionList[$i];
      
      $lotNums = $curInspection["lot_nums"];
      $day = intval($curInspection["day"]);
      $startTime = trim($curInspection["start_time"]);
      $endTime = trim($curInspection["end_time"]);
      $typhoonStartTime = trim($curInspection["typhoon_start_time"]);
      $typhoonEndTime = trim($curInspection["typhoon_end_time"]);

      $lotNumList = explode(",", $lotNums);
      foreach($lotNumList as &$lotNum) {
        $lotNum = "'" . trim($lotNum) ."'";
      }

      $insertSql = "INSERT INTO InspectionDate (lot_id, inspection_day, inspection_start_time, inspection_end_time, typhoon_start_time, typhoon_end_time)
                    SELECT L.lot_id, ?, ?, ?, ?, ?
                    FROM AuctionLot L
                    WHERE L.auction_id = ? AND L.lot_num IN (" . implode(",", $lotNumList) . ") AND NOT EXISTS (
                      SELECT 1
                      FROM InspectionDate I
                      WHERE I.lot_id = L.lot_id AND I.inspection_day = ?
                    )";

      $result = $conn->Execute($insertSql, array(
        $day, substr($startTime, 0, 5), substr($endTime, 0, 5), substr($typhoonStartTime, 0, 5), substr($typhoonEndTime, 0, 5), $auctionId, $day
      ));
    }
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
                    FROM PushHistory
                    ORDER BY push_id DESC LIMIT ?, ?";

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

  function listNoticeLink() {
    global $conn;

    $output = new stdClass();
    
    try {
      $selectSql = "SELECT
                      notice_id, title_en, title_tc, title_sc, url_en, url_tc, url_sc, seq, status
                    FROM NoticeLink
                    ORDER BY notice_id";

      $result = $conn->Execute($selectSql)->GetRows();
      $rowNum = count($result);

      $output = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $noticeLink = new stdClass();
        $noticeLink->id = $result[$i]["notice_id"];
        $noticeLink->title_en = $result[$i]["title_en"];
        $noticeLink->title_tc = $result[$i]["title_tc"];
        $noticeLink->title_sc = $result[$i]["title_sc"];
        $noticeLink->url_en = $result[$i]["url_en"];
        $noticeLink->url_tc = $result[$i]["url_tc"];
        $noticeLink->url_sc = $result[$i]["url_sc"];
        $noticeLink->seq = $result[$i]["seq"];
        $noticeLink->status = $result[$i]["status"];

        $output[] = $noticeLink;
      }
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function updateNoticeLink() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["notice_id"])) {
        throw new Exception("Notice ID missing!");  
      }
      
      $noticeId = intval($data["notice_id"]);
      if ($noticeId == 0) {
        $insertSql = "INSERT INTO NoticeLink (
                        title_en, title_tc, title_sc,
                        url_en, url_tc, url_sc, seq, status
                      ) VALUES ( 
                        ?, ?, ?, 
                        ?, ?, ?, ?, ?
                      );";

        $result = $conn->Execute($insertSql, array(
          trim($data["title_en"]), trim($data["title_tc"]), trim($data["title_sc"]),
          trim($data["url_en"]), trim($data["url_tc"]), trim($data["url_sc"]), intval($data["seq"]), trim($data["status"])
        ));

        $noticeId = $conn->insert_Id();
      } else {
        $updateSql = "UPDATE NoticeLink SET
                        title_en = ?, title_tc = ?, title_sc = ?,
                        url_en = ?, url_tc = ?, url_sc = ?,
                        seq = ?, status = ?
                      WHERE notice_id = ?;";

        $result = $conn->Execute($updateSql, array(
          trim($data["title_en"]), trim($data["title_tc"]), trim($data["title_sc"]),
          trim($data["url_en"]), trim($data["url_tc"]), trim($data["url_sc"]),
          intval($data["seq"]), trim($data["status"]),
          $noticeId
        ));
      }

      if ($noticeId == 0) {
        throw new Exception("Failed! Notice id: 0");
      }

      $output->status = "success";
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  // for initial setup batch update only
  // function batchUpdateItemSearchKeyword() {
  //   global $conn;
  //   $selectSql = "SELECT item_id, description_en, description_tc, description_sc
  //                 FROM AuctionItem";
    
  //   $result = $conn->Execute($selectSql)->GetRows();
  //   $rowNum = count($result);
  //   echo "total: $rowNum\n";

  //   for($i = 0; $i < $rowNum; ++$i) {
  //     $itemId = $result[$i]['item_id'];
  //     $descriptionEn = $result[$i]['description_en'];
  //     $descriptionTc = $result[$i]['description_tc'];
  //     $descriptionSc = $result[$i]['description_sc'];
  //     $searchKeywordEn = CommonGetSearchKeyword($descriptionEn, "en");
  //     $searchKeywordTc = CommonGetSearchKeyword($descriptionTc, "tc");
  //     $searchKeywordSc = CommonGetSearchKeyword($descriptionSc, "sc");

  //     $updateSql = "UPDATE AuctionItem 
  //                   SET search_keyword_en = ?, search_keyword_tc = ?, search_keyword_sc = ?
  //                   WHERE item_id = ?";

  //     $conn->Execute($updateSql, array($searchKeywordEn, $searchKeywordTc, $searchKeywordSc, $itemId));
      
  //     echo "[$i]$itemId: $searchKeywordEn, $searchKeywordTc, $searchKeywordSc\n";
  //   }

  //   echo "DONE!";
  // }

  // to be called directly from URL
  function batchUpdateAuctionLotImage() {
    global $conn;
    $selectSql = "SELECT L.lot_id, I.description_en, I.description_tc
                  FROM AuctionLot L
                  INNER JOIN AuctionItem I ON L.lot_id = I.lot_id
                  WHERE L.photo_url = '' AND I.seq = 1
                  ORDER BY L.lot_id";
    
    $auctionLotResult = $conn->Execute($selectSql)->GetRows();
    $total = count($auctionLotResult);
    echo "Batch update auction lot image<br />Total: $total<hr />";

    for($i = 0; $i < $total; ++$i) {
      $lotId = $auctionLotResult[$i]['lot_id'];
      $keywordEn = CommonGetSearchKeyword($auctionLotResult[$i]['description_en'], "en");
      $keywordTc = CommonGetSearchKeyword($auctionLotResult[$i]['description_tc'], "tc");
      
      $selectSql = "SELECT image_url FROM KeywordImage WHERE keyword_en = ? OR keyword_tc = ?";
      $result = $conn->Execute($selectSql, array($keywordEn, $keywordTc))->GetRows();
      $rowNum = count($result);

      echo "$lotId: $keywordEn, $keywordTc ";
      if ($rowNum > 0) {
        $randIndex = rand(0, $rowNum - 1);
        $imageUrl = $result[$randIndex]["image_url"];

        $updateSql = "UPDATE AuctionLot SET photo_url = ?, photo_real = 0 WHERE lot_id = ?";
        $conn->Execute($updateSql, array($imageUrl, $lotId));

        echo "<span style='color: #090'> -> $imageUrl</span><br>";
      } else {
        echo "<span style='color: #b00'>NOT FOUND!</span><br>";
      }
    }

    echo "<hr />DONE!";
  }

  private function getInspectionDateList($lotId, $inspectionDateList) {
    $output = array();
    $rowNum = count($inspectionDateList);

    for ($i = 0; $i < $rowNum; ++$i) {
      $curInspectionDate = $inspectionDateList[$i];

      if ($curInspectionDate["lot_id"] == $lotId) {
        $inspectionDate = new StdClass();
        $inspectionDate->inspection_id = $curInspectionDate["inspection_id"];
        $inspectionDate->day = $curInspectionDate["inspection_day"];
        $inspectionDate->start_time = $curInspectionDate["inspection_start_time"];
        $inspectionDate->end_time = $curInspectionDate["inspection_end_time"];
        $inspectionDate->typhoon_start_time = $curInspectionDate["typhoon_start_time"];
        $inspectionDate->typhoon_end_time = $curInspectionDate["typhoon_end_time"];

        $output[] = $inspectionDate;
      }
    }

    return $output;
  }

  private function getKeywordPhotoAuthor($keywordEn, $keywordTc) {
    if (empty($keywordEn) && empty($keywordTc)) {
      return new KeywordPhotoAuthor("", "", "");
    }

    global $conn;

    $selectSql = "SELECT image_url, author, author_url FROM KeywordImage WHERE keyword_en LIKE ? OR keyword_tc LIKE ?";

    $result = $conn->Execute($selectSql, array("%".$keywordEn."%", "%".$keywordTc."%"))->GetRows();
    $rowNum = count($result);

    if ($rowNum == 0) {
      return new KeywordPhotoAuthor("", "", "");
    }

    $randIndex = rand(0, $rowNum - 1);
    return new KeywordPhotoAuthor($result[$randIndex]["image_url"], $result[$randIndex]["author"], $result[$randIndex]["author_url"]);
  }

  function listKeywordImage($param) {
    global $conn;

    $page = 1;
    $pageSize = 50;
    $keyword = "";
    if (isset($param) && is_array($param)) {
      if (count($param) >= 1) {
        $page = intval($param[0]);
      }
      if (count($param) >= 2) {
        $keyword = trim($param[1]);
      }
    }

    $selectSql = "SELECT
                    keyword_image_id, keyword_en, keyword_tc, image_url, author, author_url
                  FROM KeywordImage
                  WHERE ? = '' OR keyword_en LIKE ? OR keyword_tc LIKE ?
                  LIMIT ?, ?";

    $result = $conn->Execute($selectSql, array(
      $keyword, "%".$keyword."%", "%".$keyword."%", ($page - 1) * $pageSize, $pageSize
    ))->GetRows();
    $rowNum = count($result);

    $output = array();
    for($i = 0; $i < $rowNum; ++$i) {
      $keywordImage = new stdClass();
      $keywordImage->id = $result[$i]["keyword_image_id"];
      $keywordImage->keyword_en = $result[$i]["keyword_en"];
      $keywordImage->keyword_tc = $result[$i]["keyword_tc"];
      $keywordImage->image_url = $result[$i]["image_url"];
      $keywordImage->author = $result[$i]["author"];
      $keywordImage->author_url = $result[$i]["author_url"];
      
      $output[] = $keywordImage;
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function listLotIcon($param) {
    global $conn;

    // $param: <auction id>-<show all>-<include feature>-<page no.>-<keyword>-<match 1st item>
    // e.g.
    // 0-Y-N-12
    // 3-N-Y-2-車-Y
    // 7-Y-Y-1-car-N
    $auctionId = 0;
    $showAll = false;
    $includeFeatured = false;
    $keyword = "";
    $match1stItem = false;
    $page = 1;
    $pageSize = 50;

    if (isset($param) && is_array($param)) {
      $auctionId = intval($param[0]);
      $showAll = strtoupper($param[1]) == "Y";
      $includeFeatured = strtoupper($param[2]) == "Y";
      
      if (ctype_digit($param[3])) {
        $page = intval($param[3]);  
      }
      if (count($param) >= 5) {
        $keyword = trim($param[4]);
        $match1stItem = (count($param) >= 6 ? strtoupper($param[5]) == "Y" : false);
      }
    }

    $likeKeyword = $match1stItem ? $keyword."%" : "%".$keyword."%";
    $selectSql = "SELECT
                    A.start_time, L.lot_id, L.lot_num, L.featured, L.icon, L.description_en, L.description_tc, A.status as 'auction_status', L.status
                  FROM Auction A
                  INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                  WHERE (A.auction_id = ? OR ? = 0) AND (L.icon = '' OR L.icon = 'fontawesome.box' OR ? = 1) AND (featured = 0 OR ? = 1) AND 
                        (L.description_en LIKE ? OR L.description_tc LIKE ?)
                  ORDER BY A.auction_id DESC, L.lot_num
                  LIMIT ?, ?";

    $result = $conn->Execute($selectSql, array(
      $auctionId, $auctionId, $showAll ? 1 : 0, $includeFeatured ? 1 : 0, $likeKeyword, $likeKeyword, ($page-1)*$pageSize, $pageSize
    ))->GetRows();
    $rowNum = count($result);

    $lotList = array();
    for($i = 0; $i < $rowNum; ++$i) {
      $lot = new stdClass();
      $lot->start_time = $result[$i]["start_time"];
      $lot->lot_id = $result[$i]["lot_id"];
      $lot->lot_num = $result[$i]["lot_num"];
      $lot->featured = $result[$i]["featured"];
      $lot->icon = $result[$i]["icon"];
      $lot->description_en = $result[$i]["description_en"];
      $lot->description_tc = $result[$i]["description_tc"];
      $lot->auction_status = $result[$i]["auction_status"];
      $lot->status = $result[$i]["status"];
      
      $lotList[] = $lot;
    }

    // using count(*) again is faster and more compatible than SQL_CALC_FOUND_ROWS() and FOUND_ROWS()
    $selectSql = "SELECT COUNT(*) as 'total'
                  FROM Auction A
                  INNER JOIN AuctionLot L ON A.auction_id = L.auction_id
                  WHERE (A.auction_id = ? OR ? = 0) AND (L.icon = '' OR L.icon = 'fontawesome.box' OR ? = 1) AND (featured = 0 OR ? = 1) AND 
                        (L.description_en LIKE ? OR L.description_tc LIKE ?)";
    $result = $conn->Execute($selectSql, array(
      $auctionId, $auctionId, $showAll ? 1 : 0, $includeFeatured ? 1 : 0, "%".$keyword."%", "%".$keyword."%")
    )->GetRows();
    $total = $result[0]["total"];

    $output = new StdClass();
    $output->lot_list = $lotList;
    $output->total = $total;

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function getKeywordImageUrl($param) {
    global $conn;

    $keywordEn = "";
    $keywordTc = "";
    if (isset($param) && is_array($param)) {
      $keywordEn = count($param) >= 1 ? trim($param[0]) : "";
      $keywordTc = count($param) >= 2 ? trim($param[1]) : "";
    }
    
    $output = array();
    if (!empty($keywordEn) || !empty($keywordTc)) {
      $selectSql = "SELECT image_url, author, author_url FROM KeywordImage WHERE keyword_en LIKE ? OR keyword_tc LIKE ?";

      $result = $conn->Execute($selectSql, array("%".$keywordEn."%", "%".$keywordTc."%"))->GetRows();
      $rowNum = count($result);

      for($i = 0; $i < $rowNum; ++$i) {
        $imageKeyword = new StdClass();
        $imageKeyword->image_url = $result[$i]["image_url"];
        $imageKeyword->author = $result[$i]["author"];
        $imageKeyword->author_url = $result[$i]["author_url"];

        $output[] = $imageKeyword;
      }
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function createKeywordImage() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

    try {
      if (!isset($_POST["image_url"]) && !isset($_POST["image_file"])) {
        throw new Exception("Image missing!");  
      }

      $keywordEn = empty(trim($_POST["keyword_en"])) ? "-" : trim($_POST["keyword_en"]);
      $keywordTc = empty(trim($_POST["keyword_tc"])) ? "-" : trim($_POST["keyword_tc"]);
      $imageUrl = trim($_POST["image_url"]);
      $author = trim($_POST["author"]);
      $authorUrl = trim($_POST["author_url"]);
      $md5FileName = "";

      if (!empty($_FILES["image_file"])) {
        $uploadFileName = $_FILES["image_file"]["name"];
        $ext = substr($uploadFileName, strrpos($uploadFileName, ".") + 1);
        $md5FileName = md5(basename($uploadFileName, $ext)."_".time()) . "." . $ext;
        $targetFile = $GLOBALS["AUCTION_IMAGE_FOLDER"] . $md5FileName;

        if (getimagesize($_FILES["image_file"]["tmp_name"]) === false) {
          throw new Exception("Image file not found!");
        }

        try {
          while (file_exists($targetFile)) {
            usleep(100);
            $md5FileName = md5(basename($uploadFileName, $ext)."_".time()) . "." . $ext;
            $targetFile = $GLOBALS["AUCTION_IMAGE_FOLDER"] . $md5FileName;
          }
          move_uploaded_file($_FILES["image_file"]["tmp_name"], $targetFile);
        } catch (Exception $e) {
          throw $e;
        }
      }

      $insertSql = "INSERT INTO KeywordImage (keyword_en, keyword_tc, image_url, author, author_url) 
                    VALUES (?, ?, ?, ?, ?)";
      
      $result = $conn->Execute($insertSql, array($keywordEn, $keywordTc, empty($md5FileName) ? $imageUrl : $md5FileName, $author, $authorUrl));

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
      $selectSql = "SELECT image_url FROM KeywordImage WHERE keyword_image_id = ?";
      $result = $conn->Execute($selectSql, array($id))->GetRows();
      if (count($result) < 1) {
        throw new Exception("keyword image id: $id not found!");
      } 

      if (!empty(trim($result[0]["image_url"])) && strpos($result[0]["image_url"], "http://") === false && strpos($result[0]["image_url"], "https://") === false) {
        $filePath = $GLOBALS["AUCTION_IMAGE_FOLDER"] . $result[0]["image_url"];

        if (file_exists($filePath)) {
          unlink($filePath);
        }
      }

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
    $selectSql = "SELECT min_app_version, data_version, news_en, news_tc, news_sc, last_update FROM AppInfo ORDER BY id DESC LIMIT 1";

    $result = $conn->Execute($selectSql)->GetRows();
    $rowNum = count($result);

    if (count($result) > 0) {
      $output->min_app_version = $result[0]["min_app_version"];
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

      if (!isset($data["min_app_version"]) || empty($data["min_app_version"])) {
        throw new Exception("Min App Version missing!");  
      }

      if (!isset($data["data_version"]) || empty($data["data_version"])) {
        throw new Exception("Data Version missing!");  
      }

      $minAppVersion = trim($data["min_app_version"]);
      $dataVersion = trim($data["data_version"]);
      $newsEn = $data["news_en"];
      $newsTc = $data["news_tc"];
      $newsSc = $data["news_sc"];

      $updateSql = "UPDATE AppInfo SET 
                      min_app_version = ?,  data_version = ?, news_en = ?, news_tc = ?, news_sc = ?, last_update = now()
                    WHERE id = 1";

      $result = $conn->Execute($updateSql, array(
        $minAppVersion, $dataVersion, $newsEn, $newsTc, $newsSc
      ));
  
        $output->status = "success";
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function deleteInspectionDate() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["inspection_id"]) || empty($data["inspection_id"])) {
        throw new Exception("Inspection ID missing!");  
      }

      $inspectionId = intval($data["inspection_id"]);
      $deleteSql = "DELETE FROM InspectionDate WHERE inspection_id = ?";
      $conn->Execute($deleteSql, array($inspectionId));
  
      $output->status = "success";
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function addInspectionDate() {
    global $conn;

    $output = new stdClass();
    $output->status = "fail";

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data["lot_id"]) || empty($data["lot_id"])) {
        throw new Exception("Lot ID missing!");  
      }

      if (!isset($data["day"]) || empty($data["day"])) {
        throw new Exception("Day of week missing!");  
      }

      $lotId = intval($data["lot_id"]);
      $day = intval($data["day"]);
      $startTime = substr(trim($data["start_time"]), 0, 5);
      $endTime = substr(trim($data["end_time"]), 0, 5);
      $typhoonStartTime = substr(trim($data["typhoon_start_time"]), 0, 5);
      $typhoonEndTime = substr(trim($data["typhoon_end_time"]), 0, 5);

      $insertSql = "INSERT INTO InspectionDate (lot_id, inspection_day, inspection_start_time, inspection_end_time, typhoon_start_time, typhoon_end_time) VALUES (?, ?, ?, ?, ?, ?)";
      $conn->Execute($insertSql, array($lotId, $day, $startTime, $endTime, $typhoonStartTime, $typhoonEndTime));
  
      $output->status = "success";
    } catch (Exception $e) {
      $output->status = "error";
      $output->error = $e->getMessage();
    }
    
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  function Decrypt() {
    echo str_replace("\xEF\xBB\xBF",'', Base64Aes256Decrypt($_POST["encrypted_text"], $_POST["secret"]));
  }
}

class KeywordPhotoAuthor {
  public $photoUrl;
  public $author;
  public $authorUrl;

  public function __construct($photoUrl, $author, $authorUrl) {
    $this->photoUrl = $photoUrl;
    $this->author = $author;
    $this->authorUrl = $authorUrl;
  }
}
?>