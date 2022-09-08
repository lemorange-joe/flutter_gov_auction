<?php
include_once("../include/config.php");
include_once("../include/common.php");
include_once("../include/google_api/vendor/autoload.php");

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
    $result = false;

    //testing device token
    $deviceToken = "dq8KCvTETvardhKd5OadQ7:APA91bEH5lZ63E_UAsFHjG6gFWF8rob4-XGcxmXkF5vtKExrRBuisLKB36SMC4NwFUboOiRBTI6J9_uHuwIvyNXBLw-UysH0i9VI27Cju1rsuJ1pV9NX3aJhWbbN1ABNi-eisxs7Dpje";
    $accessToken = $this->getGoogleAccessToken();
    // Debug_var_dump($accessToken);
    try {
      $headers = array(
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
      );
    
      $notification_tray = array(
        "title" => $pushData->titleTc,
        "body" => $pushData->bodyTc
      );
    
      // $data = array(
      //   "via" => "admin2",
      //   "count" => $pushId
      // );
      //The $in_app_module array above can be empty - I use this to send variables in to my app when it is opened, so the user sees a popup module with the message additional to the generic task tray notification.
    
      $message = array(
        "message" => array(
          "token" => $deviceToken,
          "notification" => $notification_tray,
        ),
      );
      // 'data'             => $data,
    
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $GLOBALS["FCM_ENDPOINT"]);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
    
      $curlResult = curl_exec($ch);
      // Debug_var_dump($curlResult);
    
      if ($curlResult === FALSE) {
          throw Exception("Curl failed: " . curl_error($ch));
      }
    
      curl_close($ch);
      $result = true;
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