<?php
class PushResult {
  private $resultEn;
  private $successEn;
  private $sentEn;
  private $resultTc;
  private $successTc;
  private $sentTc;
  private $resultSc;
  private $successSc;
  private $sentSc;

  function __construct() {
    $this->resultEn = "";
    $this->successEn = false;
    $this->sentEn = date("Y");
    $this->resultTc = "";
    $this->successTc = false;
    $this->sentTc = date("Y");
    $this->resultSc = "";
    $this->successSc = false;
    $this->sentSc = date("Y");
  }

  public function success() {
    return $this->successEn && $this->successTc && $this->successSc;
  }

  public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function __set($property, $value) {
  if (property_exists($this, $property)) {
      $this->$property = $value;
    }

    return $this;
  }
}
?>