<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/common.php");

$jsonFilePath = "./pdf_source/auction_lot.json";

if (isset($_POST["SaveJson"])) {
  $bakJsonFilePath1 = "./pdf_source/auction_lot.bk1";
  $bakJsonFilePath2 = "./pdf_source/auction_lot.bk2";
  rename($bakJsonFilePath1, $bakJsonFilePath2);
  rename($jsonFilePath, $bakJsonFilePath1);
  file_put_contents($jsonFilePath, $_POST["txtStatJson"]);
}

$jsonString = file_get_contents($jsonFilePath);
$lotData = json_decode($jsonString, true);

$lotMapping = array();
foreach($lotData as $key => $auctionLot) {
  $lotMapping[] = $key;
  $lotMapping[$key] = new StdClass();
  $lotMapping[$key]->lotNum = $auctionLot["lot-num"];
  $lotMapping[$key]->stat = $auctionLot["stat"];
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - View PDF Files</title>
  <link rel="stylesheet" href="css/main.css">
  <style>
    .folder {
      width: 300px;
      height: 420px;
      margin-right: 10px;
      overflow-y: scroll;
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
  <div id="divStatJson" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6)">
    <form method="POST">
      <div style="background-color: #fff; position: absolute; left: 50%; top: 50%; margin-left: -400px; margin-top: -250px; width: 800px; height: 450px">
        <textarea name="txtStatJson" style="width: 790px; height: 400px"><?=$jsonString?></textarea>
        <div style="display: flex; justify-content: center; margin-top: 8px;">
          <button type="submit" name="SaveJson">Save</button>
          <div style="width: 50px"></div>
          <button onclick="document.getElementById('divStatJson').style.display='none';">Cancel</button>
        </div>
      </div>
    </form>
  </div>
  <div class="header bgGreen">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">View PDF Files</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <div style="display: flex">
      <div>
        <div class="folder">
        <?php
          function buildPdfFolder($folderName) {
            global $lotMapping;

            $curFolder = __dir__."/pdf_source/".$folderName;
            $pdfFiles = scandir($curFolder);
            $displayFolderName = $folderName;
            $lotStat = "-";
            if (isset($lotMapping[$folderName])) {
              $curLot = $lotMapping[$folderName];
              $displayFolderName = $curLot->lotNum;
              $lotStat = "";
              foreach ($curLot->stat as $itemType => $stat) {
                $lotStat .= "<div style='background-color: #eee; padding: 1px 3px; font-size: 14px'>$itemType: $stat</div>";
              }
            }
            $lotStat = rtrim($lotStat, ", ");

            echo "<div class='folder-title' onclick='showFolder(this)' data-folder='$folderName' style='display: flex'>";
              echo "<div>$displayFolderName</div>";
              echo "<div style='width: 8px'></div>";
              echo "<div style='flex-grow: 1'>";
                echo "<div style='display: flex; justify-content: space-between'>$lotStat</div>";
              echo "</div>";
            echo "</div>";
            echo "<div id='divContent_$folderName' class='folder-content' style='display: none'>";
            foreach($pdfFiles as $pdfFile) {
              $curFile = $curFolder."/".$pdfFile;
              if (!is_dir($curFile) && strtolower(substr($pdfFile, -4)) == ".pdf") {
                $relativeFilePath = "pdf_source/".$folderName."/".$pdfFile;
                echo "<a href='#' onclick='openPdf(\"$relativeFilePath\");return false'>$pdfFile</a>";
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
      </div>
      <div style="margin-top: 20px">
        <a href="https://www.gld.gov.hk/zh-hk/our-services/supplies/auction/" target="_blank">Source Website</a>
      </div>
      <button style="margin-top: 20px" onclick="showJsonTextArea()">Update Stat File</button>
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

    function showJsonTextArea() {
      document.getElementById("divStatJson").style.display = "block";
    }
  </script>
</body>
</html>