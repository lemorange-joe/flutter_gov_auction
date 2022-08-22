<?php
include_once('../include/config.php');

class PushManager {
  private String $key;

  function __construct() {

  }

  public function send($pushData) {
    $result = false;

    //send push logic TBC!!!
    sleep(3);
    $result = true;

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