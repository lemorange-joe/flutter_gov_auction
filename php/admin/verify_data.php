<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/common.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Verify Data</title>
  <link rel="stylesheet" href="css/main.css">
  <style>
    .folder {
      width: 250px;
      margin-right: 10px;
    }

    .folder-title {
      background-color: #afa;
      margin: 1px 0;
      padding: 3px;
      color: #333;
      cursor: pointer;
    }

    .folder-content {
      border: solid 1px #666;
      margin-bottom: 10px;
    }

    .folder-content a {
      color: #58f;
      margin: 8px 5px;
      display: block;
    }
  </style>
</head>
<body>
  <div class="header bgGreen">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Verify Data</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <div style="display: flex">
      <div class="folder">
      <?php
        function buildPdfFolder($folderName) {
          $curFolder = __dir__."/pdf_source/".$folderName;
          $pdfFiles = scandir($curFolder);
          echo "<div class='folder-title' onclick='showFolder(this)' data-folder='".$folderName."'>" . $folderName . "</div>";
          echo "<div id='divContent_".$folderName."' class='folder-content' style='display: none'>";
          foreach($pdfFiles as $pdfFile) {
            $curFile = $curFolder."/".$pdfFile;
            if (!is_dir($curFile) && strtolower(substr($pdfFile, -4)) == ".pdf") {
              $relativeFilePath = "pdf_source/".$folderName."/".$pdfFile;
              echo "<a href='#' onclick='openPdf(\"".$relativeFilePath."\");return false'>".$pdfFile."</a>";
            }
          }
          echo "</div>";
        }

        $pdfFolders = scandir(__dir__."/pdf_source", SCANDIR_SORT_DESCENDING);
        $pdfFiles = array();
        foreach($pdfFolders as $pdfFolder) {
          $curFolder = __dir__."/pdf_source/".$pdfFolder;
          if (is_dir($curFolder) && $pdfFolder != "." && $pdfFolder != "..") {
            buildPdfFolder($pdfFolder);
          }
        }
      ?>
      <div style="margin-top: 20px">
        <a href="https://www.gld.gov.hk/zh-hk/our-services/supplies/auction/" target="_blank">Source Website</a>
      </div>
    </div>
    <iframe id="ifrmPdf" style="width: 1200px; height: 500px" src=""></iframe>
  </div>
  <button style="position: fixed; right: 20px; bottom: 140px; width:36px; height: 36px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
  <button style="position: fixed; right: 20px; bottom: 100px; width:36px; height: 36px; font-size: 20px" onmouseover="AutoScroll(-12)" onmouseout="StopScroll()">▲</button>
  <button style="position: fixed; right: 20px; bottom: 60px; width:36px; height: 36px; font-size: 20px" onmouseover="AutoScroll(12)" onmouseout="StopScroll()">▼</button>
  <button style="position: fixed; right: 20px; bottom: 20px; width:36px; height: 36px; font-size: 20px" onclick="window.scrollTo(0, document.body.scrollHeight)">⟱</button>
  <script src="js/main.js"></script>
  <script>
    function showFolder(el) {
      var contentId = "divContent_" + el.getAttribute("data-folder");

      Array.from(document.getElementsByClassName("folder-content")).forEach(function(divContent) {
        divContent.style.display = "none";
      });

      document.getElementById(contentId).style.display = "block";
    }

    function openPdf(url) {
      document.getElementById("ifrmPdf").src = url;
    }
  </script>
</body>
</html>