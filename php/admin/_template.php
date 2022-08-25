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
  <title>Admin - XXX</title>
  <link rel="stylesheet" href="css/main.css">
  <style></style>
</head>
<body>
  <div class="header bgBlue">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">[Page Title]</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
  </div>
  <button style="position: fixed; right: 20px; bottom: 140px; width:36px; height: 36px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
  <button style="position: fixed; right: 20px; bottom: 100px; width:36px; height: 36px; font-size: 20px" onmouseover="AutoScroll(-12)" onmouseout="StopScroll()">▲</button>
  <button style="position: fixed; right: 20px; bottom: 60px; width:36px; height: 36px; font-size: 20px" onmouseover="AutoScroll(12)" onmouseout="StopScroll()">▼</button>
  <button style="position: fixed; right: 20px; bottom: 20px; width:36px; height: 36px; font-size: 20px" onclick="window.scrollTo(0, document.body.scrollHeight)">⟱</button>
  <script>
      var scrollTimeout;
      function AutoScroll(d) {
        window.scrollBy({top: d});
        scrollTimeout = setTimeout(function() {
          AutoScroll(d);
        }, 25);
      }
      function StopScroll() {
        clearTimeout(scrollTimeout);
      }
    
      function TempDisableButton(id) {
        document.getElementById(id).setAttribute("disabled", "disabled");
        setTimeout(function() {
          document.getElementById(id).removeAttribute("disabled");
        }, 5000);
      }
    </script>
</body>
</html>