<?php
class Auction implements JsonSerializable{
  private $id;
  private $num;
  private $startTime;
  private $location;
  private $itemPdfList;
  private $resultPdfList;
  private $lotList;
  private $auctionStatus;
  private $version;
  private $status;
  private $lastUpdate;

  public function __construct($id, $num, $startTime, $itemPdfEn, $itemPdfTc, $itemPdfSc, $resultPdfEn, $resultPdfTc, $resultPdfSc, $auctionStatus, $version, $status, $lastUpdate) {
    $this->id = $id;
    $this->num = $num;
    $this->startTime = $startTime;
    $this->location = array(
      "en" => "",
      "tc" => "",
      "sc" => "",
    );
    $this->itemPdfList = array();
    $this->resultPdfList = array(
      "en" => $resultPdfEn,
      "tc" => $resultPdfTc,
      "sc" => $resultPdfSc,
    );
    $this->lotList = array();
    $this->auctionStatus = $auctionStatus;
    $this->version = $version;
    $this->status = $status;
    $this->lastUpdate = $lastUpdate;
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

  public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return $vars;
    }
}

class AuctionLot implements JsonSerializable{
  private $id;
  private $type;
  private $lotNum;
  private $icon;
  private $photoUrl;
  private $photoReal;
  private $itemList;
  private $transactionCurrency;
  private $transactionPrice;
  private $transactionStatus;
  private $status;
  private $lastUpdate;

  public function __construct($id, $type, $lotNum, $icon, $photoUrl, $photoReal, $transactionCurrency, $transactionPrice, $transactionStatus, $lastUpdate) {
    $this->id = $id;
    $this->type = $type;
    $this->lotNum = $lotNum;
    $this->icon = $icon;
    $this->photoUrl = $photoUrl;
    $this->photoReal = $photoReal;
    $this->itemList = array();
    $this->transactionCurrency = $transactionCurrency;
    $this->transactionPrice = $transactionPrice;
    $this->transactionStatus = $transactionStatus;
    $this->lastUpdate = $lastUpdate;
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

  public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return $vars;
    }
}

class AuctionItem implements JsonSerializable{
  private $id;
  private $icon;
  private $description;
  private $quantity;
  private $unit;

  public function __construct($id, $icon, $descriptionEn, $descriptionTc, $descriptionSc, $quantity, $unitEn, $unitTc, $unitSc) {
    $this->id = $id;
    $this->icon = $icon;
    $this->description = array(
      "en" => $descriptionEn,
      "tc" => $descriptionTc,
      "sc" => $descriptionSc,
    );
    $this->quantity = $quantity;
    $this->unit = array(
      "en" => $unitEn,
      "tc" => $unitTc,
      "sc" => $unitSc,
    );
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

  public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return $vars;
    }
}
?>