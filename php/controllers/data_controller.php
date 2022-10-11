<?php
include_once ("../include/appdata.php");

class DataController {
  function appInfo() {
    global $conn, $lang;
    $_APP = AppData::getInstance();

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

      $selectSql = "SELECT push_id, title_$lang as 'title', body_$lang as 'body', push_date
                    FROM PushHistory
                    WHERE status = ? AND push_date > ?
                    ORDER BY push_date DESC";
      $startDate = FormatMysqlDateTime(date_sub(GetCurrentLocalTime(), new DateInterval("P".$GLOBALS["PUSH_MESSAGE_DAYS"]."D")));

      $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array(PushStatus::Sent, $startDate))->GetRows();
      $rowNum = count($result);

      $data->ml = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $push = new StdClass();
        $push->id = intval($result[$i]["push_id"]);
        $push->t = $result[$i]["title"];
        $push->b = $result[$i]["body"];
        $push->d = date("Y-m-d H:i:s", strtotime($result[$i]["push_date"]));

        $data->ml[] = $push;
      }

      // item type list, from $_APP
      $data->itm = new StdClass();
      foreach ($_APP->auctionItemTypeList as $auctionItemType) {
        $data->itm->{$auctionItemType->code} = $auctionItemType->description($lang);
      }

      $output->status = "success";
      $output->data = $data;
    } catch (Exception $e) {
      $output->status = "error";
      // $output->message = $e->getMessage();
    }

    echo json_change_key(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
  }

  function messageList() {
    // quick api to return the list of available auctions
    global $conn, $lang;

    $output = new StdClass();
    $output->status = "fail";

    try {
      $startDate = FormatMysqlDateTime(date_sub(GetCurrentLocalTime(), new DateInterval("P".$GLOBALS["PUSH_MESSAGE_DAYS"]."D")));

      $selectSql = "SELECT push_id, title_$lang as 'title', body_$lang as 'body', push_date
                    FROM PushHistory
                    WHERE status = ? AND push_date > ?
                    ORDER BY push_date DESC";

      $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array(PushStatus::Sent, $startDate))->GetRows();
      $rowNum = count($result);

      $data = array();
      for($i = 0; $i < $rowNum; ++$i) {
        $push = new StdClass();
        $push->id = intval($result[$i]["push_id"]);
        $push->t = $result[$i]["title"];
        $push->b = $result[$i]["body"];
        $push->d = date("Y-m-d H:i:s", strtotime($result[$i]["push_date"]));

        $data[] = $push;
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