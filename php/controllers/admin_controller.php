<?php
include_once ('../include/enum.php');

class AdminController {
  function listAuction() {
    global $conn, $lang;

    $selectSql = "SELECT
                    auction_id, auction_num, start_time, auction_pdf_en, auction_pdf_tc, auction_pdf_sc,
                    result_pdf_en, result_pdf_tc, result_pdf_sc, auction_status, status, last_update 
                  FROM Auction
                  ORDER BY auction_id DESC";

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



      $output[] = $auction;
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

  function importLotItems($lotId, $lots) {
    global $conn;

    for ($i = 0; $i < Count($lots); ++$i) {
      $curLot = $lots[$i];

      $descriptionEn = trim($curLot["description_en"]);
      $descriptionTc = trim($curLot["description_tc"]);
      $descriptionSc = str_chinese_simp(trim($curLot["description_tc"]));
      $quantity = trim($curLot["quantity"]);
      $unitEn = trim($curLot["unit_en"]);
      $unitTc = trim($curLot["unit_tc"]);
      $unitSc = str_chinese_simp(trim($curLot["unit_tc"]));
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
}
?>