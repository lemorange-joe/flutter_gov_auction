<?php
include_once ("../include/enum.php");
include_once ("../class/admin_import.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Preview Auction List</title>
  <style>
    textarea {
      white-space: pre;
      overflow-wrap: normal;
      overflow-x: scroll;
    }
  </style>
</head>
<body>
<?php
if (!isset($_POST["item_type"]) || !isset($_POST["auction_num"]) || empty($_POST["item_type"])|| empty($_POST["auction_num"])) {
?>
Missing data!
<br /><br />
<a href="input_auction_list.php">Back</a>
<?php
} else {
?>
  Item type: <?=$_POST["item_type"]?> | Num: <?=$_POST["auction_num"]?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="input_auction_list.php">Cancel</a>
  <hr />
  <?php
    $itemType = $_POST["item_type"];
    $importText = $_POST["import_text"];
    
    $adminImport = new AdminImport();
    $adminImport->parseData($itemType, $importText);
  ?>
  <button onclick="ImportData()">Import</button>&nbsp;&nbsp;&nbsp;&nbsp;<a href="input_auction_list.php">Cancel</a>
  <script>
    function ImportData() {
      var i = 0;
      var output = "";

      while (document.getElementById("tbLotNum_"+i)) {
        var j = 0;
        var itemList = [];
        while (document.getElementById("tbItem_"+i+"_"+j)) {
          var itemLines = document.getElementById("tbItem_"+i+"_"+j).value.split("\n");
          
          itemList.push({
            "description_en": itemLines.length > 0 ? itemLines[0] : "",
            "description_tc": itemLines.length > 1 ? itemLines[1] : "",
            "quantity": itemLines.length > 2 ? itemLines[2] : "",
            "unit_en": itemLines.length > 3 ? itemLines[3] : "",
            "unit_tc": itemLines.length > 4 ? itemLines[4] : "",
          });

          ++j;
        }
        
        var curLot = {
          "lot_num": document.getElementById("tbLotNum_"+i).value,
          "gld_file_ref": document.getElementById("tbGldRef_"+i).value,
          "ref": document.getElementById("tbRef_"+i).value,
          "dept_en": document.getElementById("tbDeptEn_"+i).value,
          "dept_tc": document.getElementById("tbDeptTc_"+i).value,
          "contact_en": document.getElementById("tbContactEn_"+i).value,
          "contact_tc": document.getElementById("tbContactTc_"+i).value,
          "number_en": document.getElementById("tbNumberEn_"+i).value,
          "number_tc": document.getElementById("tbNumberTc_"+i).value,
          "location_en": document.getElementById("tbLocationEn_"+i).value,
          "location_tc": document.getElementById("tbLocationTc_"+i).value,
          "remarks_en": document.getElementById("tbRemarksEn_"+i).value,
          "remarks_tc": document.getElementById("tbRemarksTc_"+i).value,
          "items": itemList,
        };

        console.log(curLot);
        
        ++i;
      }
    }
  </script>
<?php
}
?>

</body>
</html>