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
  <title>Admin - View API</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <div class="header bgGreen">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">View API</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <select id="ddlLang">
      <option value="en">EN</option>
      <option value="tc" selected>繁</option>
      <option value="sc">简</option>
    </select>
    <select id="ddlApi" onchange="ChangeParamHint()">
      <option value="auction-list" data-param="" selected>List</option>
      <option value="auction-details" data-param="Auction ID, e.g. 1">Details</option>
      <option value="auction-search" data-param="Auction ID - Keyword - [Type], e.g. 1-jewellery-c, 2-car">Search</option>
      <option value="auction-relatedLots" data-param="Lot ID - page, e.g. 13-1">Related Lots</option>
      <option value="auction-relatedItems" data-param="Item ID - page, e.g. 31-2">Related Items</option>
      <option value="data-appinfo" data-param="">App Info</option>
      <option value="data-messagelist" data-param="">Message List</option>
    </select>
    <input id="tbParam" type="text" style="width: 300px">
    <button onclick="GetData()">Get</button>
    <div style="display: inline-block; border: solid 1px #333; padding: 3px 6px; margin-left: 10px">
      Version: <input type="text" id="tbVersion" placeholder="1.0.0">
      <button style="margin-left: 10px" onclick="PostData()">Post</button>
    </div>
    &nbsp;&nbsp;&nbsp;&nbsp;URL: <a id="lnkApiUrl" href="#" target="_blank"></a>
    <br /><br />
    <textarea id="txtResult" style="width: 1000px; height: 400px"></textarea>
    <script>
      function ChangeParamHint() {
        var ddl = document.getElementById("ddlApi");
        document.getElementById("tbParam").value = "";
        document.getElementById("tbParam").placeholder = ddl.options[ddl.selectedIndex].getAttribute("data-param");
        document.getElementById("tbParam").focus();
      }

      function GetApiUrl() {
        var url = "/{0}/api/{1}{2}";
        url = url.replace("{0}", document.getElementById("ddlLang").value);
        url = url.replace("{1}", document.getElementById("ddlApi").value);
        url = url.replace("{2}", document.getElementById("tbParam").value == "" ? "" : "-" + document.getElementById("tbParam").value);

        return url;
      }

      function SetApiUrl(url) {
        document.getElementById("lnkApiUrl").setAttribute("href", url);
        document.getElementById("lnkApiUrl").innerHTML = url;
      }

      function GetData(){
        document.getElementById("txtResult").value = "retrieving...";

        var url = GetApiUrl();
        SetApiUrl(url);

        var xhr = new XMLHttpRequest();
        
        xhr.open("GET", url);
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("txtResult").value = JSON.stringify(JSON.parse(this.responseText), null, "  ");
          }
        }
        
        xhr.send();
      }

      function PostData(){
        document.getElementById("txtResult").value = "retrieving...";

        var url = GetApiUrl();
        SetApiUrl(url);

        var xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append("version", document.getElementById("tbVersion").value.trim());
        
        xhr.open("POST", url);
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("txtResult").value = JSON.stringify(JSON.parse(this.responseText), null, "  ");
          }
        }
        
        xhr.send(formData);
      }

      ChangeParamHint();
      SetApiUrl(GetApiUrl());
    </script>
  </div>
</body>
</html>