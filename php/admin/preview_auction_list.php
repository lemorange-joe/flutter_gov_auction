<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/enum.php");
include_once ("../class/admin_import.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Preview Auction List</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
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
      <div style="width:400px;display:flex;justify-content:space-between">
        <div>
        <span style="display:inline-block;width:100px">Item type:</span><input id="tbItemType" value="<?=$_POST["item_type"]?>" style="width:60px" />
        <br /><span style="display:inline-block;width:100px">Num:</span><input id="tbAuctionNum" value="<?=$_POST["auction_num"]?>" style="width:60px" />
        </div>
        <a href="import_auction_list.php">Cancel</a>
      </div>
      <hr />
      <?php
        $itemType = $_POST["item_type"];
        $importText = $_POST["import_text"];
        
        $adminImport = new AdminImport();
        $adminImport->parseData($itemType, $importText);
      ?>
      <div style="display: flex; justify-content: space-between; width: 150px; margin-top: 10px">
        <button id="btnImport" onclick="ImportData()">Import</button>
        <a href="import_auction_list.php">Cancel</a>
      </div>
      <button style="position: fixed; right: 20px; bottom: 60px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">▲</button>
      <button style="position: fixed; right: 20px; bottom: 20px; font-size: 20px" onclick="window.scrollTo(0, document.body.scrollHeight)">▼</button>
      <script>
        
        function CheckTextarea(id) {
          var itemNum = parseInt(id.substr(-1)) + 1;
          if (document.getElementById(id).value.split("\n").length != 5) {
            document.getElementById(id).style.backgroundImage = "url('https://dummyimage.com/250x100/f88/666.png&text=++++++" + itemNum + "')";
          } else {
            document.getElementById(id).style.backgroundImage = "url('https://dummyimage.com/250x100/fff/888.png&text=++++++" + itemNum + "')";
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
          var bgImage = 'url("https://dummyimage.com/250x100/fff/888.png&text=++++++' + (itemIndex + 1) + '")';
          var textareaHtml = "<textarea id='tbItem_" + lotIndex + "_" + itemIndex + "' style='width:250px;height:100px;background-image:" + bgImage + "'></textarea>";
          document.getElementById("divItems_"+lotIndex).insertAdjacentHTML("beforeend", textareaHtml);

          btnAdd.setAttribute("data-total", itemIndex+1);
        }

        function ImportData() {
          document.getElementById("btnImport").setAttribute("disabled", "disabled");
          setTimeout(function() {
            document.getElementById("btnImport").removeAttribute("disabled");
          }, 5000);

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
      </script>
    <?php
    }
    ?>
  </div>
</body>
</html>