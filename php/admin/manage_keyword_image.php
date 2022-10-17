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
  <title>Admin - Keyword Image</title>
  <link rel="stylesheet" href="css/main.css">
  <style>
    table, th, td {
      border: 1px solid;
      border-collapse: collapse;
    }

    #tblKeywordImage tr:hover {
      background-color: #ddd;
    }

    #tblKeywordImage td {
      padding: 3px 5px;
    }

    #tblKeywordImage td.center {
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="header bgPurple">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Keyword Image</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <div style="margin:10px 0;text-decoration:underline">Create</div>
    <div>
      <input type="text" id="tbKeywordEn" style="vertical-align: top; width: 200px; margin-right: 10px" placeholder="Keyword (EN)">
      <input type="text" id="tbKeywordTc" style="vertical-align: top; width: 100px; margin-right: 10px" placeholder="Keyword (TC)">
      <div style="display: inline-block">
        <input type="text" id="tbImageUrl" style="width: 600px; margin-right: 20px" placeholder="Image URL">
        <br />OR<br />
        <input type="file" name="fileImage" id="fileImage" />
      </div>

      <button id="btnCreate" style="vertical-align: top; margin-right: 10px" onclick="CreateKeywordImage()">Create</button>
      <button onclick="ResetData()" style="vertical-align: top">Reset</button>
    </div>
    <hr style="width: 75%; margin-left: 0" />
    <div style="height: 40px;line-height: 30px;">
      Search&nbsp;&nbsp;<input id="tbKeyword" type="text" style="width: 150px; margin-right: 20px" placeholder="Input Keyword" />
      <button style="margin-right:10px" onclick="GetData(document.getElementById('tbKeyword').value)">Get</button>
      <button onclick="document.getElementById('tbKeyword').value='';GetData()">Clear</button>
    </div>
    <table>
      <thead>
        <tr>
          <th style="width: 50px"><a href="#" onclick="SortData('id');return false">ID</a></th>
          <th style="width: 150px"><a href="#" onclick="SortData('en');return false">Keyword EN</a></th>
          <th style="width: 150px"><a href="#" onclick="SortData('tc');return false">Keyword TC</a></th>
          <th style="width: 600px">Image URL</th>
          <th style="width: 60px"></th>
        </tr>
      </thead>
      <tbody id="tblKeywordImage"></tbody>
    </table>
  </div>
  <button style="position: fixed; right: 20px; bottom: 140px; width:36px; height: 36px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
  <button style="position: fixed; right: 20px; bottom: 100px; width:36px; height: 36px; font-size: 20px" onmouseover="AutoScroll(-12)" onmouseout="StopScroll()">▲</button>
  <button style="position: fixed; right: 20px; bottom: 60px; width:36px; height: 36px; font-size: 20px" onmouseover="AutoScroll(12)" onmouseout="StopScroll()">▼</button>
  <button style="position: fixed; right: 20px; bottom: 20px; width:36px; height: 36px; font-size: 20px" onclick="window.scrollTo(0, document.body.scrollHeight)">⟱</button>
  <script src="js/main.js"></script>
  <script>
      var keywordImageList = [];
      var curSortField = "";

      function GetData(keyword) {
        var apiUrl = "../en/api/admin-listKeywordImage";
        if (keyword) {
          apiUrl += "-" + encodeURIComponent(keyword);
        }
        var xhr = new XMLHttpRequest();
        
        xhr.open("GET", apiUrl);
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            if (Array.isArray(keywordImageList)) {
              keywordImageList = JSON.parse(this.responseText);
              sortField = "id";
              BuildTable();
            }
          }
        }
        
        xhr.send();
      }

      function BuildTable() {
        var tblKeywordImage = document.getElementById("tblKeywordImage");
        tblKeywordImage.innerHTML = "";
        
        for (var i = 0; i < keywordImageList.length; ++i) {
          const keywordImage = keywordImageList[i];
          
          var row = tblKeywordImage.insertRow();
          var td0 = row.insertCell(0)
          td0.appendChild(document.createTextNode(keywordImage.id));
          td0.classList.add("center");

          row.insertCell(1).appendChild(document.createTextNode(keywordImage.keyword_en));
          row.insertCell(2).appendChild(document.createTextNode(keywordImage.keyword_tc));

          var imageUrl = keywordImage.image_url;
          if (!imageUrl.startsWith("http://") && !imageUrl.startsWith("https://")) {
            imageUrl = "<?=$GLOBALS["AUCTION_IMAGE_ROOT_URL"]?>" + imageUrl; 
          }

          var imgKeyword = document.createElement("img");
          imgKeyword.setAttribute("src", imageUrl);
          imgKeyword.style.height = "60px";

          var lnkImageUrl = document.createElement("a");
          lnkImageUrl.appendChild(imgKeyword);
          lnkImageUrl.appendChild(document.createElement("br"));
          lnkImageUrl.appendChild(document.createTextNode(keywordImage.image_url));
          lnkImageUrl.setAttribute("href", imageUrl);
          lnkImageUrl.setAttribute("target", "_blank");
          row.insertCell(3).appendChild(lnkImageUrl);

          var btnDelete = document.createElement("button");
          btnDelete.appendChild(document.createTextNode("Delete"));
          btnDelete.setAttribute("data-id", keywordImage.id);
          btnDelete.setAttribute("data-keyword-en", keywordImage.keyword_en);
          btnDelete.setAttribute("data-keyword-tc", keywordImage.keyword_tc);
          btnDelete.setAttribute("data-image-url", keywordImage.image_url);
          btnDelete.onclick = function () {
            DeleteKeywordImage(this);
          }
          row.insertCell(4).appendChild(btnDelete);
        }
      }

      function ResetData(keywordEn, keywordTc, imageUrl) {
        document.getElementById("tbKeywordEn").value = keywordEn ? keywordEn : "";
        document.getElementById("tbKeywordTc").value = keywordTc ? keywordTc : "";
        document.getElementById("tbImageUrl").value = imageUrl ? imageUrl : "";
        document.getElementById("fileImage").value = "";
      }

      function CreateKeywordImage() {
        TempDisableButton("btnCreate");

        var formData = new FormData();
        formData.append("keyword_en", document.getElementById("tbKeywordEn").value.trim());
        formData.append("keyword_tc", document.getElementById("tbKeywordTc").value.trim());
        if (document.getElementById("fileImage").files.length > 0) {
          formData.append("image_file", document.getElementById("fileImage").files[0], document.getElementById("fileImage").files[0].name);
          formData.append("image_url", "");
        } else {
          formData.append("image_file", "");
          formData.append("image_url", document.getElementById("tbImageUrl").value.trim());
        }
        
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../en/api/admin-createKeywordImage");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
            if (this.status == 200) {
              const jsonData = JSON.parse(this.responseText);

              if (jsonData.status == "success") {
                ResetData();
                GetData();
              } else {
                alert("Create failed: " + jsonData.error);  
              }
            } else {
              alert("Error: " + this.responseText);
            }
          }
        };

        xhr.send(formData);
      }

      function DeleteKeywordImage(btn) {
        var id = btn.getAttribute("data-id");
        var keywordEn = btn.getAttribute("data-keyword-en");
        var keywordTc = btn.getAttribute("data-keyword-tc");
        var imageUrl = btn.getAttribute("data-image-url");

        if (!confirm("Confirm to delete image for " + keywordTc + ", " + keywordEn + "?")) {
          return;
        }

        var postData = {
          id: id,
        };

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../en/api/admin-deleteKeywordImage");
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
            if (this.status == 200) {
              const jsonData = JSON.parse(this.responseText);

              if (jsonData.status == "success") {
                ResetData(keywordEn, keywordTc, imageUrl);
                GetData();
                document.getElementById("tbKeywordEn").focus();
              } else {
                alert("Delete failed: " + jsonData.error);  
              }
            } else {
              alert("Error: " + this.responseText);
            }
          }
        };

        xhr.send(JSON.stringify(postData));
      }

      function CompareKeywordEn(a, b) {
        if (a.keyword_en.toLowerCase() < b.keyword_en.toLowerCase()){
          return -1;
        }
        if (a.keyword_en.toLowerCase() > b.keyword_en.toLowerCase()){
          return 1;
        }
        return 0;
      }

      function CompareKeywordTc(a, b) {
        if (a.keyword_tc.toLowerCase() < b.keyword_tc.toLowerCase()){
          return -1;
        }
        if (a.keyword_tc.toLowerCase() > b.keyword_tc.toLowerCase()){
          return 1;
        }
        return 0;
      }

      function CompareId(a, b) {
        if (a.id < b.id){
          return -1;
        }
        if (a.id > b.id){
          return 1;
        }
        return 0;
      }

      function SortData(field) {
        if (curSortField == field) {
          keywordImageList.reverse();
        } else {
          if (field == "en") {
            curSortField = "en";
            keywordImageList.sort(CompareKeywordEn);
          } else if (field == "tc") {
            curSortField = "tc";
            keywordImageList.sort(CompareKeywordTc);
          } else {
            curSortField = "id";
            keywordImageList.sort(CompareId);
          }
        }

        BuildTable();
      }

      GetData();
    </script>
</body>
</html>