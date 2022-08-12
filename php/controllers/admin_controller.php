<?php
include_once ('../include/enum.php');

class AdminController {
  function listAuction() {
    global $conn, $lang;

    $selectSql = "SELECT
                    auction_id, auction_num, start_time, auction_pdf_en, auction_pdf_tc, auction_pdf_sc,
                    result_pdf_en, result_pdf_tc, result_pdf_sc, auction_status, version, status, last_update 
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
      $auction->version = $result[$i]["version"];
      $auction->status = $result[$i]["status"];
      $auction->last_update = $result[$i]["last_update"];

      $output[] = $auction;
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE);
  }

  function updateAuctionStatus() {
  }
}
?>