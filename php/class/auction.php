<?php
$auctionJsonFieldMapping = array(
  // API level
  "status" => "s",
  "data" => "d",
  "message" => "m",
  "key" => "k",
  "requestStart" => "t",
  "elapsed" => "e",
  // Auction
  "num" => "n",
  "startTime" => "st",
  "collectionDeadline" => "cd",
  "location" => "l",
  "auctionPdf" => "ap",
  "resultPdf" => "rp",
  "itemPdfList" => "ipl",
  "remarks" => "r",
  "lotList" => "ll",
  "lotCount" => "lc",
  "transactionTotal" => "tt",
  "auctionStatus" => "as",
  // "status" => "s",
  "lastUpdate" => "lu",
  // ------
  // AuctionLot
  "type" => "t",
  "lotNum" => "ln",
  "gldFileRef" => "gr",
  "reference" => "rf",
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
  "photoAuthor" => "pa",
  "photoAuthorUrl" => "pau",
  "descriptionEn" => "den",
  "descriptionTc" => "dtc",
  "descriptionSc" => "dsc",
  "itemList" => "il",
  "specialInspection" => "si",
  "inspectionDateList" => "idl",
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
  // Inspection Date
  "dayOfWeek" => "dow",
  //"startTime" => "st",
  "endTime" => "et",
  // ------
  // Auction Search
  "auctionId" => "aid",
  "lotId" => "lid",
  // ------
  // Auction Lot Grid Item
  "auctionNum" => "an",
);

class Auction implements JsonSerializable {
  private $id;
  private $num;
  private $startTime;
  private $collectionDeadline;
  private $location;
  private $auctionPdf;
  private $resultPdf;
  private $remarks;
  private $lotCount;
  private $transactionTotal;
  private $itemPdfList;
  private $lotList;
  private $auctionStatus;
  private $status;
  private $lastUpdate;

  public function __construct($id, $num, $startTime, $collectionDeadline, $location, $auctionPdf, $resultPdf, $remarks, $lotCount, $transactionTotal, $auctionStatus, $status, $lastUpdate) {
    $this->id = $id;
    $this->num = $num;
    $this->startTime = $startTime;
    $this->collectionDeadline = $collectionDeadline;
    $this->location = $location;
    $this->auctionPdf = $auctionPdf;
    $this->resultPdf = $resultPdf;
    $this->remarks = $remarks;
    $this->lotCount = $lotCount;
    $this->transactionTotal = $transactionTotal;
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
  private $descriptionEn;
  private $descriptionTc;
  private $descriptionSc;

  private $featured;
  private $icon;
  private $photoUrl;
  private $photoReal;
  private $photoAuthor;
  private $photoAuthorUrl;
  private $itemList;
  private $specialInspection;
  private $inspectionDateList;
  private $tranCurrency;
  private $tranPrice;
  private $tranStatus;
  private $status;
  private $lastUpdate;

  public function __construct($id, $type, $lotNum, $gldFileRef, $reference, $department, $contact, $number, $location, $remarks, $itemCondition, $featured, $icon, 
                              $photoUrl, $photoReal, $photoAuthor, $photoAuthorUrl, $descriptionEn, $descriptionTc, $descriptionSc, $tranCurrency, $tranPrice, $tranStatus, $status, $lastUpdate) {
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
    $this->photoAuthor = $photoAuthor;
    $this->photoAuthorUrl = $photoAuthorUrl;
    $this->descriptionEn = $descriptionEn;
    $this->descriptionTc = $descriptionTc;
    $this->descriptionSc = $descriptionSc;

    $this->itemList = array();
    $this->specialInspection = false;
    $this->inspectionDateList = array();
    $this->tranCurrency = $tranCurrency;
    $this->tranPrice = $tranPrice;
    $this->tranStatus = $tranStatus;
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

class AuctionItem implements JsonSerializable {
  private $id;
  private $icon;
  private $description;
  private $quantity;
  private $unit;

  public function __construct($id, $icon, $description, $quantity, $unit) {
    $this->id = $id;
    $this->icon = $icon;
    $this->description = $description;
    $this->quantity = $quantity;
    $this->unit = $unit;
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

class AuctionLotSearch implements JsonSerializable {
  private $auctionId;
  private $auctionNum;
  private $startTime;
  private $auctionStatus;
  private $lotId;
  private $lotNum;
  private $type;
  private $description;
  private $featured;
  private $icon;
  private $photoUrl;
  private $photoReal;
  private $photoAuthor;
  private $photoAuthorUrl;
  private $tranCurrency;
  private $tranPrice;
  private $tranStatus;

  public function __construct($auction_id, $auction_num, $start_time, $auction_status, $lot_id, $lot_num, $type, $description, $featured, $icon, 
                              $photoUrl, $photoReal, $photoAuthor, $photoAuthorUrl, $tranCurrency, $tranPrice, $tranStatus) {
    $this->auctionId = $auction_id;
    $this->auctionNum = $auction_num;
    $this->startTime = $start_time;
    $this->auctionStatus = $auction_status;
    $this->lotId = $lot_id;
    $this->lotNum = $lot_num;
    $this->type = $type;
    $this->description = $description;
    $this->featured = $featured;
    $this->icon = $icon;

    $this->photoUrl = $photoUrl;
    $this->photoReal = $photoReal;
    $this->photoAuthor = $photoAuthor;
    $this->photoAuthorUrl = $photoAuthorUrl;
    $this->tranCurrency = $tranCurrency;
    $this->tranPrice = $tranPrice;
    $this->tranStatus = $tranStatus;
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

class AuctionItemSearch implements JsonSerializable {
  private $auctionId;
  private $startTime;
  private $auctionStatus;
  private $lotId;
  private $type;
  private $featured;
  private $photoUrl;
  private $photoReal;
  private $photoAuthor;
  private $photoAuthorUrl;
  private $tranCurrency;
  private $tranPrice;
  private $tranStatus;
  private $icon;
  private $description;
  private $quantity;
  private $unit;

  public function __construct($auction_id, $start_time, $auction_status, $lot_id, $type, $featured, $photoUrl, $photoReal, $photoAuthor, $photoAuthorUrl, $tranCurrency, $tranPrice, $tranStatus, $icon, $description, $quantity, $unit) {
    $this->auctionId = $auction_id;
    $this->startTime = $start_time;
    $this->auctionStatus = $auction_status;
    $this->lotId = $lot_id;
    $this->type = $type;
    $this->featured = $featured;
    $this->photoUrl = $photoUrl;
    $this->photoReal = $photoReal;
    $this->photoAuthor = $photoAuthor;
    $this->photoAuthorUrl = $photoAuthorUrl;
    $this->tranCurrency = $tranCurrency;
    $this->tranPrice = $tranPrice;
    $this->tranStatus = $tranStatus;
    $this->icon = $icon;
    $this->description = $description;
    $this->quantity = $quantity;
    $this->unit = $unit;
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

class InspectionDate implements JsonSerializable {
  private $dayOfWeek;
  private $startTime;
  private $endTime;

  public function __construct($day, $start_time, $end_time) {
    $this->dayOfWeek = $day;
    $this->startTime = $start_time;
    $this->endTime = $end_time;
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