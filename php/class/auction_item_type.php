<?php
class AuctionItemType {
  private $code;
  private $descriptionEn;
  private $descriptionTc;
  private $descriptionSc;

  public function __construct($code, $descriptionEn, $descriptionTc, $descriptionSc) {
    $this->code = $code;
    $this->descriptionEn = $descriptionEn;
    $this->descriptionTc = $descriptionTc;
    $this->descriptionSc = $descriptionSc;
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

  public function description($lang) {
    if (strtolower($lang) == "tc") return $this->descriptionTc;
    if (strtolower($lang) == "sc") return $this->descriptionSc;
    return $this->descriptionEn;
  }
}
?>
