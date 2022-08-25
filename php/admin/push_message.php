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
      width: 500px;
    }

    .form-row input {
      width: 300px;
    }

    .form-row textarea {
      width: 480px;
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
    <div style="margin-top: 10px">
      <button id="btnCreate" style="margin-right: 20px" onclick="CreatePush(5)">Create</button>
      <button id="btnConfirm" style="margin-right: 20px; display:none" onclick="ConfirmPush()">Confirm</button>
      <button onclick="ResetPush(true)">Reset</button>
    </div>
    <hr style="width: 75%; margin-left: 0" />
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
    <button style="position: fixed; right: 20px; bottom: 20px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
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
          alert("Data empty!");
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
        }
      }

      function ConfirmPush() {
        clearTimeout(confirmTimeout);

        document.getElementById("btnConfirm").disabled = true;

        var pushData = {
          "title_en": document.getElementById("tbTitleEn").value.trim(),
          "body_en": document.getElementById("txtBodyEn").value.trim(),
          "title_tc": document.getElementById("tbTitleTc").value.trim(),
          "body_tc": document.getElementById("txtBodyTc").value.trim(),
          "title_sc": document.getElementById("tbTitleSc").value.trim(),
          "body_sc": document.getElementById("txtBodySc").value.trim()
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
                GetData();
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

        document.getElementById("btnCreate").style.display = "inline-block";
        document.getElementById("btnConfirm").style.display = "none";
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

      function GetData() {
        var apiUrl = "../en/api/admin-listPush";
        var xhr = new XMLHttpRequest();
        
        xhr.open("GET", apiUrl);
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            pushList = JSON.parse(this.responseText);
            
            if (Array.isArray(pushList)) {
              var tblPush = document.getElementById("tblPush");
              tblPush.innerHTML = "";
              
              for (var i = 0; i < pushList.length; ++i) {
                const curPush = pushList[i];
                
                var row = tblPush.insertRow();
                row.insertCell(0).appendChild(document.createTextNode(curPush.id));

                var divTitleEn = document.createElement("div");
                divTitleEn.appendChild(document.createTextNode(curPush.title_en));
                divTitleEn.style.fontWeight = "bold";
                divTitleEn.style.marginBottom = "10px";
                var td1 = row.insertCell(1)
                td1.appendChild(divTitleEn);
                td1.appendChild(document.createTextNode(curPush.body_en));
                td1.classList.add("left", "top");

                var divTitleTc = document.createElement("div");
                divTitleTc.appendChild(document.createTextNode(curPush.title_tc));
                divTitleTc.style.fontWeight = "bold";
                divTitleTc.style.marginBottom = "10px";
                var td2 = row.insertCell(2)
                td2.appendChild(divTitleTc);
                td2.appendChild(document.createTextNode(curPush.body_tc));
                td2.classList.add("left", "top");

                var divTitleSc = document.createElement("div");
                divTitleSc.appendChild(document.createTextNode(curPush.title_sc));
                divTitleSc.style.fontWeight = "bold";
                divTitleSc.style.marginBottom = "10px";
                var td3 = row.insertCell(3)
                td3.appendChild(divTitleSc);
                td3.appendChild(document.createTextNode(curPush.body_sc));
                td3.classList.add("left", "top");
                
                row.insertCell(4).appendChild(document.createTextNode(curPush.push_date));
                row.insertCell(5).appendChild(document.createTextNode(statusMapping[curPush.status.toUpperCase()]));

                var btnCopy = document.createElement("button");
                btnCopy.appendChild(document.createTextNode("Copy"));
                btnCopy.setAttribute("data-i", i);
                btnCopy.onclick = function () {
                  CopyContent(this);
                }
                row.insertCell(6).appendChild(btnCopy);
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