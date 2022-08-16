<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/enum.php");

$id = $_GET["id"];
$type = isset($_GET["type"]) ? $_GET["type"] : "";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Auction Details</title>
  <style></style>
</head>
<body>
  <a href="index.php" style="float:left;text-decoration:none">< Index</a>
  <div style="text-align:right"><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  <hr />
  <a href="auction_list.php">Auction List</a>&nbsp;&nbsp;
  <select onchange="GetData(<?=$id?>, this.value)">
    <option value="" <?=$type=="" ? "selected" : ""?>>All</option>
    <option value="<?=ItemType::ConfiscatedGoods?>" <?=$type==ItemType::ConfiscatedGoods ? "selected" : ""?>>[<?=ItemType::ConfiscatedGoods?>] 充公物品</option>
    <option value="<?=ItemType::UnclaimedProperties?>" <?=$type==ItemType::UnclaimedProperties ? "selected" : ""?>>[<?=ItemType::UnclaimedProperties?>] 無人認領物品</option>
    <option value="<?=ItemType::UnserviceableStores?>" <?=$type==ItemType::UnserviceableStores ? "selected" : ""?>>[<?=ItemType::UnserviceableStores?>] 廢棄物品及剩餘物品</option>
    <option value="<?=ItemType::SurplusServiceableStores?>" <?=$type==ItemType::SurplusServiceableStores ? "selected" : ""?>>[<?=ItemType::SurplusServiceableStores?>] 仍可使用之廢棄物品及剩餘物品</option>
  </select>
  <div style="width: 300px; display: flex; justify-content: space-between; border: solid 1px #000; margin-top: 10px; padding: 5px;">
    <div>ID: <span id="spnId" style="font-weight: bold"></span></div>
    <div id="divAuctionNum" style="font-weight: bold; text-decoration: underline"></div>
    <div id="divStartTime" style="font-style: italic"></div>
  </div>
  <div style="width: 1350px; display: flex; justify-content: space-between; margin-top: 10px;">
    <div>
      <span style="text-decoration: underline">Auction PDF</span>
      <div>EN: <a id="lnkAuctionPdfEn" href="#" target="_blank"></a></div>
      <div>TC: <a id="lnkAuctionPdfTc" href="#" target="_blank"></a></div>
      <div>SC: <a id="lnkAuctionPdfSc" href="#" target="_blank"></a></div>
    </div>
    <div>
      <span style="text-decoration: underline">Result PDF</span>
      <div>EN: <a id="lnkResultPdfEn" href="#" target="_blank"></a></div>
      <div>TC: <a id="lnkResultPdfTc" href="#" target="_blank"></a></div>
      <div>SC: <a id="lnkResultPdfSc" href="#" target="_blank"></a></div>
    </div>
  </div>

  <div style="width: 1350px; display: flex; justify-content: space-between; margin-top: 10px;">
    <pre id="preAddressEn" style="flex-grow: 1; border: solid 1px #000"></pre>
    <pre id="preAddressTc" style="flex-grow: 1; border: solid 1px #000"></pre>
    <pre id="preAddressSc" style="flex-grow: 1;border: solid 1px #000"></pre>
  </div>
  <div style="white-space: nowrap">
    Auction Status: <span id="spnAuctionStatus"></span>
    &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
    Status: <span id="spnStatus"></span>
    &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
    Last Update: <span id="spnLastUpdate"></span>
  </div>

  <script>
    function GetData(auctionId, type)
    {
      var apiUrl = '../en/api/admin-getAuction?id=' + auctionId + "&type=" + type;
      var xhr = new XMLHttpRequest();
      
      xhr.open("GET", apiUrl);
      xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const jsonData = JSON.parse(this.responseText);
          buildDetails(jsonData);
        }
      }

      xhr.send();
    }

    function buildDetails(jsonData) {
      document.getElementById("spnId").innerHTML = jsonData["auction_id"];
      document.getElementById("divAuctionNum").innerHTML = jsonData["auction_num"];
      document.getElementById("divStartTime").innerHTML = jsonData["start_time"];
      
      document.getElementById("lnkAuctionPdfEn").setAttribute("href", jsonData["auction_pdf_en"]);
      document.getElementById("lnkAuctionPdfEn").innerHTML = jsonData["auction_pdf_en"];
      document.getElementById("lnkAuctionPdfTc").setAttribute("href", jsonData["auction_pdf_tc"]);
      document.getElementById("lnkAuctionPdfTc").innerHTML = jsonData["auction_pdf_tc"];
      document.getElementById("lnkAuctionPdfSc").setAttribute("href", jsonData["auction_pdf_sc"]);
      document.getElementById("lnkAuctionPdfSc").innerHTML = jsonData["auction_pdf_sc"];
      document.getElementById("lnkResultPdfEn").setAttribute("href", jsonData["result_pdf_en"]);
      document.getElementById("lnkResultPdfEn").innerHTML = jsonData["result_pdf_en"];
      document.getElementById("lnkResultPdfTc").setAttribute("href", jsonData["result_pdf_tc"]);
      document.getElementById("lnkResultPdfTc").innerHTML = jsonData["result_pdf_tc"];
      document.getElementById("lnkResultPdfSc").setAttribute("href", jsonData["result_pdf_sc"]);
      document.getElementById("lnkResultPdfSc").innerHTML = jsonData["result_pdf_sc"];
      
      document.getElementById("preAddressEn").innerHTML = jsonData["address_en"];
      document.getElementById("preAddressTc").innerHTML = jsonData["address_tc"];
      document.getElementById("preAddressSc").innerHTML = jsonData["address_sc"];

      document.getElementById("spnAuctionStatus").innerHTML = jsonData["auction_status"];
      document.getElementById("spnStatus").innerHTML = jsonData["status"];
      document.getElementById("spnLastUpdate").innerHTML = jsonData["last_update"];
    }

    GetData(<?=$id?>, "<?=$type?>");
  </script>
</body>
</html>