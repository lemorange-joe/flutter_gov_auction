<?php
include_once ("../include/appdata.php");
include_once ("../include/config.php");
include_once ("../include/common.php");

class DataController {
  public static $searchGridCategoryKeywords = array(
    "sold" => array("en" => "", "tc" => "", "sc" => "", "query" => array()),  // leave keywords empty for api to search for sold and flutter to display S.of(context).recentlySold 
    "mobile_phone" => array(
      "en" => "Mobile Phone", "tc" => "手提電話", "sc" => "手提电话", 
      "query" => array("%手提電話%")
    ),
    "used_car" => array(
      "en" => "Used Private Car", "tc" => "舊私家車", "sc" => "旧私家车",
      "query" => array("%舊私家車%")
    ),
    "television" => array(
      "en" => "Television", "tc" => "電視機", "sc" => "电视机",
      "query" => array("%電視機%")
    ),
    "air_conditioner" => array(
      "en" => "Air Conditioner", "tc" => "冷氣機", "sc" => "冷气机",
      "query" => array("%冷氣機%")
    ),
    "digital_camera" => array(
      "en" => "Digital Camera", "tc" => "數碼相機", "sc" => "数码相机",
      "query" => array("%數碼相機%")
    ),
    "refrigerator" => array(
      "en" => "Refrigerator", "tc" => "雪櫃", "sc" => "雪櫃",
      "query" => array("%雪櫃%")
    ),
    "bicycle" => array(
      "en" => "Bicycle", "tc" => "單車", "sc" => "单车",
      "query" => array("%單車%")
    ),
    "shredder" => array(
      "en" => "Shredder", "tc" => "碎紙機", "sc" => "碎纸机",
      "query" => array("%碎紙機%")
    ),
    "charger" => array(
      "en" => "Charger", "tc" => "充電器", "sc" => "充电器",
      "query" => array("%充電器%")
    ),
    "fan" => array(
      "en" => "Fan", "tc" => "風扇", "sc" => "风扇",
      "query" => array("%風扇%")
    ),
    "jewellery" => array(
      "en" => "Jewellery", "tc" => "珠寶首飾", "sc" => "珠宝首饰",
      "query" => array("%頸鏈%", "%耳環%", "%戒指%", "%手鐲%", "%鑽石%", "%玉石%")
    ),
  );

  // function testEncrypt() {
  //   $secret = GenRandomString($GLOBALS["AES_SECRET_LENGTH"]);
  //   echo Base64Aes256Encrypt($_GET["text"], $secret);
  //   echo "<br>";
  //   echo $secret;
  // }

  // function testDecrypt() {
  //   echo Base64Aes256Decrypt($_GET["text"], $_GET["secret"]);
  // }
  
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

        // include hot search keywords
        $selectSql = "SELECT keyword_$lang as 'keyword' FROM HotSearch";
        $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql)->GetRows();
        $rowNum = count($result);

        $data->hsl = array(); // hot search list
        for($i = 0; $i < $rowNum; ++$i) {
          $data->hsl[] = $result[$i]["keyword"];
        }

        // get catalog locations
        $selectSql = "SELECT address_$lang as 'address', map_address FROM Location WHERE is_catalog = 1 ORDER BY seq";
        $result = $conn->CacheExecute($GLOBALS["CACHE_PERIOD"], $selectSql)->GetRows();
        $rowNum = count($result);

        $data->cll = array(); // catalog location list
        for($i = 0; $i < $rowNum; ++$i) {
          $l = new StdClass();
          $l->a = $result[$i]["address"];
          $l->m = $result[$i]["map_address"];
          $data->cll[] = $l;
        }

        // grid category map
        $gridCategoryList = array();
        foreach(DataController::$searchGridCategoryKeywords as $category => $categoryKeyword) {
          $gridCategoryList[$category] = $categoryKeyword[$lang];
        }
        $data->gcm = $gridCategoryList;

        // item type list, from $_APP
        $data->itm = new StdClass();
        foreach ($_APP->auctionItemTypeList as $auctionItemType) {
          $data->itm->{$auctionItemType->code} = $auctionItemType->description($lang);
        }
      }

      $output->status = "success";
      if ($GLOBALS["ENCRYPT_API_DATA"]) {
        $secret = GenRandomString($GLOBALS["AES_SECRET_LENGTH"]);
        $strData = json_change_key(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        
        $output->data = Base64Aes256Encrypt($strData, $secret);
        $output->key = $secret;
      } else {
        $output->data = $data;
      }  
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
      if ($GLOBALS["ENCRYPT_API_DATA"]) {
        $secret = GenRandomString($GLOBALS["AES_SECRET_LENGTH"]);
        $strData = json_change_key(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $GLOBALS['auctionJsonFieldMapping']);
        
        $output->data = Base64Aes256Encrypt($strData, $secret);
        $output->key = $secret;
      } else {
        $output->data = $data;
      }  
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