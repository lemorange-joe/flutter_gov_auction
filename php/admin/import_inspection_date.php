<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/config.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Import Inspection Dates</title>
  <link rel="stylesheet" href="css/main.css?v=<?=$ADMIN_VERSION?>">
</head>
<body>
  <div class="header">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Import Inspection Dates</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <div>
      Auction Num: <input id="tbAuctionNum" style="width: 100px" placeholder="1/2023"/>
    </div>
    <div style="margin-top: 10px; text-decoration: underline">Dates</div>
    <div id="divInspectionDateForm"></div>
    <button id="btnAddInspectionDate" data-count="0" style="margin-top: 20px" onclick="AddInspectionDate()">Add</button>
    <hr />
    <div style="display: flex; justify-content: space-between; width: 150px; margin-top: 10px">
      <button id="btnSubmit" class="action-button" onclick="SubmitData()">Submit</button>
    </div>
    <script src="js/main.js?v=<?=$ADMIN_VERSION?>"></script>
    <script>
      function AddInspectionDate() {
        var btnAdd = document.getElementById("btnAddInspectionDate");
        var count = parseInt(btnAdd.getAttribute("data-count"));
        document.getElementById("divInspectionDateForm").appendChild(GetInspectionDateFormRow(count));

        btnAdd.setAttribute("data-count", count + 1);
      }
      
      function GetInspectionDateFormRow(i) {
        var output = "<div>";
          output += '<input id="tbInspectionLotNum_' + i + '" type="text" placeholder="C-401,C-402,UP-205,MS-123" style="width: 400px" onchange="AutoFormatInspectionDate(this)">';
          output += "&nbsp;&nbsp;";
            // must have day of week
            // output += '<input type="radio" id="rdbInspectionDay_0_' + i + '" name="rdbInspectionDay_' + i + '" value="0">';
            // output += '<label for="rdbInspectionDay_0_' + i + '">全部</label>';
            output += '<input type="radio" id="rdbInspectionDay_7_' + i + '" name="rdbInspectionDay_' + i + '" value="7" style="margin-left: 0px">';
            output += '<label for="rdbInspectionDay_7_' + i + '">日</label>';
            output += '<input type="radio" id="rdbInspectionDay_1_' + i + '" name="rdbInspectionDay_' + i + '" value="1" style="margin-left: 10px">';
            output += '<label for="rdbInspectionDay_1_' + i + '">一</label>';
            output += '<input type="radio" id="rdbInspectionDay_2_' + i + '" name="rdbInspectionDay_' + i + '" value="2" style="margin-left: 10px">';
            output += '<label for="rdbInspectionDay_2_' + i + '">二</label>';
            output += '<input type="radio" id="rdbInspectionDay_3_' + i + '" name="rdbInspectionDay_' + i + '" value="3" style="margin-left: 10px">';
            output += '<label for="rdbInspectionDay_3_' + i + '">三</label>';
            output += '<input type="radio" id="rdbInspectionDay_4_' + i + '" name="rdbInspectionDay_' + i + '" value="4" style="margin-left: 10px">';
            output += '<label for="rdbInspectionDay_4_' + i + '">四</label>';
            output += '<input type="radio" id="rdbInspectionDay_5_' + i + '" name="rdbInspectionDay_' + i + '" value="5" style="margin-left: 10px">';
            output += '<label for="rdbInspectionDay_5_' + i + '">五</label>';
            output += '<input type="radio" id="rdbInspectionDay_6_' + i + '" name="rdbInspectionDay_' + i + '" value="6" style="margin-left: 10px">';
            output += '<label for="rdbInspectionDay_6_' + i + '">六</label>';
          output += "&nbsp;&nbsp;";
          output += '<input id="tbInspectionStartTime_' + i + '" type="text" placeholder="09:30" maxlength="5" style="width: 60px" onchange="AutoFormatInspectionTime(this)">';
          output += " - ";
          output += '<input id="tbInspectionEndTime_' + i + '" type="text" placeholder="12:30" maxlength="5" style="width: 60px" onchange="AutoFormatInspectionTime(this)">';
          output += '&nbsp;&nbsp;（ ';
            output += '<input id="tbTyphoonStartTime_' + i + '" type="text" placeholder="--:--" maxlength="5" style="width: 60px" onchange="AutoFormatInspectionTime(this)">';
            output += " - ";
            output += '<input id="tbTyphoonEndTime_' + i + '" type="text" placeholder="--:--" maxlength="5" style="width: 60px" onchange="AutoFormatInspectionTime(this)">';
          output += ' ）';
        output += "<div>";

        var returnDiv = document.createElement("div");
        returnDiv.innerHTML = output;
        return returnDiv;
      }

      function AutoFormatInspectionDate(el) {
        el.value = el.value.replaceAll("and", ",").replaceAll("及", ",").replaceAll(/[^a-z\d\,\-]/ig, "");
      }

      function AutoFormatInspectionTime(el) {
        var text = el.value.replaceAll(/[^\d:-]/ig, "");
        if (text.length == 4) {
          text = text.substr(0, 2) + ":" + text.substr(2);
        }
        el.value = text;
      }

      function SubmitData() {
        TempDisableButton("btnSubmit");

        var auctionNum = document.getElementById("tbAuctionNum").value.trim();
        if (auctionNum == "") {
          alert("Auction Num empty!");
          return;
        }

        var auctionData = {
          "auction_num": auctionNum,
        };

        var i = 0;
        var inspectionList = [];
        while (document.getElementById("tbInspectionLotNum_"+i)) {
          var lotNums = document.getElementById("tbInspectionLotNum_"+i).value.trim();
          var selectedRdbDay = Array.prototype.slice.call(document.getElementsByName("rdbInspectionDay_" + i)).find((rdb) => rdb.checked);
          var inspectionDay = selectedRdbDay ? parseInt(selectedRdbDay.value) : 0;
          
          if (lotNums && inspectionDay > 0) {
            var inspectionDate = {
              "lot_nums": lotNums,
              "day": inspectionDay,
              "start_time": document.getElementById("tbInspectionStartTime_"+i).value,
              "end_time": document.getElementById("tbInspectionEndTime_"+i).value,
              "typhoon_start_time": document.getElementById("tbTyphoonStartTime_"+i).value,
              "typhoon_end_time": document.getElementById("tbTyphoonEndTime_"+i).value,
            };

            inspectionList.push(inspectionDate);
          }

          ++i;
        }
        auctionData.inspection_list = inspectionList;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../en/api/admin-submitInspectionDate");
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
  </div>
</body>
</html>