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
  <title>Admin - App Info</title>
  <link rel="stylesheet" href="css/main.css?v=<?=$ADMIN_VERSION?>">
  <style>
    textarea {
      white-space: normal;
    }
  </style>
</head>
<body>
  <div class="header bgYellow">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">App Info</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <div>
      <span>Min App Version</span>
      <input id="tbMinAppVersion" style="width: 100px" placeholder="1.0.0" data-reset-value=""/>
    </div>
    <div>
      <span>Data Version</span>
      <input id="tbDataVersion" style="width: 100px" placeholder="2201001" data-reset-value=""/>
    </div>
    <div style="display: flex; width: 1200px; margin: 10px 0 5px 0">
      <div>
        News (EN)<br />
        <textarea id="txtNewsEn" style="width: 400px; height: 100px" data-reset-value=""></textarea>
      </div>
      <div style="margin: 0 10px">
        News (TC)<br />
        <textarea id="txtNewsTc" style="width: 400px; height: 100px" data-reset-value=""></textarea>
      </div>
      <div>
        News (SC)<br />
        <textarea id="txtNewsSc" style="width: 400px; height: 100px" data-reset-value=""></textarea>
      </div>
    </div>
    <div style="margin-bottom: 20px">
      Last Update: <span id="spnLastUpdate"></span>
    </div>
    <button id="btnSave" class="action-button" onclick="TempDisableButton('btnSave');SaveForm()">Save</button>
    <button onclick="ResetForm()" style="margin-left: 20px">Reset</button>
  </div>
  <script src="js/main.js?v=<?=$ADMIN_VERSION?>"></script>
  <script>
    function GetData() {
      var apiUrl = '../en/api/admin-getAppInfo';
        var xhr = new XMLHttpRequest();
        
        xhr.open("GET", apiUrl);
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            const jsonData = JSON.parse(this.responseText);

            document.getElementById("tbMinAppVersion").value = jsonData["min_app_version"];
            document.getElementById("tbMinAppVersion").setAttribute("data-reset-value", jsonData["min_app_version"]);
            document.getElementById("tbDataVersion").value = jsonData["data_version"];
            document.getElementById("tbDataVersion").setAttribute("data-reset-value", jsonData["data_version"]);
            document.getElementById("txtNewsEn").value = jsonData["news_en"];
            document.getElementById("txtNewsEn").setAttribute("data-reset-value", jsonData["news_en"]);
            document.getElementById("txtNewsTc").value = jsonData["news_tc"];
            document.getElementById("txtNewsTc").setAttribute("data-reset-value", jsonData["news_tc"]);
            document.getElementById("txtNewsSc").value = jsonData["news_sc"];
            document.getElementById("txtNewsSc").setAttribute("data-reset-value", jsonData["news_sc"]);

            var highlightText = document.getElementById("spnLastUpdate").innerHTML != "";
            document.getElementById("spnLastUpdate").innerHTML = jsonData["last_update"];
            if (highlightText) {
              document.getElementById("spnLastUpdate").classList.remove("highlight-text");
              document.getElementById("spnLastUpdate").classList.add("highlight-text");
            }
          }
        }

        xhr.send();
    }

    function SaveForm() {
      var postData = {
        min_app_version: document.getElementById("tbMinAppVersion").value,
        data_version: document.getElementById("tbDataVersion").value,
        news_en: document.getElementById("txtNewsEn").value,
        news_tc: document.getElementById("txtNewsTc").value,
        news_sc: document.getElementById("txtNewsSc").value
      }

      var xhr = new XMLHttpRequest();
      xhr.open("POST", "../en/api/admin-saveAppInfo");
      xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
      xhr.onreadystatechange = function () {
        if (this.readyState == 4) {
          if (this.status == 200) {
            const jsonData = JSON.parse(this.responseText);

            if (jsonData.status == "success") {
              GetData();
            } else {
              alert("Save app info failed: " + jsonData.error);  
            }
          } else {
            alert("Error: " + this.responseText);
          }
        }
      };

      xhr.send(JSON.stringify(postData));
    }

    function ResetForm() {
      var inputList = ["tbMinAppVersion", "tbDataVersion", "txtNewsEn", "txtNewsTc", "txtNewsSc"];

      inputList.forEach(function (id, i) {
        var input = document.getElementById(id);
        input.value = input.getAttribute("data-reset-value");
      });
    }

    GetData();
  </script>
</body>
</html>