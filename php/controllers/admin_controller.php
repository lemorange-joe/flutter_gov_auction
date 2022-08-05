<?php
include_once ('../include/demo_data.php');

class AdminController {
  function parseData() {
    $txt = $GLOBALS['DEMO_AUCTION_PDF'];
    $strAuctionList = $this->splitAuctionListText($txt);
    Debug_var_dump($strAuctionList);
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
        $curLines[] = $line;
        ++$i;
      }
      
    }
    $output[] = implode("\n", $curLines);

    return $output;
  }
}
?>