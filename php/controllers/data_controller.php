<?php
class DataController {
  function getAppInfo() {
    global $conn, $lang;

    $output = new stdClass();
    $selectSql = "SELECT data_version, news_$lang as news FROM AppInfo ORDER BY id DESC LIMIT 1";

    $result = $conn->Execute($selectSql)->GetRows();  // simple query, no need to cache
    $rowNum = count($result);

    if (count($result) > 0) {
      $output->dv = $result[0]["data_version"];
      $output->n = $result[0]["news"];
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
}
?>