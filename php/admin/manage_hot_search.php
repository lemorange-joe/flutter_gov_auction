<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/config.php");
include_once ("../include/common.php");
include_once ('../include/adodb5/adodb.inc.php');
$conn = new stdClass();
$conn = ADONewConnection('mysqli');
$conn->PConnect($GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'] , $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_NAME']);
$conn->Execute("SET NAMES UTF8");

$message = "";
if (isset($_POST["create_keyword"])) {
  $keywordEn = trim($_POST["keyword_en"]);
  $keywordTc = trim($_POST["keyword_tc"]);
  $keywordSc = trim($_POST["keyword_sc"]);

  if (empty($keywordEn) || empty($keywordEn) || empty($keywordEn)) {
    $message = "<div style='color: #800'>Fail: empty hot search keyword(s)</div>";
  } else {
    $insertSql = "INSERT INTO HotSearch (keyword_en, keyword_tc, keyword_sc) VALUES (?, ?, ?)";
    $insertResult = $conn->Execute($insertSql, array($keywordEn, $keywordTc, $keywordSc));

    if ($insertResult) {
      $message = "<div style='color: #080'>Success create: $keywordEn / $keywordTc / $keywordSc</div>";
    } else {
      $message = "<div style='color: #800'>Fail create: $keywordEn / $keywordTc / $keywordSc</div>";
    }
  }
} else if (isset($_POST["delete_keyword"])) {
  $keywordId = $_POST["delete_keyword"];
  
  $deleteSql = "DELETE FROM HotSearch WHERE keyword_id = ?";
  $deleteResult = $conn->Execute($deleteSql, array($keywordId));

  if ($deleteResult) {
    $message = "<div style='color: #080'>Success delete keyword ID: $keywordId</div>";
  } else {
    $message = "<div style='color: #800'>Fail delete keyword ID: $keywordId</div>";
  }
}

$selectSql = "SELECT keyword_id, keyword_en, keyword_tc, keyword_sc FROM HotSearch";
$result = $conn->Execute($selectSql)->GetRows();
$rowCount = count($result);

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Hot Search</title>
  <link rel="stylesheet" href="css/main.css">
  <style>
    table, th, td {
      border: 1px solid;
      border-collapse: collapse;
    }

    #tblHotSearch tr:hover {
      background-color: #ddd;
    }

    #tblHotSearch td {
      padding: 3px 5px;
    }

    #tblHotSearch td.center {
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="header bgPurple">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Hot Search</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
    <?=$message?>
    <div style="margin:10px 0;text-decoration:underline">Create</div>
    <div>
      <form method="POST">
        <input type="text" name="keyword_en" style="vertical-align: top; width: 200px; margin-right: 10px" maxlength="50" placeholder="Keyword (EN)">
        <input type="text" name="keyword_tc" style="vertical-align: top; width: 100px; margin-right: 10px" maxlength="10" placeholder="Keyword (TC)">
        <input type="text" name="keyword_sc" style="vertical-align: top; width: 100px; margin-right: 30px" maxlength="10" placeholder="Keyword (SC)">
        <button type="submit" name="create_keyword" style="margin-right: 10px">Create</button>
        <button type="reset">Reset</button>
      </form>
    </div>
    <hr style="width: 90%; margin-left: 0" />
    <table>
      <thead>
        <tr>
          <th style="width: 30px">#</th>
          <th style="width: 50px">ID</th>
          <th style="width: 200px">Keyword EN</th>
          <th style="width: 100px">Keyword TC</th>
          <th style="width: 100px">Keyword SC</th>
          <th style="width: 80px"></th>
        </tr>
      </thead>
      <tbody id='tblHotSearch'>
        <?php
        if ($rowCount == 0) {
          echo "<td colspan='6' style='height: 30px;text-align: center'>No Record</td>";
        } else {
          for ($i = 0; $i < $rowCount; ++$i) {
            echo "<tr style='height: 40px'>";
            echo "<td class='center'>" . ($i + 1) . "</td>";
            echo "<td class='center'>" . $result[$i]["keyword_id"] . "</td>";
            echo "<td>" . $result[$i]["keyword_en"] . "</td>";
            echo "<td>" . $result[$i]["keyword_tc"] . "</td>";
            echo "<td>" . $result[$i]["keyword_sc"] . "</td>";
            echo "<td class='center'><form method='post'><button type='submit' name='delete_keyword' value='" . $result[$i]["keyword_id"] . "'>Delete</button></form></td>";
            echo "</tr>\n";
          }
        }
        ?>
      </tbody>
    </table>
  </div>
  <script src="js/main.js"></script>
</body>
</html>