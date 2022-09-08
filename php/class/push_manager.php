<?php
include_once("../include/config.php");
include_once("../include/common.php");

class PushManager {
  private String $key;

  function __construct() {

  }

  public function send($pushId, $pushData) {
    $result = false;
    
    try {
      $fcmUrl = "https://fcm.googleapis.com/fcm/send";
      $tokens = array(
        "dq8KCvTETvardhKd5OadQ7:APA91bEH5lZ63E_UAsFHjG6gFWF8rob4-XGcxmXkF5vtKExrRBuisLKB36SMC4NwFUboOiRBTI6J9_uHuwIvyNXBLw-UysH0i9VI27Cju1rsuJ1pV9NX3aJhWbbN1ABNi-eisxs7Dpje"
      );
      $apiKey = "AAAARKBn9NM:APA91bEccFN76-gqOKFx9jrhVEgpglE632fPLZ0ESTpFUItpsuFPehijvQnULkqA91lCd3lR31Jlzogya2nQA2OIaw2dALwW3FDyAYa-0TQMImwT5DWrImT54CxWCrgErBGwqeHSz9N_";
    
      $notification = array(
        "title" => $pushData->titleTc,
        "body" => $pushData->bodyTc
      );
      $data = array(
        "via" => "admin",
        "count" => $pushId
      );

      $fcmNotification = array(
        "registration_ids" => $tokens,
        "notification" => $notification,
        "data" => $data
      );

      $headers = [
        "Authorization: key=" . $apiKey,
        "Content-Type: application/json"
      ];

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$fcmUrl);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
      $result = curl_exec($ch);
      // Debug_var_dump($result);
      
      curl_close($ch);      
    } catch (Exception $e) {}

    return $result;
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