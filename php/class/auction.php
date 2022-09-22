<?php
$auctionJsonFieldMapping = array(
  // API level
  "status" => "s",
  "data" => "d",
  "message" => "m",
  // Auction
  "num" => "n",
  "startTime" => "st",
  "location" => "l",
  "auctionPdf" => "ap",
  "resultPdf" => "rp",
  "itemPdfList" => "ipl",
  "remarks" => "r",
  "lotList" => "ll",
  "auctionStatus" => "as",
  // "status" => "s",
  "lastUpdate" => "lu",
  // ------
  // AuctionLot
  "type" => "t",
  "lotNum" => "ln",
  "gldFileRef" => "gr",
  "reference" => "r",
  "department" => "dp",
  "contact" => "co",
  "contactNumber" => "cn",
  "contactLocation" => "cl",
  // "remarks" => "r",
  "itemCondition" => "ic",
  "featured" => "f",
  "icon" => "i",
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
  // "icon" => "i",
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
  private $remarks;
  private $itemPdfList;
  private $lotList;
  private $auctionStatus;
  private $status;
  private $lastUpdate;

  public function __construct($id, $num, $startTime, $location, $auctionPdf, $resultPdf, $remarks, $auctionStatus, $status, $lastUpdate) {
    $this->id = $id;
    $this->num = $num;
    $this->startTime = $startTime;
    $this->location = $location;
    $this->auctionPdf = $auctionPdf;
    $this->resultPdf = $resultPdf;
    $this->remarks = $remarks;
    $this->itemPdfList = array();
    $this->lotList = array();
    $this->auctionStatus = $auctionStatus;
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
  private $gldFileRef;
  private $reference;
  private $department;
  private $contact;
  private $contactNumber;
  private $contactLocation;
  private $remarks;
  private $itemCondition;

  private $featured;
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

  public function __construct($id, $type, $lotNum, $gldFileRef, $reference, $department, $contact, $number, $location, $remarks, $itemCondition, 
                              $featured, $icon, $photoUrl, $photoReal, $tranCurrency, $tranPrice, $tranStatus, $status, $lastUpdate, $v) {
    $this->id = $id;
    $this->type = $type;
    $this->lotNum = $lotNum;
    $this->gldFileRef = $gldFileRef;
    $this->reference = $reference;
    $this->department = $department;
    $this->contact = $contact;
    $this->contactNumber = $number;
    $this->contactLocation = $location;
    $this->remarks = $remarks;
    $this->itemCondition = $itemCondition;

    $this->featured = $featured;
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
  private $featured;
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

  public function __construct($auction_id, $start_time, $auction_status, $lot_id, $type, $featured, $photoUrl, $photoReal, $tranCurrency, $tranPrice, $tranStatus, $icon, $description, $quantity, $unit, $v) {
    $this->auctionId = $auction_id;
    $this->startTime = $start_time;
    $this->auctionStatus = $auction_status;
    $this->lotId = $lot_id;
    $this->type = $type;
    $this->featured = $featured;
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