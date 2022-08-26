<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Manage Auction</title>
  <link rel="stylesheet" href="css/main.css">
  <style>
    table, th, td {
      border: 1px solid;
      border-collapse: collapse;
    }

    #tblAuction tr:hover {
      background-color: #ddd;
    }

    #tblAuction td {
      text-align: center;
    }

    .form-row {
      display: flex;
    }

    .form-row div:first-child {
      width: 150px;
    }

    .form-row.separate {
      margin-top: 8px;
    }

    #newForm input {
      width: 130px;
    }

    #newForm input.long {
      width: 550px;
    }

    .highlight-text {
      animation: highlight-fade 5s 1;
    }

    @keyframes highlight-fade {
      0% { background-color: rgba(255,255,255,0); }
      20% { background-color: rgba(255,255,128,.9); }  
      1000% { background-color: rgba(255,255,255,0); }
    }
  </style>
</head>
<body>
  <div class="header bgBlue">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Auction List</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <table>
      <thead>
        <tr>
          <th style="width: 50px">ID</th>
          <th style="width: 100px">Auction No.</th>
          <th style="width: 100px">Start Time</th>
          <th style="width: 300px">Auction PDF</th>
          <th style="width: 300px">Result PDF</th>
          <th style="width: 150px">Auction Status</th>
          <th style="width: 80px">Status</th>
          <th style="width: 100px"></th>
        </tr>
      </thead>
      <tbody id="tblAuction">
      </tbody>
    </table>
    <hr />
    <div style="margin:10px 0;text-decoration:underline">New</div>
    <div id="newForm">
      <div class="form-row">
        <div>Auction No.</div>
        <div><input id="tbNewAuctionNum" /></div>
      </div>
      <div class="form-row">
        <div>Start Time</div>
        <div><input id="tbNewStartTime" placeholder="2022-08-11 10:30:00"/></div>
      </div>
      <div class="form-row separate">
        <div>Auction PDF (EN)</div>
        <div><input id="tbNewAuctionPdfEn" class="long" /></div>
      </div>
      <div class="form-row">
        <div>Auction PDF (TC)</div>
        <div><input id="tbNewAuctionPdfTc" class="long" /></div>
      </div>
      <div class="form-row">
        <div>Auction PDF (SC)</div>
        <div><input id="tbNewAuctionPdfSc" class="long" /></div>
      </div>
      <div class="form-row separate">
        <div>Result PDF (EN)</div>
        <div><input id="tbNewResultPdfEn" class="long" /></div>
      </div>
      <div class="form-row">
        <div>Result PDF (TC)</div>
        <div><input id="tbNewResultPdfTc" class="long" /></div>
      </div>
      <div class="form-row">
        <div>Result PDF (SC)</div>
        <div><input id="tbNewResultPdfSc" class="long" /></div>
      </div>
      <div class="form-row separate">
        <div>Remarks (EN)</div>
        <div><textarea id="tbNewRemarksEn" style="width:550px;height:45px"></textarea></div>
      </div>
      <div class="form-row">
        <div>Remarks (TC)</div>
        <div><textarea id="tbNewRemarksTc" style="width:550px;height:45px"></textarea></div>
      </div>
      <div class="form-row">
        <div>Remarks (SC)</div>
        <div><textarea id="tbNewRemarksSc" style="width:550px;height:45px"></textarea></div>
      </div>
      <div class="form-row separate">
        <div>Auction Status</div>
        <div>
          <select id="ddlNewAuctionStatus">
          <option value="P" selected>Pending</option>
          <option value="C">Confirmed</option>
          <option value="X">Cancelled</option>
          <option value="F">Finished</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div>Status</div>
        <div>
          <select id="ddlNewStatus">
            <option value="A">Active</option>
            <option value="I" selected>Inactive</option>
          </select>
        </div>
      </div>
    </div>
    <div style="margin-top: 10px">
      <button id="btnCreate" onclick="CreateAuction()">Create</button>&nbsp;&nbsp;&nbsp;&nbsp;
      <button onclick="ResetAuction()">Reset</button>
    </div>
    <button style="position: fixed; right: 20px; bottom: 20px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
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

        if (type == "AuctionStatus") {
          values = {
            "P": "Pending",
            "C": "Confirmed",
            "X": "Cancelled",
            "F": "Finished",
          };
        } else if (type == "Status") {
          values = {
            "A": "Active",
            "I": "Inactive",
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

        return select;
      }

      function GetTextBox(id, value, type, width) {
        var input = document.createElement("input");
        input.setAttribute("id", id);
        input.setAttribute("type", type);
        input.setAttribute("value", value);
        if (width) {
          input.style.width = width+"px";
        }
        return input;
      }

      function PostAuctionData(url, auctionData, highlightId) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", url);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
            if (this.status == 200) {
              const jsonData = JSON.parse(this.responseText);

              if (jsonData.status == "success") {
                GetData(highlightId);
                ResetAuction();
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

      function CreateAuction() {
        TempDisableButton("btnCreate");

        var auctionData = {
          "auction_num": document.getElementById("tbNewAuctionNum").value,
          "start_time": document.getElementById("tbNewStartTime").value,
          "auction_pdf_en": document.getElementById("tbNewAuctionPdfEn").value,
          "auction_pdf_tc": document.getElementById("tbNewAuctionPdfTc").value,
          "auction_pdf_sc": document.getElementById("tbNewAuctionPdfSc").value,
          "result_pdf_en": document.getElementById("tbNewResultPdfEn").value,
          "result_pdf_tc": document.getElementById("tbNewResultPdfTc").value,
          "result_pdf_sc": document.getElementById("tbNewResultPdfSc").value,
          "remarks_en": document.getElementById("tbNewRemarksEn").value,
          "remarks_tc": document.getElementById("tbNewRemarksTc").value,
          "remarks_sc": document.getElementById("tbNewRemarksSc").value,
          "auction_status": document.getElementById("ddlNewAuctionStatus").value,
          "status": document.getElementById("ddlNewStatus").value,
        };

        PostAuctionData("../en/api/admin-createAuction", auctionData);
      }

      function UpdateAuction(i) {
        var tr = document.getElementById("tblAuction").getElementsByTagName("tr")[i];
        var i = tr.getAttribute("data-index");

        if (!i) {
          alert('Index not found!');
          return;
        }

        var auctionData = {
          "id": document.getElementById("tbAuctionId_"+i).value,
          "auction_num": document.getElementById("tbAuctionNum_"+i).value,
          "start_time": document.getElementById("tbAuctionStartTime_"+i).value,
          "auction_pdf_en": document.getElementById("tbAuctionPdfEn_"+i).value,
          "auction_pdf_tc": document.getElementById("tbAuctionPdfTc_"+i).value,
          "auction_pdf_sc": document.getElementById("tbAuctionPdfSc_"+i).value,
          "result_pdf_en": document.getElementById("tbResultPdfEn_"+i).value,
          "result_pdf_tc": document.getElementById("tbResultPdfTc_"+i).value,
          "result_pdf_sc": document.getElementById("tbResultPdfSc_"+i).value,
          "remarks_en": document.getElementById("tbRemarksEn_"+i).value,
          "remarks_tc": document.getElementById("tbRemarksTc_"+i).value,
          "remarks_sc": document.getElementById("tbRemarksSc_"+i).value,
          "auction_status": document.getElementById("ddlAuctionStatus_"+i).value,
          "status": document.getElementById("ddlStatus_"+i).value,
        };

        PostAuctionData("../en/api/admin-updateAuction", auctionData, "divLastUpdate_"+i);
      }

      function ResetAuction() {
        document.getElementById("tbNewAuctionNum").value = "";
        document.getElementById("tbNewStartTime").value = "";
        document.getElementById("tbNewAuctionPdfEn").value = "";
        document.getElementById("tbNewAuctionPdfTc").value = "";
        document.getElementById("tbNewAuctionPdfSc").value = "";
        document.getElementById("tbNewResultPdfEn").value = "";
        document.getElementById("tbNewResultPdfTc").value = "";
        document.getElementById("tbNewResultPdfSc").value = "";
        document.getElementById("tbNewRemarksEn").value = "";
        document.getElementById("tbNewRemarksTc").value = "";
        document.getElementById("tbNewRemarksSc").value = "";
        document.getElementById("ddlNewAuctionStatus").value = "P";
        document.getElementById("ddlNewStatus").value = "I";
      }

      function GetData(highlightId) {
        var apiUrl = '../en/api/admin-listAuction';
        var xhr = new XMLHttpRequest();
        
        xhr.open("GET", apiUrl);
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            const jsonData = JSON.parse(this.responseText);
            
            if (Array.isArray(jsonData)) {
              var tblAuction = document.getElementById("tblAuction");
              tblAuction.innerHTML = "";
              
              for (var i = 0; i < jsonData.length; ++i) {
                const curAuction = jsonData[i];

                var link = document.createElement('a');
                link.appendChild(document.createTextNode(curAuction.id));
                link.href = "auction_details.php?id="+curAuction.id;

                var row = tblAuction.insertRow();
                row.setAttribute("data-index", i);
                var td0 = row.insertCell(0)
                td0.appendChild(link);
                td0.appendChild(GetTextBox("tbAuctionId_"+i, curAuction.id, "hidden"));
                row.insertCell(1).appendChild(GetTextBox("tbAuctionNum_"+i, curAuction.num, "text", 60));
                row.insertCell(2).appendChild(GetTextBox("tbAuctionStartTime_"+i, curAuction.start_time, "text", 130));              
                var td3 = row.insertCell(3);
                td3.appendChild(GetTextBox("tbAuctionPdfEn_"+i, curAuction.auction_pdf_en, "text", 550));
                td3.appendChild(GetTextBox("tbAuctionPdfTc_"+i, curAuction.auction_pdf_tc, "text", 550));
                td3.appendChild(GetTextBox("tbAuctionPdfSc_"+i, curAuction.auction_pdf_sc, "text", 550));
                var td4 = row.insertCell(4);
                td4.appendChild(GetTextBox("tbResultPdfEn_"+i, curAuction.result_pdf_en, "text", 550));
                td4.appendChild(GetTextBox("tbResultPdfTc_"+i, curAuction.result_pdf_tc, "text", 550));
                td4.appendChild(GetTextBox("tbResultPdfSc_"+i, curAuction.result_pdf_sc, "text", 550));
                row.insertCell(5).appendChild(GetDdl("ddlAuctionStatus_"+i, curAuction.auction_status, "AuctionStatus"));
                row.insertCell(6).appendChild(GetDdl("ddlStatus_"+i, curAuction.status, "Status"));
                var btnUpdate = document.createElement("button");
                btnUpdate.innerHTML = "Update";
                btnUpdate.onclick = function () {
                  UpdateAuction(this.parentNode.parentNode.rowIndex - 1);
                };
                row.insertCell(7).appendChild(btnUpdate);

                var row2 = tblAuction.insertRow();
                var cell = row2.insertCell(0);
                cell.setAttribute("colspan", 8);
                cell.setAttribute("style", "height:30px;vertical-align:top;text-align:left");

                  var divRemarks = document.createElement("div");
                  divRemarks.setAttribute("style", "display:flex;");

                    var divRemarksEn = document.createElement("div");
                    var txtRemarksEn = document.createElement("textarea");
                    txtRemarksEn.setAttribute("id", "tbRemarksEn_"+i);
                    txtRemarksEn.value = curAuction.remarks_en;
                    txtRemarksEn.style.width = "500px";
                    txtRemarksEn.style.height = "45px";
                    divRemarksEn.appendChild(document.createTextNode("Remarks EN"));
                    divRemarksEn.appendChild(document.createElement("br"));
                    divRemarksEn.appendChild(txtRemarksEn);

                    var divRemarksTc = document.createElement("div");
                    divRemarksTc.setAttribute("style", "margin:0 10px;");
                    var txtRemarksTc = document.createElement("textarea");
                    txtRemarksTc.setAttribute("id", "tbRemarksTc_"+i);
                    txtRemarksTc.value = curAuction.remarks_tc;
                    txtRemarksTc.style.width = "500px";
                    txtRemarksTc.style.height = "45px";
                    divRemarksTc.appendChild(document.createTextNode("Remarks TC"));
                    divRemarksTc.appendChild(document.createElement("br"));
                    divRemarksTc.appendChild(txtRemarksTc);

                    var divRemarksSc = document.createElement("div");
                    var txtRemarksSc = document.createElement("textarea");
                    txtRemarksSc.setAttribute("id", "tbRemarksSc_"+i);
                    txtRemarksSc.value = curAuction.remarks_sc;
                    txtRemarksSc.style.width = "500px";
                    txtRemarksSc.style.height = "45px";
                    divRemarksSc.appendChild(document.createTextNode("Remarks SC"));
                    divRemarksSc.appendChild(document.createElement("br"));
                    divRemarksSc.appendChild(txtRemarksSc);

                  divRemarks.appendChild(divRemarksEn);
                  divRemarks.appendChild(divRemarksTc);
                  divRemarks.appendChild(divRemarksSc);

                  var divCount = document.createElement("div");
                  divCount.style.height = "30px";
                  divCount.style.lineHeight = "30px";
                  divCount.appendChild(document.createTextNode("📊 " + curAuction.lot_count));

                  var divLastUpdate = document.createElement("div");
                  divLastUpdate.setAttribute("id", "divLastUpdate_"+i);
                  divLastUpdate.appendChild(document.createTextNode("🗓 " + curAuction.last_update));

                  var importItemLink = document.createElement('a');
                  importItemLink.appendChild(document.createTextNode("Import Items"));
                  importItemLink.href = "import_auction_list.php?auction_num=" + encodeURIComponent(curAuction.num);

                  var importResultLink = document.createElement('a');
                  importResultLink.appendChild(document.createTextNode("Import Result"));
                  importResultLink.href = "import_auction_result.php?auction_num=" + encodeURIComponent(curAuction.num);

                  var divLinks = document.createElement("div");
                  divLinks.style.lineHeight = "40px";
                  divLinks.appendChild(importItemLink);
                  divLinks.appendChild(document.createTextNode("\u00A0\u00A0\u00A0\u00A0"));
                  divLinks.appendChild(importResultLink);
                
                cell.appendChild(divRemarks);
                cell.appendChild(divCount);
                cell.appendChild(divLastUpdate);
                cell.appendChild(divLinks);
              }

              if (highlightId) {
                document.getElementById(highlightId).classList.add("highlight-text");
              }
            }
          }
        }
        
        xhr.send();
      }

      GetData();
    </script>
  </div>
</body>
</html>