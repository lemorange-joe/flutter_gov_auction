<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/config.php");
include_once ("../include/enum.php");
include_once ("../include/appdata.php");
$_APP = AppData::getInstance();

$id = $_GET["id"];
$featuredOnly = isset($_GET["featuredOnly"]) ? ($_GET["featuredOnly"] == 1) : false;
$type = isset($_GET["type"]) ? $_GET["type"] : "";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Auction Details</title>
  <link rel="stylesheet" href="css/main.css?v=<?=$ADMIN_VERSION?>">
</head>
<body>
  <div class="header">
    <div><h2><a href="index.php">« Admin Index</a></h2>&nbsp;&nbsp;<h2><a href="auction_list.php">‹ Auction List</a></h2></div>
    <div class="title">Auction Details: <?=$id?></div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <div style="display: flex; justify-content: space-between; width: 600px">
      <div style="width: 450px; display: flex; justify-content: space-between; border: solid 1px #000; margin-top: 10px; padding: 5px;">
        <div>ID: <input id="tbAuctionId" style="width: 30px" type="text" disabled="disabled"/></div>
        <div>|</div>
        <div id="divAuctionNum" style="font-weight: bold"></div>
        <div>|</div>
        <div id="divStartTime" style="text-decoration: underline"></div>
        <div>❯</div>
        <div id="divCollectionDeadline" style="text-decoration: underline"></div>
      </div>
      <a id="lnkImportNewLot" href="#" style="margin-top: 15px">Import New Lot</a>
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
    <div style="width: 1350px; margin-top: 10px">      
      <span style="text-decoration: underline">Remarks</span>
      <div style="display: flex; justify-content: start">
        <div style="width: 50px">EN</div>
        <textarea id="txtRemarksEn" style="width:600px; height:45px; white-space: normal" readonly></textarea>
      </div>
      <div style="display: flex; justify-content: start">
        <div style="width: 50px">TC</div>
        <textarea id="txtRemarksTc" style="width:600px; height:45px; white-space: normal" readonly></textarea>
      </div>
      <div style="display: flex; justify-content: start">
        <div style="width: 50px">SC</div>
        <textarea id="txtRemarksSc" style="width:600px; height:45px; white-space: normal" readonly></textarea>
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

    <hr style="width: 1350px; margin-left: 0" />
    Item PDF:
    <div id="divItemPdfList" style="display: flex; flex-wrap: wrap; width: 1200px"></div>
    <div style="margin-top: 10px">
      <button id="btnAddItemPdf" data-item-pdf-count="0" onclick="AddItemPdf()">+ PDF</button>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <button id="btnSaveItemPdf" onclick="SaveItemPdf()">Save PDF</button>
    </div>

    <hr style="border-top: solid 3px #46f" />
    
    <div style="padding: 10px 0; background-color: #ccc">
      <span style="font-weight: bold">Show lots:</span>
      <select id="ddlAuctionType" onchange="GetData(<?=$id?>, document.getElementById('chkFeaturedOnly').checked, this.value)">
        <option value="" <?=$type=="" ? "selected" : ""?>>All</option>
        <?php
          foreach ($_APP->auctionItemTypeList as $auctionItemType) {
            echo "<option value='" . $auctionItemType->code . "'" . ($type == $auctionItemType->code ? " selected" : "") . ">" . $auctionItemType->code . " - " . $auctionItemType->description("tc") . "</option>";
          }
        ?>
      </select>
      <input id="chkFeaturedOnly" type="checkbox" style="margin: 0 5px 0 15px" onchange="GetData(<?=$id?>, this.checked, document.getElementById('ddlAuctionType').value)"><label for="chkFeaturedOnly">Featured Only</label>
    </div>

    <div id="divLotList" style="width: 1400px"></div>
    <div style="margin-top: 10px">
      <button id="btnAddLot" data-lot-count="0" onclick="AddLot()">+ Lot</button>
      <div class="remarks" style="margin-top:10px">* add inspection dates after adding the lot</div>
    </div>

    <button style="position: fixed; right: 20px; bottom: 140px; width:36px; height: 36px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
    <button style="position: fixed; right: 20px; bottom: 100px; width:36px; height: 36px; font-size: 20px" onclick="JumpScroll(-100)" onmouseover="AutoScroll(-12)" onmouseout="StopScroll()">▲</button>
    <button style="position: fixed; right: 20px; bottom: 60px; width:36px; height: 36px; font-size: 20px" onclick="JumpScroll(100)"  onmouseover="AutoScroll(12)" onmouseout="StopScroll()">▼</button>
    <button style="position: fixed; right: 20px; bottom: 20px; width:36px; height: 36px; font-size: 20px" onclick="window.scrollTo(0, document.body.scrollHeight)">⟱</button>
    <script src="js/main.js?v=<?=$ADMIN_VERSION?>"></script>
    <script>
      var photoRootUrl = "<?=$GLOBALS["AUCTION_IMAGE_ROOT_URL"]?>";
      const conditionMapping = [
	      {	"en": "Serviceable But May Not Function Properly/May be Damaged", "tc": "仍可使用但或許不能正常操作或已有損壞", "sc": "仍可使用但或许不能正常操作或已有损坏" },
	      { "en": "May be Unserviceable/May Not Function Properly/May be Damaged", "tc": "或不能再用/或不能正常操作/或已有損壞", "sc": "或不能再用/或不能正常操作/或已有损坏" },
	      { "en": "Abandoned Regulated Electrical Equipment", "tc": "被棄置受管制電器", "sc": "被弃置受管制电器" },
	      { "en": "Empty Toner/Ink Cartridges", "tc": "已用完的空碳粉匣", "sc": "已用完的空碳粉匣" },
	      { "en": "May be Damaged", "tc": "或許已有損壞", "sc": "或许已有损坏" },
	      { "en": "Unserviceable", "tc": "不能再用", "sc": "不能再用" },
      ];

      function GetImageSrc(photoUrl) {
        if (!photoUrl.startsWith("http://") && !photoUrl.startsWith("https://") && photoUrl.trim() != "") {
          return photoRootUrl + photoUrl;
        }

        return photoUrl;
      }

      function GetDdl(id, selectedValue, type) {
        var select = document.createElement("select");
        var option;
        var values;

        if (type == "TransactionStatus") {
          values = {
            // derived status: "P" (pending) is not available
            "N": "Not Sold",
            "S": "Sold",
          };
        } else if (type == "Status") {
          values = {
            "A": "Active",
            "P": "Pending",
            "I": "Inactive",
          };
        } else if (type == "ItemType") {
          values = {
            <?php
            foreach ($_APP->auctionItemTypeList as $auctionItemType) {
              echo '"' . $auctionItemType->code . '": "' . $auctionItemType->code .' - ' . $auctionItemType->description("tc") . '",' . "\n";
            }
            ?>            
          };
        }
        
        select.setAttribute("id", id);
        Object.keys(values).forEach(function(k) {
          var option = document.createElement("option");
          option.value = k;
          option.textContent = values[k];

          if (selectedValue == k) {
            option.setAttribute("selected", "selected");
          }
          select.appendChild(option);
        });

        return select.outerHTML;
      }

      function GetData(auctionId, featuredOnly, type)
      {
        var apiUrl = '../en/api/admin-getAuction-' + auctionId + "-" + (featuredOnly ? "1" : "0") + "-" + type;
        var xhr = new XMLHttpRequest();
        
        xhr.open("GET", apiUrl);
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            const jsonData = JSON.parse(this.responseText);
            BuildDetails(jsonData);
          }

          document.getElementById("tbAuctionId").value = auctionId;
          document.getElementById("chkFeaturedOnly").checked = featuredOnly;
          document.getElementById("ddlAuctionType").value = type;
        }

        xhr.send();
      }

      function PostData(url, auctionData) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", url);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
            if (this.status == 200) {
              const jsonData = JSON.parse(this.responseText);

              if (jsonData.status == "success") {
                GetData(document.getElementById("tbAuctionId").value, document.getElementById("chkFeaturedOnly").checked, document.getElementById("ddlAuctionType").value);
              } else {
                alert("Update failed: " + jsonData.error);  
              }
            } else {
              alert("Error: " + this.responseText);
            }
          }
        };

        xhr.send(JSON.stringify(auctionData));
      }

      function AddItemPdf() {
        BuildItemPdfDiv(parseInt(document.getElementById("btnAddItemPdf").getAttribute("data-item-pdf-count")), "", "", "", "");
      }

      function SaveItemPdf() {
        var i = 0;
        var itemPdfList = [];

        while (document.getElementById("ddlItemPdf_"+i)) {
          var type = document.getElementById("ddlItemPdf_"+i).value;

          if (type != "") {
            var urlEn = document.getElementById("tbItemPdfEn_"+i).value;
            var urlTc = document.getElementById("tbItemPdfTc_"+i).value;
            var urlSc = document.getElementById("tbItemPdfSc_"+i).value;

            itemPdfList.push({
              type: type,
              url_en: urlEn,
              url_tc: urlTc,
              url_sc: urlSc,
            });
          }

          ++i;
        }

        var postData = {
          id: document.getElementById("tbAuctionId").value,
          item_pdf_list: itemPdfList
        }

        TempDisableButton("btnSaveItemPdf");
        PostData("../en/api/admin-updateAuctionItemPdf", postData);
      }

      function BuildItemPdfDiv(i, type, urlEn, urlTc, urlSc) {
        var selected = "";
        var divHtml = "<div style='border: solid 1px #000;padding: 8px;'>";
        divHtml += "<select id='ddlItemPdf_" + i + "' style='margin-bottom: 10px'>";
        divHtml += "<option value=''>-- Empty -- </option>";
          <?php
            foreach ($_APP->auctionItemTypeList as $auctionItemType) {
              echo 'selected = (type == "' . $auctionItemType->code . '") ? "selected" : "";' . "\n";
              echo 'divHtml += "<option value=\'' . $auctionItemType->code . '\'" + selected + ">' . $auctionItemType->code .' - ' . $auctionItemType->description("tc") . '</option>";' . "\n";
            }
          ?>            
        divHtml += "</select>";
        divHtml += "<div style='width: 550px'>EN: <input id='tbItemPdfEn_" + i + "' type='text' value='" + urlEn + "' style='width: 500px;'></div>";
        divHtml += "<div style='width: 550px'>TC: <input id='tbItemPdfTc_" + i + "' type='text' value='" + urlTc + "' style='width: 500px;'></div>";
        divHtml += "<div style='width: 550px'>SC: <input id='tbItemPdfSc_" + i + "' type='text' value='" + urlSc + "' style='width: 500px;'></div>";
        divHtml += "<div>";

        document.getElementById("divItemPdfList").insertAdjacentHTML("beforeend", divHtml);
        document.getElementById("btnAddItemPdf").setAttribute("data-item-pdf-count", i+1);
      }

      function AddLot() {
        var lotIndex = parseInt(document.getElementById("btnAddLot").getAttribute("data-lot-count"));
        BuildLotDiv(lotIndex);
        document.getElementById("btnAddLot").setAttribute("data-lot-count", lotIndex + 1);
      }

      function UpdateLotFeatured(i) {
        var lotId = document.getElementById("tbLotId_"+i).value;
        var chk = document.getElementById("chkFeatured_"+i);
        var origVal = chk.getAttribute("data-checked");
        var newVal = origVal == "1" ? "0" : "1";

        chk.setAttribute("data-checked", newVal);
        chk.innerHTML = newVal == "1" ? "★" : "☆";

        if (lotId <= 0) return; // this is adding new lot, no need to update featured immediately

        var logData = {
          lot_id: lotId,
          featured: parseInt(newVal)
        };
        var url = "../en/api/admin-updateAuctionLotFeatured";

        var xhr = new XMLHttpRequest();
        xhr.open("POST", url);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
            if (this.status == 200) {
              const jsonData = JSON.parse(this.responseText);

              if (jsonData.status == "success") {
                var divLastUpdate = document.getElementById("divLastUpdate_"+i);
                divLastUpdate.innerHTML = jsonData.data;
                divLastUpdate.classList.remove("highlight-text");
                chk.classList.remove("highlight-text");
                setTimeout(() => {
                  chk.classList.add("highlight-text");
                  divLastUpdate.classList.add("highlight-text");
                }, 100);
              } else {
                chk.setAttribute("data-checked", origVal);
                chk.innerHTML = origVal == "1" ? "★" : "☆";
                alert("Update failed: " + jsonData.error);
              }
            } else {
              alert("Error: " + this.responseText);
            }
          }
        };

        xhr.send(JSON.stringify(logData));
      }

      function AddInspectionDateRow(i) {
        var lotId = parseInt(document.getElementById("tbLotId_" + i).value);

        if (document.getElementById("divNewInspectionDateField")) {
          document.getElementById("divNewInspectionDateField").outerHTML = "";
        }

        output = '<div id="divNewInspectionDateField">';
        output += '<input type="hidden" id="tbNewInspectionLotId" value="' + lotId + '">';
        // must have day of week
        // output += '<input type="radio" id="rdbNewInspectionDay_0" name="rdbNewInspectionDay" value="0">';
        // output += '<label for="rdbNewInspectionDay_0">沒有註明</label>';
        output += '<input type="radio" id="rdbNewInspectionDay_7" name="rdbNewInspectionDay" value="7" style="margin-left: 0px">';
        output += '<label for="rdbNewInspectionDay_7">日</label>';
        output += '<input type="radio" id="rdbNewInspectionDay_1" name="rdbNewInspectionDay" value="1" style="margin-left: 10px">';
        output += '<label for="rdbNewInspectionDay_1">一</label>';
        output += '<input type="radio" id="rdbNewInspectionDay_2" name="rdbNewInspectionDay" value="2" style="margin-left: 10px">';
        output += '<label for="rdbNewInspectionDay_2">二</label>';
        output += '<input type="radio" id="rdbNewInspectionDay_3" name="rdbNewInspectionDay" value="3" style="margin-left: 10px">';
        output += '<label for="rdbNewInspectionDay_3">三</label>';
        output += '<input type="radio" id="rdbNewInspectionDay_4" name="rdbNewInspectionDay" value="4" style="margin-left: 10px">';
        output += '<label for="rdbNewInspectionDay_4">四</label>';
        output += '<input type="radio" id="rdbNewInspectionDay_5" name="rdbNewInspectionDay" value="5" style="margin-left: 10px">';
        output += '<label for="rdbNewInspectionDay_5">五</label>';
        output += '<input type="radio" id="rdbNewInspectionDay_6" name="rdbNewInspectionDay" value="6" style="margin-left: 10px">';
        output += '<label for="rdbNewInspectionDay_6">六</label>';
        output += "&nbsp;&nbsp;";
        output += '<input id="tbNewInspectionStartTime" type="text" placeholder="09:30" maxlength="5" style="width: 40px">';
        output += " - ";
        output += '<input id="tbNewInspectionEndTime" type="text" placeholder="12:30" maxlength="5" style="width: 40px">';
        output += '&nbsp;（ ';
            output += '<input id="tbNewTyphoonStartTime" type="text" placeholder="--:--" maxlength="5" style="width: 40px">';
            output += " - ";
            output += '<input id="tbNewTyphoonEndTime" type="text" placeholder="--:--" maxlength="5" style="width: 40px">';
          output += ' ）';
        output += '<button style="margin-left: 10px" onclick="AddInspectionDate()">Save</button>'
        output += "</div>";

        document.getElementById("divNewInspectionRow_" + i).innerHTML = output;
      }      

      function AddInspectionDate() {
        var selectedRdbDay = Array.prototype.slice.call(document.getElementsByName("rdbNewInspectionDay")).find((rdb) => rdb.checked);
        if (!selectedRdbDay) {
          alert("Please select day of week!");
          return;
        }

        var lotId = document.getElementById("tbNewInspectionLotId").value;
        var day = selectedRdbDay.value;
        var startTime = document.getElementById("tbNewInspectionStartTime").value;
        var endTime = document.getElementById("tbNewInspectionEndTime").value;
        var typhoonStartTime = document.getElementById("tbNewTyphoonStartTime").value;
        var typhoonEndTime = document.getElementById("tbNewTyphoonEndTime").value;

        var inspectionData = {
          lot_id: lotId,
          day: day,
          start_time: startTime,
          end_time: endTime,
          typhoon_start_time: typhoonStartTime,
          typhoon_end_time: typhoonEndTime,
        };

        var url = "../en/api/admin-addInspectionDate";
        
        PostData(url, inspectionData);
      }

      function DeleteInspectionDate(inspectionId) {
        var inspectionData = {
          inspection_id: inspectionId,
        };
        var url = "../en/api/admin-deleteInspectionDate";

        PostData(url, inspectionData);
      }

      function SaveLot(lotIndex) {
        var auctionId = document.getElementById("tbAuctionId").value;
        var lotId = document.getElementById("tbLotId_"+lotIndex).value;
        var itemCode = document.getElementById("ddlItemType_"+lotIndex).value;
        var lotNum = document.getElementById("tbLotNum_"+lotIndex).value;
        var gldFileRef = document.getElementById("tbGldRef_"+lotIndex).value;
        var reference = document.getElementById("tbRef_"+lotIndex).value;
        var departmentEn = document.getElementById("tbDeptEn_"+lotIndex).value;
        var departmentTc = document.getElementById("tbDeptTc_"+lotIndex).value;
        var departmentSc = document.getElementById("tbDeptSc_"+lotIndex).value;
        var contactEn = document.getElementById("tbContactEn_"+lotIndex).value;
        var contactTc = document.getElementById("tbContactTc_"+lotIndex).value;
        var contactSc = document.getElementById("tbContactSc_"+lotIndex).value;
        var numberEn = document.getElementById("tbNumberEn_"+lotIndex).value;
        var numberTc = document.getElementById("tbNumberTc_"+lotIndex).value;
        var numberSc = document.getElementById("tbNumberSc_"+lotIndex).value;
        var locationEn = document.getElementById("tbLocationEn_"+lotIndex).value;
        var locationTc = document.getElementById("tbLocationTc_"+lotIndex).value;
        var locationSc = document.getElementById("tbLocationSc_"+lotIndex).value;
        var remarksEn = document.getElementById("tbRemarksEn_"+lotIndex).value;
        var remarksTc = document.getElementById("tbRemarksTc_"+lotIndex).value;
        var remarksSc = document.getElementById("tbRemarksSc_"+lotIndex).value;
        var itemConditionEn = document.getElementById("tbItemConditionEn_"+lotIndex).value;
        var itemConditionTc = document.getElementById("tbItemConditionTc_"+lotIndex).value;
        var itemConditionSc = document.getElementById("tbItemConditionSc_"+lotIndex).value;
        var featured = parseInt(document.getElementById("chkFeatured_"+lotIndex).getAttribute("data-checked"));
        var lotIcon = document.getElementById("tbLotIcon_"+lotIndex).value;
        var photoUrl = document.getElementById("tbPhotoUrl_"+lotIndex).value;
        var photoReal = document.getElementById("chkPhotoReal_"+lotIndex).checked ? 1 : 0;
        var photoAuthor = document.getElementById("tbPhotoAuthor_"+lotIndex).value;
        var photoAuthorUrl = document.getElementById("tbPhotoAuthorUrl_"+lotIndex).value;
        var tranCurrency = document.getElementById("tbTranCurrency"+lotIndex).value;
        var tranPrice = document.getElementById("tbTranPrice_"+lotIndex).value;
        var tranStatus = document.getElementById("ddlTranStatus_"+lotIndex).value;
        var status = document.getElementById("ddlStatus_"+lotIndex).value;
        var itemList = [];

        var i = 0;
        while (document.getElementById("tbItem_"+lotIndex+"_"+i)) {
          var itemIcon = document.getElementById("tbItemIcon_"+lotIndex+"_"+i).value;
          var itemText = document.getElementById("tbItem_"+lotIndex+"_"+i).value;
          var itemDetails = itemText.split("\n");
          if (itemDetails.length >= 7 && !isNaN(parseFloat(itemDetails[3]))) {
            var descriptionEn = itemDetails[0];
            var descriptionTc = itemDetails[1];
            var descriptionSc = itemDetails[2];
            var quantity = parseFloat(itemDetails[3]);
            var unitEn = itemDetails[4];
            var unitTc = itemDetails[5];
            var unitSc = itemDetails[6];

            itemList.push({
              icon: itemIcon,
              description_en: descriptionEn,
              description_tc: descriptionTc,
              description_sc: descriptionSc,
              quantity: quantity,
              unit_en: unitEn,
              unit_tc: unitTc,
              unit_sc: unitSc,
            });
          }
          ++i;
        }

        var lotData = {
          auction_id: auctionId,
          lot_id: lotId,
          item_code: itemCode,
          lot_num: lotNum,
          gld_file_ref: gldFileRef,
          reference: reference,
          department_en: departmentEn,
          department_tc: departmentTc,
          department_sc: departmentSc,
          contact_en: contactEn,
          contact_tc: contactTc,
          contact_sc: contactSc,
          number_en: numberEn,
          number_tc: numberTc,
          number_sc: numberSc,
          location_en: locationEn,
          location_tc: locationTc,
          location_sc: locationSc,
          remarks_en: remarksEn,
          remarks_tc: remarksTc,
          remarks_sc: remarksSc,
          item_condition_en: itemConditionEn,
          item_condition_tc: itemConditionTc,
          item_condition_sc: itemConditionSc,
          featured: featured,
          lot_icon: lotIcon,
          photo_url: photoUrl,
          photo_real: photoReal,
          photo_author: photoAuthor,
          photo_author_url: photoAuthorUrl,
          transaction_currency: tranCurrency,
          transaction_price: tranPrice,
          transaction_status: tranStatus,
          status: status,
          item_list: itemList
        };
        
        TempDisableButton("btnSaveLot_"+lotIndex);
        PostData("../en/api/admin-updateAuctionLot", lotData);
      }

      function BuildLotDiv(i, lotData) {
        var lotId = 0;
        var itemType = "";
        var lotNum = "";
        var gldFileRef = "";
        var reference = "";

        var departmentEn = "";
        var departmentTc = "";
        var departmentSc = "";
        var contactEn = "";
        var contactTc = "";
        var contactSc = "";
        var numberEn = "";
        var numberTc = "";
        var numberSc = "";
        var locationEn = "";
        var locationTc = "";
        var locationSc = "";
        var remarksEn = "";
        var remarksTc = "";
        var remarksSc = "";
        var itemConditionEn = "";
        var itemConditionTc = "";
        var itemConditionSc = "";
        var lotDescriptionEn = "";
        var lotDescriptionTc = "";
        var lotDescriptionSc = "";

        var featured = false;
        var lotIcon = "fontawesome.box";
        var photoUrl = "";
        var photoReal = false;
        var photoAuthor = "";
        var photoAuthorUrl = "";

        var transactionCurrency = "HKD";
        var transactionPrice = 0;
        var transactionStatus = "N";
        var status = "A";
        var lastUpdate = "";
        var itemList = [];
        var inspectionDateList = [];

        if (lotData){
          lotId = lotData["lot_id"];
          itemCode = lotData["item_code"];
          lotNum = lotData["lot_num"];
          gldFileRef = lotData["gld_file_ref"];
          reference = lotData["reference"];

          departmentEn = lotData["department_en"];
          departmentTc = lotData["department_tc"];
          departmentSc = lotData["department_sc"];
          contactEn = lotData["contact_en"];
          contactTc = lotData["contact_tc"];
          contactSc = lotData["contact_sc"];
          numberEn = lotData["number_en"];
          numberTc = lotData["number_tc"];
          numberSc = lotData["number_sc"];
          locationEn = lotData["location_en"];
          locationTc = lotData["location_tc"];
          locationSc = lotData["location_sc"];
          inspectionDateList = lotData["inspection_date_list"];

          remarksEn = lotData["remarks_en"];
          remarksTc = lotData["remarks_tc"];
          remarksSc = lotData["remarks_sc"];
          itemConditionEn = lotData["item_condition_en"];
          itemConditionTc = lotData["item_condition_tc"];
          itemConditionSc = lotData["item_condition_sc"];
          lotDescriptionEn = lotData["lot_description_en"];
          lotDescriptionTc = lotData["lot_description_tc"];
          lotDescriptionSc = lotData["lot_description_sc"];

          featured = lotData["featured"];
          lotIcon = lotData["lot_icon"];
          photoUrl = lotData["photo_url"];
          photoReal = lotData["photo_real"];
          photoAuthor = lotData["photo_author"];
          photoAuthorUrl = lotData["photo_author_url"];
          transactionCurrency = lotData["transaction_currency"];
          transactionPrice = lotData["transaction_price"];
          transactionStatus = lotData["transaction_status"];
          status = lotData["status"];
          lastUpdate = lotData["last_update"];
          itemList = lotData["item_list"];
        }

        var divHtml = "<div>";
        
        divHtml += "<div style='width:800px; float: left'>";
          divHtml += "<div style='display:flex; justify-content: space-between'>";
            divHtml += "<div style='display:flex'>";
              divHtml += "<div style='width:100px'>ID</div><input id='tbLotId_" + i + "' style='width:50px; margin-right: 60px' disabled='disabled' value='" + lotId + "'>";
              divHtml += GetDdl("ddlItemType_"+i, itemCode, "ItemType");
            divHtml += "</div>";
            divHtml += "<div id='chkFeatured_" + i + "' data-checked='" + (featured ? 1 : 0) + "' style='width:30px;margin-right:42px;cursor:pointer;font-size:18px;text-align:center' onclick='UpdateLotFeatured("+i+")'>";
              divHtml += featured ? "★" : "☆";
            divHtml += "</div>";
          divHtml += "</div>";
          divHtml += "<div style='display:flex; justify-content: space-between'>";
            divHtml += "<div><div style='display:inline-block;width:100px'>Lot Num</div><input id='tbLotNum_" + i + "' style='width:100px' value='" + lotNum.replace("'", '"') + "'></div>";
            if (i > 0) { 
              divHtml += "<button onclick='CopyInfo(" + i + ")' style='margin-right: 40px'>Copy from above</button>";
            }
          divHtml += "</div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>GLD Ref</div><input id='tbGldRef_" + i + "' style='width:200px' value='" + gldFileRef.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Ref</div><input id='tbRef_" + i + "' style='width:200px' value='" + reference.replace("'", '"') + "'></div>";
          divHtml += "<div style='height:10px'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Dept</div><input id='tbDeptEn_" + i + "' style='width:650px' value='" + departmentEn.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>部門</div><input id='tbDeptTc_" + i + "' style='width:650px' value='" + departmentTc.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>部门</div><input id='tbDeptSc_" + i + "' style='width:650px' value='" + departmentSc.replace("'", '"') + "'></div>";
          divHtml += "<div style='height:10px'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Contact</div><input id='tbContactEn_" + i + "' style='width:650px' value='" + contactEn.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>聯絡人</div><input id='tbContactTc_" + i + "' style='width:650px' value='" + contactTc.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>联络人</div><input id='tbContactSc_" + i + "' style='width:650px' value='" + contactSc.replace("'", '"') + "'></div>";
          divHtml += "<div style='height:10px'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Number</div><input id='tbNumberEn_" + i + "' style='width:650px' value='" + numberEn.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>電話</div><input id='tbNumberTc_" + i + "' style='width:650px' value='" + numberTc.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>电话</div><input id='tbNumberSc_" + i + "' style='width:650px' value='" + numberSc.replace("'", '"') + "'></div>";
          divHtml += "<div style='height:10px'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Location</div><input id='tbLocationEn_" + i + "' style='width:650px' value='" + locationEn.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>地點</div><input id='tbLocationTc_" + i + "' style='width:650px' value='" + locationTc.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>地点</div><input id='tbLocationSc_" + i + "' style='width:650px' value='" + locationSc.replace("'", '"') + "'></div>";
          divHtml += "<div style='height:10px'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>看貨日期</div>";
            divHtml += "<div>";
              divHtml += BuildInspectionDateField(i, inspectionDateList);
              divHtml += "<div id='divNewInspectionRow_" + i + "'></div>";
              divHtml += "<button onclick='AddInspectionDateRow("+i+")'>+ Date</button>";
            divHtml += "</div>";
          divHtml += "</div>";
          divHtml += "<div style='height:10px'></div>";
          divHtml += "<div style='display:flex;width:760px;height:40px'><div style='width:100px'>Description</div><div style='width:660px;overflow-x:scroll;white-space:nowrap'>" + lotDescriptionEn.replace("<", '&lt;').replace(">", '&gt;') + "</div></div>";
          divHtml += "<div style='display:flex;width:760px;height:40px'><div style='width:100px'></div><div style='width:660px;overflow-x:scroll;white-space:nowrap'>" + lotDescriptionTc.replace("<", '&lt;').replace(">", '&gt;') + "</div></div>";
          divHtml += "<div style='display:flex;width:760px;height:40px'><div style='width:100px'></div><div style='width:660px;overflow-x:scroll;white-space:nowrap'>" + lotDescriptionSc.replace("<", '&lt;').replace(">", '&gt;') + "</div></div>";
          divHtml += "<div style='height:10px'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Status</div>" + GetDdl("ddlStatus_"+i, status, "Status") + "</div>";
          divHtml += "<div style='height:10px'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Last Update:</div><div id='divLastUpdate_" + i + "'>" + lastUpdate + "</div></div>";
        divHtml += "</div>";
        divHtml += "<div style='width:600px;float: right;'>";
          divHtml += "<div style='height:20px'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Lot Icon</div><input id='tbLotIcon_" + i + "' value='" + lotIcon.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'>";
            divHtml += "<div style='width:100px'><a href='#' style='line-height: 32px;' onclick='window.open(GetImageSrc(document.getElementById(\"tbPhotoUrl_"+i+"\").value), \"_blank\");return false' title='View Photo'>Photo URL</a></div>";
            divHtml += "<div>";
              divHtml += "<button style='margin-right:5px;font-size:24px;padding:0 5px' onclick='GetLotImage("+i+")'>⊞</button>";
              divHtml += "<input id='tbPhotoUrl_" + i + "' style='width:490px' value='" + photoUrl.replace("'", '"') + "'>";
            divHtml += "</div>";
          divHtml += "</div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Author</div><input id='tbPhotoAuthor_" + i + "' style='width:200px' value='" + photoAuthor.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Author Url</div><input id='tbPhotoAuthorUrl_" + i + "' style='width:499px' value='" + photoAuthorUrl.replace("'", '"') + "'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Photo Real</div><input id='chkPhotoReal_" + i + "' type='checkbox' " + (photoReal ? "checked" : "") + "></div>";
          divHtml += "<div style='padding: 10px 0' class='" + (transactionStatus == 'S' ? 'green' : '') + "'>";
            divHtml += "<div style='display:flex'><div style='width:100px'>Currency</div><input id='tbTranCurrency" + i + "' value='" + transactionCurrency.replace("'", '"') + "'></div>";
            divHtml += "<div style='display:flex'><div style='width:100px'>Price</div><input id='tbTranPrice_" + i + "' value='" + transactionPrice + "'></div>";
            divHtml += "<div style='display:flex'><div style='width:100px'>T. Status</div>" + GetDdl("ddlTranStatus_"+i, transactionStatus, "TransactionStatus") + "</div>";
          divHtml += "</div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Remarks</div>";
          divHtml += "<textarea id='tbRemarksEn_" + i + "' style='width:600px;height:48px'>" + remarksEn + "</textarea></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>注意</div>";
          divHtml += "<textarea id='tbRemarksTc_" + i + "' style='width:600px;height:48px'>" + remarksTc + "</textarea></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>注意 (SC)</div>";
          divHtml += "<textarea id='tbRemarksSc_" + i + "' style='width:600px;height:48px'>" + remarksSc + "</textarea></div>";
          divHtml += "<div style='height:10px'></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'>Conditions</div>";
          divHtml += "<textarea id='tbItemConditionEn_" + i + "' style='width:600px;height:48px'>" + itemConditionEn + "</textarea></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'><a href='#' style='color: #000' onclick='CopyTxtConditions(" + i + ", \"tc\"); return false' title='Generate from conditions (EN)'>狀態</a></div>";
          divHtml += "<textarea id='tbItemConditionTc_" + i + "' style='width:600px;height:48px'>" + itemConditionTc + "</textarea></div>";
          divHtml += "<div style='display:flex'><div style='width:100px'><a href='#' style='color: #000' onclick='CopyTxtConditions(" + i + ", \"sc\"); return false' title='Generate from conditions (EN)'>状态</a></div>";
          divHtml += "<textarea id='tbItemConditionSc_" + i + "' style='width:600px;height:48px'>" + itemConditionSc + "</textarea></div>";
        divHtml += "</div>";
        
        divHtml += "<br style='clear: both' />";
        divHtml += "<div style='height:10px'></div>";
        divHtml += "<div id='divItems_" + i + "' style='width:1400px'>";
        for (var j = 0; j < itemList.length; ++j) {
          divHtml += BuildLotItems(i, j, itemList[j]);
        }
        divHtml += "</div>"

        divHtml += "<div><button id='btnAddItem_" + i + "' data-lot-index='" + i + "' data-total='" + itemList.length + "' onclick='AddItem(" + i + ")'>+ Item</button></div>";
        divHtml += "<div style='display: flex'>";
          divHtml += "<button id='btnSaveLot_" + i + "' onclick='SaveLot(" + i + ")' style='width: 80px;height: 30px;margin-top: 15px'>Save Lot</button>";
          // more buttons later
        divHtml += "</div>";

        divHtml += "</div>";
        divHtml += "<br style='clear: both' /><hr />";

        document.getElementById("divLotList").insertAdjacentHTML("beforeend", divHtml);
      }

      function AddItem(lotIndex) {
        var lotIndex = document.getElementById("btnAddItem_" + lotIndex).getAttribute("data-lot-index");
        var itemIndex = parseInt(document.getElementById("btnAddItem_" + lotIndex).getAttribute("data-total"))  ;

        var divHtml = BuildLotItems(lotIndex, itemIndex);

        document.getElementById("btnAddItem_" + lotIndex).setAttribute("data-total", itemIndex + 1);
        document.getElementById("divItems_" + lotIndex).insertAdjacentHTML("beforeend", divHtml);
        document.getElementById("tbItem_" + lotIndex + "_" + itemIndex).focus();
      }

      function BuildLotItems(lotIndex, itemIndex, itemData) {
        var itemIcon = itemData ? itemData["item_icon"].replace("'", '"') : "fontawesome.box";
        var textareaContent = itemData ? 
            itemData["description_en"] + "\n" + 
            itemData["description_tc"] + "\n" + 
            itemData["description_sc"] + "\n" + 
            itemData["quantity"] + "\n" + 
            itemData["unit_en"] + "\n" + 
            itemData["unit_tc"] + "\n" + 
            itemData["unit_sc"] + "\n"
          : "";

        var className = "auction-item-textarea item" + String(itemIndex + 1).padStart(2, "0");
        var textareaHtml = "<div style='display: inline-block'>";
        textareaHtml += "Icon: <input id='tbItemIcon_" + lotIndex + "_" + itemIndex + "' value='" + itemIcon + "' /><br/>";
        textareaHtml += "<textarea id='tbItem_" + lotIndex + "_" + itemIndex + "' class='" + className + "' style='width:250px;height:120px'>";
        textareaHtml += textareaContent;
        textareaHtml += "</textarea>";
        textareaHtml += "</div>";

        return textareaHtml
      }

      function BuildDetails(jsonData) {
        document.getElementById("tbAuctionId").value = jsonData["auction_id"];
        document.getElementById("divAuctionNum").innerHTML = jsonData["auction_num"];
        document.getElementById("divStartTime").innerHTML = jsonData["start_time"];
        document.getElementById("divCollectionDeadline").innerHTML = jsonData["collection_deadline"];
        document.getElementById("lnkImportNewLot").setAttribute("href", "import_auction_list.php?auction_num="+encodeURIComponent(jsonData["auction_num"]));
        
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
        document.getElementById("txtRemarksEn").value = jsonData["remarks_en"];
        document.getElementById("txtRemarksTc").value = jsonData["remarks_tc"];
        document.getElementById("txtRemarksSc").value = jsonData["remarks_sc"];
        
        document.getElementById("preAddressEn").innerHTML = jsonData["address_en"];
        document.getElementById("preAddressTc").innerHTML = jsonData["address_tc"];
        document.getElementById("preAddressSc").innerHTML = jsonData["address_sc"];

        document.getElementById("spnAuctionStatus").innerHTML = jsonData["auction_status"];
        document.getElementById("spnStatus").innerHTML = jsonData["status"];
        document.getElementById("spnLastUpdate").innerHTML = jsonData["last_update"];

        document.getElementById("divItemPdfList").innerHTML = "";
        var itemPdfList = jsonData["item_pdf_list"];
        for (var i = 0; i < itemPdfList.length; ++i) {
          var type = itemPdfList[i]["type"];
          var urlEn = itemPdfList[i]["url_en"];
          var urlTc = itemPdfList[i]["url_tc"];
          var urlSc = itemPdfList[i]["url_sc"];

          BuildItemPdfDiv(i, type, urlEn, urlTc, urlSc);
        }

        document.getElementById("divLotList").innerHTML = "";
        var lotList = jsonData["lot_list"];
        for (var i = 0; i < lotList.length; ++i) {
          BuildLotDiv(i, lotList[i]);
        }

        document.getElementById("btnAddLot").setAttribute("data-lot-count", lotList.length);
      }

      function BuildInspectionDateField(i, inspectionDateList) {
        var output = "";
        for(var i = 0; i < inspectionDateList.length; ++i) {
          var dayOfWeek = ["全部日子", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"];
          var curDate = inspectionDateList[i];

          output += "<div>";
          output += (1 <= curDate["day"] && curDate["day"] <= 7 ? dayOfWeek[curDate["day"]] : dayOfWeek[0]) + " " + curDate["start_time"] + " - " + curDate["end_time"] + "&nbsp;&nbsp;(" + curDate["typhoon_start_time"] + " - " + curDate["typhoon_end_time"] + ")";
          output += "<a href='#' style='text-decoration: none; margin-left: 6px; font-size: 12px'; onclick='DeleteInspectionDate(" + curDate["inspection_id"] + ");return false;'>❌</a>";
          output += "</div>";
        }

        return output;
      }

      function GetImageSearchKeyword(itemDesc, lang) {
        var bracketPos = itemDesc.indexOf("(");
        var bracket2Pos = itemDesc.indexOf("（");
        var commaPos = itemDesc.indexOf(",");
        var pos = (lang == "en" ? 255 : 50);

        if (bracketPos != -1 && bracket2Pos != -1 && commaPos != -1) {
          pos = Math.min(Math.min(bracketPos, bracket2Pos), commaPos);
        } else if (bracketPos != -1 && bracket2Pos != -1) {
          pos = Math.min(bracketPos, bracket2Pos);
        } else if (bracketPos != -1 && commaPos != -1) {
          pos = Math.min(bracketPos, commaPos);
        } else if (bracket2Pos != -1 && commaPos != -1) {
          pos = Math.min(bracket2Pos, commaPos);
        } else if (bracketPos != -1) {
          pos = bracketPos;
        } else if (bracket2Pos != -1) {
          pos = bracket2Pos;
        } else if (commaPos != -1) {
          pos = commaPos;
        }
        
        return itemDesc.substring(0, pos).trim();
      }

      function GetLotImage(i) {
        var itemLines = document.getElementById("tbItem_"+i+"_0").value.split("\n");
        var keywordEn = GetImageSearchKeyword(itemLines[0]).replaceAll("-", " ");
        var keywordTc = GetImageSearchKeyword(itemLines[1]).replaceAll("-", " ");
        
        var apiUrl = '../en/api/admin-getKeywordImageUrl-' + encodeURIComponent(keywordEn)+ '-' + encodeURIComponent(keywordTc);
        var xhr = new XMLHttpRequest();
        
        xhr.open("GET", apiUrl);
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            const jsonData = JSON.parse(this.responseText);
            if (jsonData && jsonData.length > 0) {
              var rndIndex = Math.floor(Math.random() * jsonData.length);
              document.getElementById("tbPhotoUrl_"+i).value = jsonData[rndIndex]['image_url'];
              document.getElementById("tbPhotoAuthor_"+i).value = jsonData[rndIndex]['author'];
              document.getElementById("tbPhotoAuthorUrl_"+i).value = jsonData[rndIndex]['author_url'];
            } else {
              alert(keywordEn + ", " + keywordTc + " image not found!");
            }
          }
        }

        xhr.send();
      }

      function CopyInfo(num) {
        var fieldList = [
          "tbGldRef_", "tbRef_",
          "tbDeptEn_", "tbDeptTc_", "tbDeptSc_", "tbContactEn_", "tbContactTc_", "tbContactSc_", 
          "tbNumberEn_", "tbNumberTc_", "tbNumberSc_", "tbLocationEn_", "tbLocationTc_", "tbLocationSc_"
        ];
          
        for (var i = 0; i < fieldList.length; ++i) {
          var prevFieldId = fieldList[i] + (num - 1);
          var curFieldId = fieldList[i] + num;
          if (document.getElementById(curFieldId).value.trim() == "") {
            document.getElementById(curFieldId).value = document.getElementById(prevFieldId).value;
          }
        }
      }

      function CopyTxtConditions(num, lang) {
        if (document.getElementById("tbItemConditionEn_" + num).value.trim() == "") return;

        var conditionListEn = document.getElementById("tbItemConditionEn_" + num).value.split("\n");
        var conditionOutput = [];

        for (var i = 0; i < conditionListEn.length; ++i) {
          var conditionEn = conditionListEn[i].trim().replaceAll(" /", "/").replaceAll("/ ", "/");
          for (var j = 0; j < conditionMapping.length; ++j) {
            if (conditionMapping[j]["en"] == conditionEn) {
              conditionOutput.push(conditionMapping[j][lang]);
            }
          }
        }

        document.getElementById("tbItemCondition" + (lang == "sc" ? "Sc" : "Tc") + "_" + num).value = conditionOutput.join("\n");
      }

      GetData(<?=$id?>, <?=$featuredOnly?"1":'0'?>, "<?=$type?>");
    </script>
  </div>
</body>
</html>