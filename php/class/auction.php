<?php
$auctionJsonFieldMapping = array(
  // Auction
  "num" => "n",
  "startTime" => "st",
  "location" => "l",
  "auctionPdf" => "ap",
  "resultPdf" => "rp",
  "itemPdfList" => "ipl",
  "lotList" => "ll",
  "auctionStatus" => "as",
  "version" => "v",
  "status" => "s",
  "lastUpdate" => "lu",
  // ------
  // AuctionLot
  "type" => "t",
  "lotNum" => "ln",
  "icon" => "ic",
  "photoUrl" => "pu",
  "photoReal" => "pr",
  "itemList" => "il",
  "tranCurrency" => "tc",
  "tranPrice" => "tp",
  "tranStatus" => "ts",
  // "status" => "s",
  // "lastUpdate" => "lu",
  // ------
  // Auction Item
  // "icon" => "ic",
  "description" => "d",
  "quantity" => "q",
  "unit" => "u",
  // ------
  // Auction Search
  "auctionId" => "aid",
  "lotId" => "lid",
);


class Auction implements JsonSerializable {
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

  public function jsonSerialize() {
    $vars = get_object_vars($this);
    return $vars;
  }
}

class AuctionLot implements JsonSerializable {
  private $id;
  private $type;
  private $lotNum;
  private $icon;
  private $photoUrl;
  private $photoReal;
  private $itemList;
  private $tranCurrency;
  private $tranPrice;
  private $tranStatus;
  private $status;
  private $lastUpdate;
  private $v;

  public function __construct($id, $type, $lotNum, $icon, $photoUrl, $photoReal, $tranCurrency, $tranPrice, $tranStatus, $status, $lastUpdate, $v) {
    $this->id = $id;
    $this->type = $type;
    $this->lotNum = $lotNum;
    $this->icon = $icon;
    $this->photoUrl = $photoUrl;
    $this->photoReal = $photoReal;
    $this->itemList = array();
    $this->tranCurrency = $tranCurrency;
    $this->tranPrice = $tranPrice;
    $this->tranStatus = $tranStatus;
    $this->status = $status;
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

  public function jsonSerialize() {
    $vars = get_object_vars($this);
    return $vars;
  }
}

class AuctionItem implements JsonSerializable {
  private $id;
  private $icon;
  private $description;
  private $quantity;
  private $unit;
  private $v;

  public function __construct($id, $icon, $description, $quantity, $unit, $v) {
    $this->id = $id;
    $this->icon = $icon;
    $this->description = $description;
    $this->quantity = $quantity;
    $this->unit = $unit;
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

  public function jsonSerialize() {
    $vars = get_object_vars($this);
    return $vars;
  }
}

class AuctionSearch implements JsonSerializable {
  private $auctionId;
  private $startTime;
  private $auctionStatus;
  private $lotId;
  private $type;
  private $photoUrl;
  private $photoReal;
  private $tranCurrency;
  private $tranPrice;
  private $tranStatus;
  private $icon;
  private $description;
  private $quantity;
  private $unit;
  private $v;

  public function __construct($auction_id, $start_time, $auction_status, $lot_id, $type, $photoUrl, $photoReal, $tranCurrency, $tranPrice, $tranStatus, $icon, $description, $quantity, $unit, $v) {
    $this->auctionId = $auction_id;
    $this->startTime = $start_time;
    $this->auctionStatus = $auction_status;
    $this->lotId = $lot_id;
    $this->type = $type;
    $this->photoUrl = $photoUrl;
    $this->photoReal = $photoReal;
    $this->tranCurrency = $tranCurrency;
    $this->tranPrice = $tranPrice;
    $this->tranStatus = $tranStatus;
    $this->icon = $icon;
    $this->description = $description;
    $this->quantity = $quantity;
    $this->unit = $unit;
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

  public function jsonSerialize() {
    $vars = get_object_vars($this);
    return $vars;
  }
}
?>