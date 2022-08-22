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
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <div style="float: left"><h2><a href="index.php">« Admin Index</a></h2>&nbsp;&nbsp;<h2><a href="auction_list.php">« Auction List</a></h2></div>
  <div style="float:right"><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  <hr style="clear: both"/>
  <a href="auction_list.php">Auction List</a>&nbsp;&nbsp;
  <select id="ddlAuctionType" onchange="GetData(<?=$id?>, this.value)">
    <option value="" <?=$type=="" ? "selected" : ""?>>All</option>
    <option value="<?=ItemType::ConfiscatedGoods?>" <?=$type==ItemType::ConfiscatedGoods ? "selected" : ""?>>[<?=ItemType::ConfiscatedGoods?>] 充公物品</option>
    <option value="<?=ItemType::UnclaimedProperties?>" <?=$type==ItemType::UnclaimedProperties ? "selected" : ""?>>[<?=ItemType::UnclaimedProperties?>] 無人認領物品</option>
    <option value="<?=ItemType::UnserviceableStores?>" <?=$type==ItemType::UnserviceableStores ? "selected" : ""?>>[<?=ItemType::UnserviceableStores?>] 廢棄物品及剩餘物品</option>
    <option value="<?=ItemType::SurplusServiceableStores?>" <?=$type==ItemType::SurplusServiceableStores ? "selected" : ""?>>[<?=ItemType::SurplusServiceableStores?>] 仍可使用之廢棄物品及剩餘物品</option>
  </select>
  <div style="width: 300px; display: flex; justify-content: space-between; border: solid 1px #000; margin-top: 10px; padding: 5px;">
    <div>ID: <input id="tbAuctionId" style="width: 30px" type="text" disabled="disabled"/></div>
    <div>|</div>
    <div id="divAuctionNum" style="font-weight: bold"></div>
    <div>|</div>
    <div id="divStartTime" style="text-decoration: underline"></div>
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

  <hr />
  Item PDF:
  <div id="divItemPdfList" style="display: flex; flex-wrap: wrap; width: 1200px"></div>
  <div style="margin-top: 10px">
    <button id="btnAddItemPdf" data-item-pdf-count="0" onclick="AddItemPdf()">+ PDF</button>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <button id="btnSaveItemPdf" onclick="SaveItemPdf()">Save PDF</button>
  </div>

  <hr />

  <div id="divLotList" style="width: 1600px"></div>
  <div style="margin-top: 10px">
    <button id="btnAddLot" data-lot-count="0" onclick="AddLot()">+ Lot</button>
  </div>

  <button style="position: fixed; right: 20px; bottom: 20px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">▲</button>

  <script>
    function TempDisableButton(id) {
      document.getElementById(id).setAttribute("disabled", "disabled");
      setTimeout(function() {
        document.getElementById(id).removeAttribute("disabled");
      }, 5000);
    }

    function GetDdl(id, selectedValue, type) {
      var select = document.createElement("select");
      var option;
      var values;

      if (type == "TransactionStatus") {
        values = {
          "N": "Not Sold",
          "S": "Sold",
        };
      } else if (type == "Status") {
        values = {
          "A": "Active",
          "I": "Inactive",
        };
      } else if (type == "ItemType") {
        values = {
          "<?=ItemType::ConfiscatedGoods?>": "[<?=ItemType::ConfiscatedGoods?>] 充公物品",
          "<?=ItemType::UnclaimedProperties?>": "[<?=ItemType::UnclaimedProperties?>] 無人認領物品",
          "<?=ItemType::UnserviceableStores?>": "[<?=ItemType::UnserviceableStores?>] 廢棄物品及剩餘物品",
          "<?=ItemType::SurplusServiceableStores?>": "[<?=ItemType::SurplusServiceableStores?>] 仍可使用之廢棄物品及剩餘物品",
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

    function GetData(auctionId, type)
    {
      var apiUrl = '../en/api/admin-getAuction?id=' + auctionId + "&type=" + type;
      var xhr = new XMLHttpRequest();
      
      xhr.open("GET", apiUrl);
      xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const jsonData = JSON.parse(this.responseText);
          BuildDetails(jsonData);
        }

        document.getElementById("tbAuctionId").value = auctionId;
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
              GetData(document.getElementById("tbAuctionId").value, document.getElementById("ddlAuctionType").value);
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
        selected = (type == "<?=ItemType::ConfiscatedGoods?>") ? "selected" : "";
        divHtml += "<option value='<?=ItemType::ConfiscatedGoods?>'" + selected + ">[<?=ItemType::ConfiscatedGoods?>] 充公物品</option>";
        selected = (type == "<?=ItemType::UnclaimedProperties?>") ? "selected" : "";
        divHtml += "<option value='<?=ItemType::UnclaimedProperties?>'" + selected + ">[<?=ItemType::UnclaimedProperties?>] 無人認領物品</option>";
        selected = (type == "<?=ItemType::UnserviceableStores?>") ? "selected" : "";
        divHtml += "<option value='<?=ItemType::UnserviceableStores?>'" + selected + ">[<?=ItemType::UnserviceableStores?>] 廢棄物品及剩餘物品</option>";
        selected = (type == "<?=ItemType::SurplusServiceableStores?>") ? "selected" : "";
        divHtml += "<option value='<?=ItemType::SurplusServiceableStores?>'" + selected + ">[<?=ItemType::SurplusServiceableStores?>] 仍可使用之廢棄物品及剩餘物品</option>";
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

    function SaveLot(lotIndex) {
      var auctionId = document.getElementById("tbAuctionId").value;
      var lotId = document.getElementById("tbLotId_"+lotIndex).value;
      var itemCode = document.getElementById("ddlItemType_"+lotIndex).value;
      var lotNum = document.getElementById("tbLotNum_"+lotIndex).value;
      var seq = document.getElementById("tbSeq_"+lotIndex).value;
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
      var lotIcon = document.getElementById("tbLotIcon_"+lotIndex).value;
      var photoUrl = document.getElementById("tbPhotoUrl_"+lotIndex).value;
      var photoReal = document.getElementById("chkPhotoReal_"+lotIndex).checked ? 1 : 0;
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
        seq: seq,
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
        lot_icon: lotIcon,
        photo_url: photoUrl,
        photo_real: photoReal,
        transaction_currency: tranCurrency,
        transaction_price: tranPrice,
        transaction_status: tranStatus,
        status: status,
        item_list: itemList
      };

      console.log(lotData);
      
      TempDisableButton("btnSaveLot_"+lotIndex);
      PostData("../en/api/admin-updateAuctionLot", lotData);
    }

    function BuildLotDiv(i, lotData) {
      var lotId = 0;
      var itemType = "";
      var lotNum = "";
      var seq = i+1;
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

		  var lotIcon = "fontawesome.box";
		  var photoUrl = "";
      var photoReal = false;
      var transactionCurrency = "HKD";
      var transactionPrice = 0;
      var transactionStatus = "N";
      var status = "A";
      var lastUpdate = "";
      var itemList = [];

      if (lotData){
        lotId = lotData["lot_id"];
        itemType = lotData["item_type"];
        lotNum = lotData["lot_num"];
        seq = lotData["seq"];
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
        remarksEn = lotData["remarks_en"];
        remarksTc = lotData["remarks_tc"];
        remarksSc = lotData["remarks_sc"];

        lotIcon = lotData["lot_icon"];
        photoUrl = lotData["photo_url"];
        photoReal = lotData["photo_real"];
        transactionCurrency = lotData["transaction_currency"];
        transactionPrice = lotData["transaction_price"];
        transactionStatus = lotData["transaction_status"];
        status = lotData["status"];
        lastUpdate = lotData["last_update"];
        itemList = lotData["item_list"];
      }

      var divHtml = "<div>";
      
      divHtml += "<div style='width:900px; float: left'>";
        divHtml += "<div style='display:flex'><div style='width:100px'>ID</div><input id='tbLotId_" + i + "' style='width:50px; margin-right: 60px' disabled='disabled' value='" + lotId + "'>";
        divHtml += GetDdl("ddlItemType_"+i, itemType, "ItemType");
        divHtml += "</div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Lot Num</div><input id='tbLotNum_" + i + "' style='width:100px' value='" + lotNum.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Seq</div><input id='tbSeq_" + i + "' style='width:100px' value='" + seq + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>GLD Ref</div><input id='tbGldRef_" + i + "' style='width:200px' value='" + gldFileRef.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Ref</div><input id='tbRef_" + i + "' style='width:200px' value='" + reference.replace("'", '"') + "'></div>";
        divHtml += "<div style='height:10px'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Dept</div><input id='tbDeptEn_" + i + "' style='width:750px' value='" + departmentEn.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>部門</div><input id='tbDeptTc_" + i + "' style='width:750px' value='" + departmentTc.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>部门</div><input id='tbDeptSc_" + i + "' style='width:750px' value='" + departmentSc.replace("'", '"') + "'></div>";
        divHtml += "<div style='height:10px'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Contact</div><input id='tbContactEn_" + i + "' style='width:750px' value='" + contactEn.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>聯絡人</div><input id='tbContactTc_" + i + "' style='width:750px' value='" + contactTc.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>联络人</div><input id='tbContactSc_" + i + "' style='width:750px' value='" + contactSc.replace("'", '"') + "'></div>";
        divHtml += "<div style='height:10px'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Number</div><input id='tbNumberEn_" + i + "' style='width:750px' value='" + numberEn.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>電話</div><input id='tbNumberTc_" + i + "' style='width:750px' value='" + numberTc.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>电话</div><input id='tbNumberSc_" + i + "' style='width:750px' value='" + numberSc.replace("'", '"') + "'></div>";
        divHtml += "<div style='height:10px'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Location</div><input id='tbLocationEn_" + i + "' style='width:750px' value='" + locationEn.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>地點</div><input id='tbLocationTc_" + i + "' style='width:750px' value='" + locationTc.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>地点</div><input id='tbLocationSc_" + i + "' style='width:750px' value='" + locationSc.replace("'", '"') + "'></div>";
        divHtml += "<div style='height:10px'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Status</div>" + GetDdl("ddlStatus_"+i, status, "Status") + "</div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Last Update:</div><div>" + lastUpdate + "</div></div>";
      divHtml += "</div>";
      divHtml += "<div style='width:700px;float: right;'>";
        divHtml += "<div style='height:20px'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Lot Icon</div><input id='tbLotIcon_" + i + "' value='" + lotIcon.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Photo URL</div><input id='tbPhotoUrl_" + i + "' style='width:590px' value='" + photoUrl.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Photo Real</div><input id='chkPhotoReal_" + i + "' type='checkbox' " + (photoReal ? "checked" : "") + "></div>";
        divHtml += "<div style='height:10px'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Currency</div><input id='tbTranCurrency" + i + "' value='" + transactionCurrency.replace("'", '"') + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Price</div><input id='tbTranPrice_" + i + "' value='" + transactionPrice + "'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>T. Status</div>" + GetDdl("ddlTranStatus_"+i, transactionStatus, "TransactionStatus") + "</div>";
        divHtml += "<div style='height:10px'></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>Remarks</div>";
        divHtml += "<textarea id='tbRemarksEn_" + i + "' style='width:600px;height:60px'>" + remarksEn + "</textarea></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>注意</div>";
        divHtml += "<textarea id='tbRemarksTc_" + i + "' style='width:600px;height:60px'>" + remarksTc + "</textarea></div>";
        divHtml += "<div style='display:flex'><div style='width:100px'>注意 (SC)</div>";
        divHtml += "<textarea id='tbRemarksSc_" + i + "' style='width:600px;height:60px'>" + remarksSc + "</textarea></div>";
      divHtml += "</div>";
      
      divHtml += "<br style='clear: both' />";
      divHtml += "<div style='height:10px'></div>";
      divHtml += "<div id='divItems_" + i + "' style='width:1500px'>";
      for (var j = 0; j < itemList.length; ++j) {
        divHtml += BuildLotItems(i, j, itemList[j]);
      }
      divHtml += "</div>"

      divHtml += "<div><button id='btnAddItem_" + i + "' data-lot-index='" + i + "' data-total='" + itemList.length + "' onclick='AddItem(" + i + ")'>+ Item</button></div>";
      divHtml += "<div><button id='btnSaveLot_" + i + "' onclick='SaveLot(" + i + ")' style='width: 80px;height: 30px;margin-top: 15px'>Save Lot</button></div>";

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
      

      var bgImage = 'url("https://dummyimage.com/250x120/fff/888.png&text=++++++' + (itemIndex + 1) + '")';
      var textareaHtml = "<div style='display: inline-block'>";
      textareaHtml += "Icon: <input id='tbItemIcon_" + lotIndex + "_" + itemIndex + "' value='" + itemIcon + "' /><br/>";
      textareaHtml += "<textarea id='tbItem_" + lotIndex + "_" + itemIndex + "' style='width:250px;height:120px;background-image:" + bgImage + "'>";
      textareaHtml += textareaContent;
      textareaHtml += "</textarea>";
      textareaHtml += "</div>";

      return textareaHtml
    }

    function BuildDetails(jsonData) {
      document.getElementById("tbAuctionId").value = jsonData["auction_id"];
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

    GetData(<?=$id?>, "<?=$type?>");
  </script>
</body>
</html>