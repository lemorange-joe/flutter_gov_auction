<?php
include_once("../include/config.php");
include_once("../include/common.php");
include_once("../include/google_api/vendor/autoload.php");
include_once("../class/push_result.php");

class PushManager {
  private String $key;

  function __construct() {
  }

  // legacy HTTP rest
  // public function send($pushId, $pushData) {
  //   $result = false;
    
  //   try {
  //     $fcmUrl = "https://fcm.googleapis.com/fcm/send";
  //     $tokens = array(
  //       "dq8KCvTETvardhKd5OadQ7:APA91bEH5lZ63E_UAsFHjG6gFWF8rob4-XGcxmXkF5vtKExrRBuisLKB36SMC4NwFUboOiRBTI6J9_uHuwIvyNXBLw-UysH0i9VI27Cju1rsuJ1pV9NX3aJhWbbN1ABNi-eisxs7Dpje"
  //     );
  //     $apiKey = "AAAARKBn9NM:APA91bEccFN76-gqOKFx9jrhVEgpglE632fPLZ0ESTpFUItpsuFPehijvQnULkqA91lCd3lR31Jlzogya2nQA2OIaw2dALwW3FDyAYa-0TQMImwT5DWrImT54CxWCrgErBGwqeHSz9N_";
    
  //     $notification = array(
  //       "title" => $pushData->titleTc,
  //       "body" => $pushData->bodyTc
  //     );
  //     $data = array(
  //       "via" => "admin",
  //       "count" => $pushId
  //     );

  //     $fcmNotification = array(
  //       "registration_ids" => $tokens,
  //       "notification" => $notification,
  //       "data" => $data
  //     );

  //     $headers = [
  //       "Authorization: key=" . $apiKey,
  //       "Content-Type: application/json"
  //     ];

  //     $ch = curl_init();
  //     curl_setopt($ch, CURLOPT_URL,$fcmUrl);
  //     curl_setopt($ch, CURLOPT_POST, true);
  //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
  //     $curlResult = curl_exec($ch);
  //     // Debug_var_dump($result);
      
  //     curl_close($ch);      
  //   } catch (Exception $e) {}

  //   return $result;
  // }
  public function send($pushId, $pushData) {
    $pushResult = new PushResult();

    $accessToken = $this->getGoogleAccessToken();
    
    $pushResult->resultEn = $this->sendTopic("news_en", $pushData->titleEn, $pushData->bodyEn, $accessToken);
    $pushResult->successEn = strpos(strtolower($resultEn), "error") === false;
    $pushResult->sentEn = date("Y-m-d H:i:s");
    sleep(1);
    $pushResult->resultTc = $this->sendTopic("news_tc", $pushData->titleTc, $pushData->bodyTc, $accessToken);
    $pushResult->successTc = strpos(strtolower($resultTc), "error") === false;
    $pushResult->sentTc = date("Y-m-d H:i:s");
    sleep(1);
    $pushResult->resultSc = $this->sendTopic("news_sc", $pushData->titleSc, $pushData->bodySc, $accessToken);
    $pushResult->successSc = strpos(strtolower($resultSc), "error") === false;
    $pushResult->sentSc = date("Y-m-d H:i:s");
    
    return $pushResult;
  }

  public function resend($lang, $title, $body) {
    //TBC!!!
  }

  private function sendTopic($topic, $title, $body, $accessToken) {
    $result = "";
    
    try {
      $headers = array(
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
      );
    
      $notification = array(
        "title" => $title,
        "body" => $body
      );

      $message = array(
        "message" => array(
          "topic" => $topic,
          "notification" => $notification,
        ),
      );
    
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $GLOBALS["FCM_ENDPOINT"]);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
    
      $result = curl_exec($ch);
      // Debug_var_dump($result);
    
      if ($result === FALSE) {
          throw Exception("Curl failed: " . curl_error($ch));
      }
    
      curl_close($ch);
    } catch (Exception $e) {}
   
    return $result;
   }

  private function getGoogleAccessToken(){
    $credentialsFilePath = $GLOBALS["GOOGLE_SERVICE_ACCOUNT_FILE_PATH"];
    $client = new Google_Client();
    $client->setAuthConfig($credentialsFilePath);
    $client->addScope("https://www.googleapis.com/auth/firebase.messaging");
    $client->refreshTokenWithAssertion();
    $token = $client->getAccessToken();

    return $token["access_token"];
  }
}

class PushData {
  private $titleEn;
  private $titleTc;
  private $titleSc;
  private $bodyEn;
  private $bodyTc;
  private $bodySc;

  function __construct($titleEn, $titleTc, $titleSc, $bodyEn, $bodyTc, $bodySc) {
    $this->titleEn = $titleEn;
    $this->titleTc = $titleTc;
    $this->titleSc = $titleSc;
    $this->bodyEn = $bodyEn;
    $this->bodyTc = $bodyTc;
    $this->bodySc = $bodySc;
  }

  public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
?>