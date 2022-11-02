<?php
include_once ("../include/appdata.php");

class DataController {
  function appInfo() {
    global $conn, $lang;
    $_APP = AppData::getInstance();

    $output = new StdClass();
    $output->status = "fail";
    $clientVersion = "0";

    if (!isset($_POST["version"]) || empty($_POST["version"])) {
      $output->message = "version is empty";
    } else {
      $clientVersion = trim($_POST["version"]);
    }

    try {
      $data = new StdClass();
      $selectSql = "SELECT min_app_version, data_version, news_$lang as news, last_update FROM AppInfo ORDER BY id DESC LIMIT 1";

      $result = $conn->Execute($selectSql)->GetRows();  // simple query, no need to cache
      $rowNum = count($result);

      if (CommonCompareVersion($clientVersion, $result[0]["min_app_version"]) == -1) {
        $data->fu = "Y";  // force upgrade
      } else {
        if (count($result) > 0) {
          $data->dv = $result[0]["data_version"];
          $data->n = $result[0]["news"];
          $data->lu = date("Y-m-d H:i:s", strtotime($result[0]["last_update"]));
        }

        $selectSql = "SELECT title_$lang as 'title', url_$lang as 'url' FROM NoticeLink WHERE status = ? ORDER BY seq";
        $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array(Status::Active))->GetRows();
        $rowNum = count($result);

        $data->nll = array(); // notice links list
        for($i = 0; $i < $rowNum; ++$i) {
          $noticeLink = new StdClass();
          $noticeLink->t = $result[$i]["title"];
          $noticeLink->u = $result[$i]["url"];

          $data->nll[] = $noticeLink;
        }

        $selectSql = "SELECT push_id, title_$lang as 'title', body_$lang as 'body', push_date
                      FROM PushHistory
                      WHERE status = ? AND push_date > ?
                      ORDER BY push_date DESC";
        $startDate = FormatMysqlDateTime(date_sub(GetCurrentLocalTime(), new DateInterval("P".$GLOBALS["PUSH_MESSAGE_DAYS"]."D")));

        $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql, array(PushStatus::Sent, $startDate))->GetRows();
        $rowNum = count($result);

        $data->ml = array();  // push message list
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

  function getDeveloperId() {
    $output = new StdClass();
    $output->s = "fail";

    if (!$GLOBALS["ENABLE_DEVELOPER"] || !isset($_POST["keyword"])) {
      echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return;
    }
    
    $data = new StdClass();
    $base64Keyword = base64_encode($_POST["keyword"]);
    if ($base64Keyword == "6Kyd6Kqe5b+D" || $base64Keyword == "6Kyd6Ku+54S2") {
      $data->dk = $GLOBALS["DEVELOPER_GAUC_ID"];
    } else {
      $data->dk = "x9LvKM6J80B6qIzOEhdhW8vw";  // just return a fake key
    }

    $output->s = "success";
    $output->d = $data;
    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
}
?>