<?php
class Auction implements JsonSerializable{
  private $id;
  private $num;
  private $startTime;
  private $location;
  private $auctionPdf;
  private $resultPdf;
  private $itemPdfList;
  private $lotList;
  private $auctionStatus;
  private $version;
  private $status;
  private $lastUpdate;

  public function __construct($id, $num, $startTime, $location, $auctionPdf, $resultPdf, $auctionStatus, $version, $status, $lastUpdate) {
    $this->id = $id;
    $this->num = $num;
    $this->startTime = $startTime;
    $this->location = $location;
    $this->auctionPdf = $auctionPdf;
    $this->resultPdf = $resultPdf;
    $this->itemPdfList = array();
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
  private $tc;    //transactionCurrency
  private $tp;    //transactionPrice
  private $ts;    //transactionStatus
  private $status;
  private $lastUpdate;
  private $v;

  public function __construct($id, $type, $lotNum, $icon, $photoUrl, $photoReal, $transactionCurrency, $transactionPrice, $transactionStatus, $lastUpdate, $v) {
    $this->id = $id;
    $this->type = $type;
    $this->lotNum = $lotNum;
    $this->icon = $icon;
    $this->photoUrl = $photoUrl;
    $this->photoReal = $photoReal;
    $this->itemList = array();
    $this->tc = $transactionCurrency;
    $this->tp = $transactionPrice;
    $this->ts = $transactionStatus;
    $this->lastUpdate = $lastUpdate;
    $this->v = $v;
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
  private $ic;  //icon
  private $d;   //description
  private $q;   //quantity
  private $u;   //unit
  private $v;

  public function __construct($id, $icon, $description, $quantity, $unit, $v) {
    $this->id = $id;
    $this->ic = $icon;
    $this->d = $description;
    $this->q = $quantity;
    $this->u = $unit;
    $this->v = $v;
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

class RelatedAuctionLot implements JsonSerializable{
  private $auctionId;
  private $lotId;
  private $startTime;
  private $itemPdfList;
  private $resultPdfList;
  private $auctionStatus;
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
  private $v;

  public function __construct($icon, $descriptionEn, $descriptionTc, $descriptionSc, $quantity, $unitEn, $unitTc, $unitSc, $v) {
    $this->icon = $icon;
    $this->description;
    $this->quantity = $quantity;
    $this->unit;
    $this->v = $v;
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