<?php
class DataController {
  function appInfo() {
    global $conn, $lang;

    $output = new StdClass();
    $output->status = "fail";

    try {
      $data = new stdClass();
      $selectSql = "SELECT data_version, news_$lang as news, last_update FROM AppInfo ORDER BY id DESC LIMIT 1";

      $result = $conn->Execute($selectSql)->GetRows();  // simple query, no need to cache
      $rowNum = count($result);

      if (count($result) > 0) {
        $data->dv = $result[0]["data_version"];
        $data->n = $result[0]["news"];
        $data->lu = date("Y-m-d H:i:s", strtotime($result[0]["last_update"]));
      }

      $output->status = "success";
      $output->data = $data;
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }
}
?>