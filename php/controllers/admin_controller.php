<?php
include_once ('../include/demo_data.php');
include_once ('../include/enum.php');

class AdminController {
  public $regSkipLine = '/(拍賣物品清單編號*)|(AUCTION LIST *)|(- \d* -$)|(Lot No. Item No. Description Quantity)|(批號 項目 物品詳情 數量)/';

  function parseData() {
    $txt = $GLOBALS['DEMO_AUCTION_PDF'];
    $strAuctionList = $this->splitAuctionListText($txt);
    foreach($strAuctionList as $strAuction) {
      $this->processAuctionListText($strAuction, ItemType::UnclaimedProperties);
    }
  }

  function splitAuctionListText($txt) {
    // post: array of auction text, to be processed 1 by 1 in the next stage
    $output = array();

    $i = 0;
    $foundBeginning = false;
    $curLines = array();
    $curStartsWithGldFileRef = false;
    $curFoundRemarks = false;
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $txt) as $line) {
      // if (!$foundBeginning && mb_substr($line, 0, 13) != "批號 項目 物品詳情 數量") {
      //   continue;
      // } else {
      //   $foundBeginning = true;
      // }

      if (substr($line, 0, 8)  == "Remarks " || substr($line, 0, 14)  == "(GLD File Ref.") {
        $foundBeginning = true;
        
        if (substr($line, 0, 14)  == "(GLD File Ref.") {
          // it must be the start of the new lot

          if ($i > 0) {
            // put $curLines into $output and start a new lot
            $output[] = implode("\n", $curLines);
          }

          $curLines = array();
          $curStartsWithGldFileRef = true;
          $curFoundRemarks = false;
        } else {
          // current line starts with "Remarks", it may be a start of the new lot
          // need to check the current starts with "GLD File Ref" or not                    
          if ($curStartsWithGldFileRef && !$curFoundRemarks) {
            // if so, it is the remarks of the current lot, no need to start a new lot
            $curFoundRemarks = true;
          } else {
            // if not, it is a start of the new item, can put $curLines into $output and start a new lot
            if ($i > 0) {
              $output[] = implode("\n", $curLines);
            }

            $curLines = array();
            $curStartsWithGldFileRef = false;
            $curFoundRemarks = true;
          }       
        }
      }
      
      if ($foundBeginning) {
        if (!preg_match($this->regSkipLine, $line)) {
          $curLines[] = $line;
        }
        ++$i;
      }
      
    }
    $output[] = implode("\n", $curLines);

    return $output;
  }

  function processAuctionListText($strAuction, $itemType) {
    $matchValues = array();
    $patterns = array(
      "lotNum" => '/\n' . $itemType . '-((\d)+) 1\./i',
      "gldFileRef" => '/\(GLD File Ref.*:\s*(.*)\)/i',
      "reference" => '/Reference\s*:\s*(.*)/i',
      "departmentEn" => '/Department\s*:\s*(.*)/i',
      "departmentTc" => '/部門\s*:\s*(.*)/i',
      "contactPersonEn" => '/Contact Person\s*:\s*(.*)\s*on\s*/i',
      "contactPersonTc" => '/聯絡人\s*:\s*(.*)\s*電話\s*:/i',
      "contactNumberEn" => '/Contact Person\s*:\s*.*\s*on\s*(.*)/i',
      "contactNumberTc" => '/聯絡人\s*:.*電話\s*:\s*(.*)/i',
      "locationEn" => '/Location\s*:\s*((.|\n)*)Contact Person\s*:/i',
      "locationTc" => '/地點\s*:\s*((.|\n)*)聯絡人\s*:/i',
      "remarksEn" => '/Remarks.*:((.|\n)*)注意事項/i',
      "remarksTc" => '/注意事項.*:((.|\n)*)。\n[a-zA-Z]+/i',
    );

    // loop through the patterns and assign the matched values into $matchValues
    foreach(array_keys($patterns) as $key) {
      preg_match($patterns[$key], $strAuction, $matches);

      if (count($matches) > 1) {
        $matchValues[$key] = $matches[1];
      } else {
        $matchValues[$key] = "";
      }
    }

    // post process add back characters
    if ($matchValues["remarksTc"] != "") {
      $matchValues["remarksTc"] .= "。";
    }
    if ($matchValues["lotNum"] != "") {
      $matchValues["lotNum"] = $itemType . "-" . $matchValues["lotNum"];
    }

    foreach(array_keys($matchValues) as $key) {
      echo "$key: " . $matchValues[$key] . "<br>";
    }
    echo "<textarea style='width: 800px; height: 300px'>$strAuction</textarea> <br />";
  }
}
?>