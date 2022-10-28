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
  <title>Admin - Push Message</title>
  <link rel="stylesheet" href="css/main.css">
  <style>
    table, th, td {
      border: 1px solid;
      border-collapse: collapse;
    }

    #tblPush tr:hover {
      background-color: #ddd;
    }

    #tblPush td {
      text-align: center;
    }

    #tblPush td.left {
      text-align: left;
    }

    #tblPush td.top {
      vertical-align: top;
    }

    .form-row {
      display: flex;
      justify-content: start;
    }

    .form-row > div {
      width: 470px;
    }

    .form-row input {
      width: 300px;
    }

    .form-row textarea {
      width: 450px;
      height: 150px;
      white-space: normal;
    }
  </style>
</head>
<body>
  <div class="header bgYellow">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Push Message</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <div style="margin:10px 0;text-decoration:underline">Create</div>
    <div class="form-row">
      <div>
        <div>Message (EN)</div>
        <div>
          <input id="tbTitleEn" class="long" placeholder="Title" />
          <textarea id="txtBodyEn" placeholder="Content"></textarea>
        </div>
      </div>
      <div>
        <div>Message (TC)</div>
        <div>
          <input id="tbTitleTc" class="long" placeholder="標題" />
          <textarea id="txtBodyTc" placeholder="內容"></textarea>
        </div>
      </div>
      <div>
        <div>Message (SC)</div>
        <div>
          <input id="tbTitleSc" class="long" placeholder="标题" />
          <textarea id="txtBodySc" placeholder="內容"></textarea>
        </div>
      </div>
    </div>
    <div style="margin-top: 15px">
      <button id="btnCreate" style="margin-right: 20px" onclick="CreatePush(5)">Send</button>
      <button id="btnConfirm" style="margin-right: 20px; display:none" onclick="ConfirmPush()">Confirm</button>
      <button onclick="ResetPush(true)">Reset</button>
    </div>
    <hr style="width: 1260px; margin-left: 0" />
    <table>
      <thead>
        <tr>
          <th style="width: 50px">ID</th>
          <th style="width: 300px">Content EN</th>
          <th style="width: 300px">Content TC</th>
          <th style="width: 300px">Content SC</th>
          <th style="width: 150px">Push Date</th>
          <th style="width: 80px">Status</th>
          <th style="width: 60px"></th>
        </tr>
      </thead>
      <tbody id="tblPush"></tbody>
    </table>
    <div style="margin-top: 10px">
      <input id="tbLoadMore" value="10" style="width:30px"/><button id="btnLoadMore" onclick="LoadMore()" data-start="-1" style="margin-left: 10px">Load More</button>
    </div>
    <button style="position: fixed; right: 20px; bottom: 60px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
    <button style="position: fixed; right: 20px; bottom: 20px; width:36px; height: 36px; font-size: 20px" onclick="window.scrollTo(0, document.body.scrollHeight)">⟱</button>
    <script>
      var pushList = [];
      var confirmTimeout;

      const statusMapping = {
        "P": "Pending",
        "I": "Sending",
        "S": "Sent",
        "F": "Failed",
        "X": "Cancelled",
      }

      function CreatePush(t) {
        if (
            document.getElementById("tbTitleEn").value.trim() == "" ||
            document.getElementById("txtBodyEn").value.trim() == "" ||
            document.getElementById("tbTitleTc").value.trim() == "" ||
            document.getElementById("txtBodyTc").value.trim() == "" ||
            document.getElementById("tbTitleSc").value.trim() == "" ||
            document.getElementById("txtBodySc").value.trim() == "") {
          alert("Please input all fields!");
          return;
        }

        document.getElementById("tbTitleEn").disabled = t > 0;
        document.getElementById("txtBodyEn").disabled = t > 0;
        document.getElementById("tbTitleTc").disabled = t > 0;
        document.getElementById("txtBodyTc").disabled = t > 0;
        document.getElementById("tbTitleSc").disabled = t > 0;
        document.getElementById("txtBodySc").disabled = t > 0;
        document.getElementById("btnConfirm").disabled = t > 3;

        if (t > 0) {
          document.getElementById("btnCreate").style.display = "none";
          document.getElementById("btnConfirm").style.display = "inline-block";
          document.getElementById("btnConfirm").innerHTML = "Confirm (" + t + ")";

          confirmTimeout = setTimeout(function() {
            CreatePush(t-1);
          }, 1000);
        } else {
          clearTimeout(confirmTimeout);
          document.getElementById("btnCreate").style.display = "inline-block";
          document.getElementById("btnConfirm").style.display = "none";
          document.getElementById("btnConfirm").innerHTML = "Confirm";
        }
      }

      function ConfirmPush() {
        clearTimeout(confirmTimeout);
        document.getElementById("btnConfirm").disabled = true;
        document.getElementById("btnConfirm").innerHTML = "Sending...";

        document.querySelectorAll('.resend-button').forEach(function(button) {
          button.disabled = true;
        });

        var push_password = prompt("Please enter password for push message:");
        var pushData = {
          "title_en": document.getElementById("tbTitleEn").value.trim(),
          "body_en": document.getElementById("txtBodyEn").value.trim(),
          "title_tc": document.getElementById("tbTitleTc").value.trim(),
          "body_tc": document.getElementById("txtBodyTc").value.trim(),
          "title_sc": document.getElementById("tbTitleSc").value.trim(),
          "body_sc": document.getElementById("txtBodySc").value.trim(),
          push_password: push_password,
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../en/api/admin-sendPush");
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
            if (this.status == 200) {
              const jsonData = JSON.parse(this.responseText);

              if (jsonData.status == "success") {
                ResetPush(true);
                GetData(false);
              } else {
                ResetPush(false);
                alert("Send push failed: " + jsonData.error);  
              }
            } else {
              alert("Error: " + this.responseText);
            }
          }
        };

        xhr.send(JSON.stringify(pushData));
      }

      function ResetPush(clearText) {
        clearTimeout(confirmTimeout);
        
        if (clearText) {
          document.getElementById("tbTitleEn").value = "";
          document.getElementById("txtBodyEn").value = "";
          document.getElementById("tbTitleTc").value = "";
          document.getElementById("txtBodyTc").value = "";
          document.getElementById("tbTitleSc").value = "";
          document.getElementById("txtBodySc").value = "";
        }

        document.getElementById("tbTitleEn").disabled = false;
        document.getElementById("txtBodyEn").disabled = false;
        document.getElementById("tbTitleTc").disabled = false;
        document.getElementById("txtBodyTc").disabled = false;
        document.getElementById("tbTitleSc").disabled = false;
        document.getElementById("txtBodySc").disabled = false;

        document.getElementById("btnCreate").disabled = false;
        document.getElementById("btnCreate").style.display = "inline-block";
        document.getElementById("btnConfirm").style.display = "none";

        document.querySelectorAll('.resend-button').forEach(function(button) {
          button.disabled = false;
        });
      }

      function CopyContent(btn) {
        var i = parseInt(btn.getAttribute("data-i"));

        document.getElementById("tbTitleEn").value = pushList[i].title_en;
        document.getElementById("txtBodyEn").value = pushList[i].body_en;
        document.getElementById("tbTitleTc").value = pushList[i].title_tc;
        document.getElementById("txtBodyTc").value = pushList[i].body_tc;
        document.getElementById("tbTitleSc").value = pushList[i].title_sc;
        document.getElementById("txtBodySc").value = pushList[i].body_sc;

        document.getElementById("tbTitleEn").focus();
      }

      function ResendPush(btn) {
        document.getElementById("btnCreate").disabled = true;
        document.getElementById("btnConfirm").disabled = true;
        document.querySelectorAll('.resend-button').forEach(function(button) {
          button.disabled = true;
        });

        var id = parseInt(btn.getAttribute("data-id"));
        var lang = btn.getAttribute("data-lang");

        var push_password = prompt("Please enter password for resend push message:");
        var pushData = {
          "push_id": id,
          "lang": lang,
          push_password: push_password,
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../en/api/admin-resendPush");
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
            if (this.status == 200) {
              const jsonData = JSON.parse(this.responseText);

              if (jsonData.status == "success") {
                ResetPush(true);
                GetData(false);
              } else {
                ResetPush(false);
                alert("Resend failed: " + jsonData.error);  
              }
            } else {
              alert("Error: " + this.responseText);
            }
          }
        };

        xhr.send(JSON.stringify(pushData));
      }

      function LoadMore(){
        var btnLoadMore = document.getElementById("btnLoadMore");
        var origStart = parseInt(btnLoadMore.getAttribute("data-start"));
        var pageSize = parseInt(document.getElementById("tbLoadMore").value);
        var start = origStart == -1 ? 0 : origStart + pageSize;

        GetData(true, start, pageSize);
        btnLoadMore.setAttribute("data-start", start);
      }

      function GetData(append, start, pageSize) {
        var apiUrl = "../en/api/admin-listPush-{0}-{1}";
        apiUrl = apiUrl.replace("{0}", start ? start : 0);
        apiUrl = apiUrl.replace("{1}", pageSize ? pageSize : parseInt(document.getElementById("tbLoadMore").value));

        var xhr = new XMLHttpRequest();
        
        xhr.open("GET", apiUrl);
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            var curPushList = JSON.parse(this.responseText);
            
            if (Array.isArray(curPushList)) {
              var tblPush = document.getElementById("tblPush");
              var btnLoadMore = document.getElementById("btnLoadMore");
              if (!append) {
                tblPush.innerHTML = "";
                btnLoadMore.removeAttribute("disabled");
                btnLoadMore.setAttribute("data-start", 0);
                pushList = curPushList;
              } else {
                pushList = pushList.concat(curPushList);
              }

              if (curPushList.length == 0) {
                btnLoadMore.setAttribute("disabled", "disabled");
              } else {           
                var itemStart = parseInt(btnLoadMore.getAttribute("data-start"));

                for (var i = 0; i < curPushList.length; ++i) {
                  const curPush = curPushList[i];
                  
                  var row = tblPush.insertRow();
                  row.insertCell(0).appendChild(document.createTextNode(curPush.id));

                  // EN
                  var divTitleEn = document.createElement("div");
                  divTitleEn.appendChild(document.createTextNode(curPush.title_en));
                  divTitleEn.style.fontWeight = "bold";
                  divTitleEn.style.marginBottom = "10px";
                  var td1 = row.insertCell(1)
                  td1.appendChild(divTitleEn);
                  td1.appendChild(document.createTextNode(curPush.body_en));
                  td1.appendChild(document.createElement("hr"));                  

                  var statusEn = document.createElement("span");
                  statusEn.appendChild(document.createTextNode("Status: " + curPush.status_en));
                  statusEn.setAttribute("title", curPush.result_en);
                  statusEn.style.color = curPush.status_en == "S" ? "#0a3" : "#d00";

                  td1.appendChild(statusEn);
                  td1.appendChild(document.createElement("br"));
                  td1.appendChild(document.createTextNode("Last Sent: " + curPush.last_sent_en));
                  td1.classList.add("left", "top");

                  // TC
                  var divTitleTc = document.createElement("div");
                  divTitleTc.appendChild(document.createTextNode(curPush.title_tc));
                  divTitleTc.style.fontWeight = "bold";
                  divTitleTc.style.marginBottom = "10px";
                  var td2 = row.insertCell(2)
                  td2.appendChild(divTitleTc);
                  td2.appendChild(document.createTextNode(curPush.body_tc));
                  td2.appendChild(document.createElement("hr"));
                  
                  var statusTc = document.createElement("span");
                  statusTc.appendChild(document.createTextNode("Status: " + curPush.status_tc));
                  statusTc.setAttribute("title", curPush.result_tc);
                  statusTc.style.color = curPush.status_tc == "S" ? "#0a3" : "#d00";

                  td2.appendChild(statusTc);
                  td2.appendChild(document.createElement("br"));
                  td2.appendChild(document.createTextNode("Last Sent: " + curPush.last_sent_tc));
                  td2.classList.add("left", "top");

                  // SC
                  var divTitleSc = document.createElement("div");
                  divTitleSc.appendChild(document.createTextNode(curPush.title_sc));
                  divTitleSc.style.fontWeight = "bold";
                  divTitleSc.style.marginBottom = "10px";
                  var td3 = row.insertCell(3)
                  td3.appendChild(divTitleSc);
                  td3.appendChild(document.createTextNode(curPush.body_sc));
                  td3.appendChild(document.createElement("hr"));
                  
                  var statusSc = document.createElement("span");
                  statusSc.appendChild(document.createTextNode("Status: " + curPush.status_sc));
                  statusSc.setAttribute("title", curPush.result_sc);
                  statusSc.style.color = curPush.status_sc == "S" ? "#0a3" : "#d00";

                  td3.appendChild(statusSc);
                  td3.appendChild(document.createElement("br"));
                  td3.appendChild(document.createTextNode("Last Sent: " + curPush.last_sent_sc));
                  td3.classList.add("left", "top");
                  
                  row.insertCell(4).appendChild(document.createTextNode(curPush.push_date));
                  row.insertCell(5).appendChild(document.createTextNode(statusMapping[curPush.status.toUpperCase()]));

                  var btnCopy = document.createElement("button");
                  btnCopy.appendChild(document.createTextNode("Copy"));
                  btnCopy.setAttribute("data-i", (i + itemStart));
                  btnCopy.setAttribute("style", "width:60px;height:40px;margin:10px 10px 30px 10px");
                  btnCopy.onclick = function () {
                    CopyContent(this);
                  }

                  var btnResendEn = document.createElement("button");
                  btnResendEn.classList.add("resend-button");
                  btnResendEn.appendChild(document.createTextNode("Resend (EN)"));
                  btnResendEn.setAttribute("data-id", curPush.id);
                  btnResendEn.setAttribute("data-lang", "en");
                  btnResendEn.setAttribute("style", "width:110px;height:25px;margin:0 10px 10px 10px");
                  btnResendEn.onclick = function () {
                    ResendPush(this);
                  }

                  var btnResendTc = document.createElement("button");
                  btnResendTc.classList.add("resend-button");
                  btnResendTc.appendChild(document.createTextNode("Resend (TC)"));
                  btnResendTc.setAttribute("data-id", curPush.id);
                  btnResendTc.setAttribute("data-lang", "tc");
                  btnResendTc.setAttribute("style", "width:110px;height:25px;margin:0 10px 10px 10px");
                  btnResendTc.onclick = function () {
                    ResendPush(this);
                  }

                  var btnResendSc = document.createElement("button");
                  btnResendSc.classList.add("resend-button");
                  btnResendSc.appendChild(document.createTextNode("Resend (SC)"));
                  btnResendSc.setAttribute("data-id", curPush.id);
                  btnResendSc.setAttribute("data-lang", "sc");
                  btnResendSc.setAttribute("style", "width:110px;height:25px;margin:0 10px 10px 10px");
                  btnResendSc.onclick = function () {
                    ResendPush(this);
                  }

                  var td6 = row.insertCell(6);
                  td6.appendChild(btnCopy);
                  td6.appendChild(btnResendEn);
                  td6.appendChild(btnResendTc);
                  td6.appendChild(btnResendSc);
                }
              }
            }
          }
        }
        
        xhr.send();
      }

      LoadMore();
    </script>
  </div>
</body>
</html>