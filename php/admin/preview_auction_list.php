<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/config.php");
include_once ("../include/enum.php");
include_once ("../class/admin_import.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Preview Auction List</title>
  <link rel="stylesheet" href="css/main.css?v=<?=$ADMIN_VERSION?>">
</head>
<body onkeydown="return CheckShortcut()">
  <div class="header">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Import Auction List (2/2)</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <?php
    if (!isset($_POST["item_type"]) || !isset($_POST["auction_num"]) || empty($_POST["item_type"])|| empty($_POST["auction_num"])) {
    ?>
      Missing data!
      <br /><br />
      <a href="import_auction_list.php">Back</a>
    <?php
    } else {
    ?>
      <div style="width:350px;display:flex;justify-content:space-between">
        <div>
        <span style="display:inline-block;width:100px">Item type:</span><input id="tbItemType" value="<?=$_POST["item_type"]?>" style="width:60px" />
        <br /><span style="display:inline-block;width:100px">Num:</span><input id="tbAuctionNum" value="<?=$_POST["auction_num"]?>" style="width:60px" />
        </div>
        <a href="import_auction_list.php" style="line-height: 40px">Cancel</a>
      </div>
      <hr />
      <?php
        $itemType = $_POST["item_type"];
        $importText = $_POST["import_text"];
        
        $adminImport = new AdminImport();
        $adminImport->parseData($itemType, $importText);
      ?>
      <div style="display: flex; justify-content: space-between; width: 150px; margin-top: 10px">
        <button id="btnImport" class="action-button" onclick="ImportData()">Import</button>
        <a href="import_auction_list.php" style="line-height: 28px">Cancel</a>
      </div>
      
      <button id="btnNextEmptyData" style="position: fixed; right: 20px; bottom: 232px; width:36px; height: 36px; font-size: 18px; background-color: #ffbd88; color: #fff; border: solid 1px #888; border-radius: 8px;" onclick="FindNextEmptyData()"  onmouseover="this.style.backgroundColor='#ffc999'" onmouseout="this.style.backgroundColor='#ffbd88'">∅</button>
      <button id="btnNextErrorItem" style="position: fixed; right: 20px; bottom: 190px; width:36px; height: 36px; font-size: 18px; background-color: #f88; color: #fff; border: solid 1px #888" onclick="FindNextErrorItem()"  onmouseover="this.style.backgroundColor='#f99'" onmouseout="this.style.backgroundColor='#f88'">⮕</button>
      <button style="position: fixed; right: 20px; bottom: 140px; width:36px; height: 36px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
      <button style="position: fixed; right: 20px; bottom: 100px; width:36px; height: 36px; font-size: 20px" onclick="JumpScroll(-100)" onmouseover="AutoScroll(-12)" onmouseout="StopScroll()">▲</button>
      <button style="position: fixed; right: 20px; bottom: 60px; width:36px; height: 36px; font-size: 20px" onclick="JumpScroll(100)" onmouseover="AutoScroll(12)" onmouseout="StopScroll()">▼</button>
      <button style="position: fixed; right: 20px; bottom: 20px; width:36px; height: 36px; font-size: 20px" onclick="window.scrollTo(0, document.body.scrollHeight)">⟱</button>
      <script src="js/main.js?v=<?=$ADMIN_VERSION?>"></script>
      <script>
        function CheckShortcut() {
          if (event.target.type != "text" && event.target.type != "textarea") {
            if (event.keyCode == 49) {  // "1"
              FindNextEmptyData();
              return false;
            } else if (event.keyCode == 50) {  // "2"
              FindNextErrorItem();
              return false;
            }
          }

          return true;
        }

        function FindNextEmptyData() {
          var i = 0;
          var total = 0;
          var firstEmptyId = "";
          var checkFieldList = ["tbGldRef", "tbRef", "tbDeptEn", "tbDeptTc", "tbContactEn", "tbContactTc", "tbNumberEn", "tbNumberTc", "tbLocationEn", "tbLocationTc"];
          var checkFieldPairList = ["tbRemarks", "tbItemCondition"];

          while (document.getElementById(checkFieldList[0]+"_"+i)) {  // if there is lot i, check its fields
            for (var j = 0; j < checkFieldList.length; ++j) {
              if (document.getElementById(checkFieldList[j]+"_"+i).value.trim() == "") {
                firstEmptyId = firstEmptyId == "" ? checkFieldList[j]+"_"+i : firstEmptyId;
                ++total;
                break;
              }
            }

            ++i;
          }

          i = 0;
          while (document.getElementById(checkFieldPairList[0]+"En_"+i)) {
            var enFieldId = checkFieldPairList[0]+"En_"+i;
            var tcFieldId = checkFieldPairList[0]+"Tc_"+i;
            if (document.getElementById(enFieldId).value.trim() != "" && document.getElementById(tcFieldId).value.trim() == "") {
              firstEmptyId = firstEmptyId == "" ? tcFieldId : firstEmptyId;
              ++total;
            } else if (document.getElementById(enFieldId).value.trim() == "" && document.getElementById(tcFieldId).value.trim() != "") {
              firstEmptyId = firstEmptyId == "" ? enFieldId : firstEmptyId;
              ++total;
            }
            ++i;
          }

          document.getElementById("btnNextEmptyData").innerHTML = total;
          if (total > 0) {
            document.getElementById(firstEmptyId).focus();
          } else {
            alert("No more empty data!");
          }
        }

        function FindNextErrorItem() {
          var total = document.getElementsByClassName("auction-item-textarea wrong").length;
          document.getElementById("btnNextErrorItem").innerHTML = total;
          if (total > 0) {
            document.getElementsByClassName("auction-item-textarea wrong")[0].focus();
          } else {
            alert("No more error item!");
          }
        }

        function CheckTextarea(id) {
          var itemNum = parseInt(id.substr(-1)) + 1;
          var textList = document.getElementById(id).value.split("\n");

          // item correct conditions:
          // 1. 5 lines;
          // 2. 3rd line (i.e. quantity) is a number; nd
          // 3. 5th line (i.e. unit in Chinese) has <= 6 characters
          if (textList.length != 5 || !(/^\d*\.?\d+$/.test(textList[2].trim())) || textList[4].trim().length > 6) {
            document.getElementById(id).classList.add("wrong");
          } else {
            document.getElementById(id).classList.remove("wrong");
          }
        }

        function FixTextarea(id, lang) {
          var textarea = document.getElementById(id);
          var contentList = textarea.value.split("\n");
          var origContent = textarea.getAttribute("data-orig-content");
          
          if (lang == "undo") {
            
            if (!origContent) {
              alert("The textarea was not edited, cannot undo!");
              return;
            }

            textarea.value = origContent;
            CheckTextarea(id);
            return;
          }

          if (contentList.length <= 5) {
            alert("Number of lines <= 5, cannot update!")
            return;
          }

          if (!origContent) {
            // original content is empty, set it in the data attribute for future undo
            textarea.setAttribute("data-orig-content", textarea.value);
          }

          var line6 = contentList.splice(5, 1); // remove the 6th line

          if (lang == "en") {
            contentList[0] += " " + line6;
          } else if (lang == "tc") {
            contentList[1] += line6;
          }

          textarea.value = contentList.join("\n");
          CheckTextarea(id);
        }


        function AddItem(lotIndex) {
          var btnAdd = document.getElementById("btnAddItem_" + lotIndex);
          var itemIndex = parseInt(btnAdd.getAttribute("data-total"));
          var className = "auction-item-textarea item" + String(itemIndex + 1).padStart(2, "0");
          var textareaHtml = "<textarea id='tbItem_" + lotIndex + "_" + itemIndex + "' class='" + className + "' style='width:250px;height:100px;margin-bottom:17px'></textarea>";
          document.getElementById("divItems_"+lotIndex).insertAdjacentHTML("beforeend", textareaHtml);
          document.getElementById("tbItem_" + lotIndex + "_" + itemIndex).focus();

          btnAdd.setAttribute("data-total", itemIndex+1);
        }

        function ImportData() {
          TempDisableButton("btnImport");

          var i = 0;
          var auctionData = {
            "type": document.getElementById("tbItemType").value,
            "auction_num": document.getElementById("tbAuctionNum").value,
            "lots": [],
          };

          while (document.getElementById("tbLotNum_"+i)) {
            var j = 0;
            var itemList = [];
            while (document.getElementById("tbItem_"+i+"_"+j)) {
              var itemContent = document.getElementById("tbItem_"+i+"_"+j).value.trim();
              if (itemContent != "") {
                var itemLines = itemContent.split("\n");
                
                itemList.push({
                  "description_en": itemLines.length > 0 ? itemLines[0] : "",
                  "description_tc": itemLines.length > 1 ? itemLines[1] : "",
                  "quantity": itemLines.length > 2 ? itemLines[2] : "",
                  "unit_en": itemLines.length > 3 ? itemLines[3] : "",
                  "unit_tc": itemLines.length > 4 ? itemLines[4] : "",
                });
              }

              ++j;
            }
            
            var curLot = {
              "lot_num": document.getElementById("tbLotNum_"+i).value,
              "gld_file_ref": document.getElementById("tbGldRef_"+i).value,
              "ref": document.getElementById("tbRef_"+i).value,
              "dept_en": document.getElementById("tbDeptEn_"+i).value,
              "dept_tc": document.getElementById("tbDeptTc_"+i).value,
              "contact_en": document.getElementById("tbContactEn_"+i).value,
              "contact_tc": document.getElementById("tbContactTc_"+i).value,
              "number_en": document.getElementById("tbNumberEn_"+i).value,
              "number_tc": document.getElementById("tbNumberTc_"+i).value,
              "location_en": document.getElementById("tbLocationEn_"+i).value,
              "location_tc": document.getElementById("tbLocationTc_"+i).value,
              "remarks_en": document.getElementById("tbRemarksEn_"+i).value,
              "remarks_tc": document.getElementById("tbRemarksTc_"+i).value,
              "item_condition_en": document.getElementById("tbItemConditionEn_"+i).value,
              "item_condition_tc": document.getElementById("tbItemConditionTc_"+i).value,
              "items": itemList,
            };

            auctionData.lots.push(curLot);
            
            ++i;
          }

          var xhr = new XMLHttpRequest();
          xhr.open("POST", "../en/api/admin-importAuction");
          xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
          xhr.onreadystatechange = function () {
            if (this.readyState == 4) {
              if (this.status == 200) {
                const jsonData = JSON.parse(this.responseText);

                if (jsonData.status == "success") {
                  window.location = "auction_details.php?id=" + jsonData.data.id;
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

        function CopyInfo(num) {
          var fieldList = [
            "tbGldRef_", "tbRef_",
            "tbDeptEn_", "tbDeptTc_", "tbContactEn_", "tbContactTc_", 
            "tbNumberEn_", "tbNumberTc_", "tbLocationEn_", "tbLocationTc_"
          ];
            
          for (var i = 0; i < fieldList.length; ++i) {
            var prevFieldId = fieldList[i] + (num - 1);
            var curFieldId = fieldList[i] + num;
            if (document.getElementById(curFieldId).value.trim() == "") {
              document.getElementById(curFieldId).value = document.getElementById(prevFieldId).value;
            }
          }
        }
      </script>
    <?php
    }
    ?>
  </div>
</body>
</html>