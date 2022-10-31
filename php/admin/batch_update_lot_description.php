<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/config.php");
include_once ("../include/common.php");

$outputMessage = "";
if (isset($_POST["update_description"])) {
  include_once ('../include/adodb5/adodb.inc.php');

  $auctionId = intval($_POST["auction_id"]);
  $forceUpdate = isset($_POST["force_update"]) && ($_POST["force_update"] == "1");

  $successLotId = array();
  $failLotId = array();

  $conn = new stdClass();
  $conn = ADONewConnection('mysqli');
  $conn->PConnect($GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'] , $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_NAME']);
  $conn->Execute("SET NAMES UTF8");

  $selectSql = "SELECT lot_id FROM AuctionLot WHERE (auction_id = ? OR 0 = ?)";
  if (!$forceUpdate) {
    $selectSql .= " AND (description_en = '' OR description_tc = '' OR description_sc = '')";
  }
  $lotResult = $conn->Execute($selectSql, array($auctionId, $auctionId))->GetRows();
  $rowTotal = count($lotResult);

  for ($i = 0; $i < $rowTotal; ++$i) {
    $curLotId = $lotResult[$i]["lot_id"];
    $selectSql = "SELECT description_en, description_tc, description_sc, quantity, unit_en, unit_tc, unit_sc FROM AuctionItem WHERE lot_id = ? ORDER BY seq";

    $result = $conn->Execute($selectSql, array($curLotId))->GetRows();
    $rowNum = count($result);
    $descriptionEn = "";
    $descriptionTc = "";
    $descriptionSc = "";

    for ($j = 0; $j < $rowNum; ++$j) {
      $descriptionEn .= CommonGetSearchKeyword(trim($result[$j]["description_en"]), "en") . " " . trim($result[$j]["quantity"]) . " " . trim($result[$j]["unit_en"]) . ", ";
      $descriptionTc .= CommonGetSearchKeyword(trim($result[$j]["description_tc"]), "tc") . trim($result[$j]["quantity"]) . trim($result[$j]["unit_tc"]) . ", ";
      $descriptionSc .= CommonGetSearchKeyword(trim($result[$j]["description_sc"]), "sc") . trim($result[$j]["quantity"]) . trim($result[$j]["unit_sc"]) . ", ";
    }

    $updateSql = "UPDATE AuctionLot SET description_en = ?, description_tc = ?, description_sc = ? WHERE lot_id = ?";
    $updateResult = $conn->Execute($updateSql, array(rtrim($descriptionEn, ", "), rtrim($descriptionTc, ", "), rtrim($descriptionSc, ", "), $curLotId));

    if ($updateResult) {
      $successLotId[] = $curLotId;
    } else {
      $failLotId[] = $curLotId;
    }
  }
  $conn->close();

  $outputMessage = "<span style='text-decoration: underline'>Result</span><br>";
  $outputMessage .= "Total: $rowTotal<br>";
  $outputMessage .= "<span style='color: #080'>Success Lot ID: </span>" . (count($successLotId) > 0 ? implode(", ", $successLotId) : "-") . "<br>";
  $outputMessage .= "<span style='color: #800'>Failed Lot ID: </span>" . (count($failLotId) > 0 ? implode(", ", $failLotId) : "-") . "<hr>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Keyword Image</title>
  <link rel="stylesheet" href="css/main.css">
  <style>
  </style>
</head>
<body>
  <div class="header bgPurple">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Batch Update Lot Descriptions</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <?=$outputMessage?>
    <form method="POST">
      <div style="margin-left: 5px">Auction ID: <input name="auction_id" type="number" placeholder="0 or empty to update all lots" style="width: 200px"></div>
      <div><label><input type="checkbox" name="force_update" value="1">Force update all descriptions</label></div>
      <br /><br />
      <button type="submit" name="update_description" value="1">Update</button>
      &nbsp;&nbsp;
      <button type="reset">Reset</button>
    </form>
    <br />
    <hr style="width: 33%; float: left" />
    <div class="remarks" style="clear: both">
    <ul style="margin-left: -20px">
      <li>to update AuctionLots description fields based on AuctionItem by batch</li>
      <li>check "Force update all descriptions" will overwrite existing AuctionLots.description fields</li>
      <li>uncheck "Force update all descriptions" will only update those fields that any lang of AuctionLots.description is empty</li>
      <li>update all records should finish in 20 seconds</li>
    </ul>
    </div>
  </div>
  <script src="js/main.js"></script>
  <script>
  </script>
</body>
</html>