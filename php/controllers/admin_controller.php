<?php
include_once ('../include/enum.php');
$testingItemType = ItemType::UnserviceableStores;
include_once ('../include/demo_data_'.strtolower($testingItemType).'.php');

class AdminController {
  public $regSkipLine = '/(拍賣物品清單編號*)|(AUCTION LIST *)|(- \d* -$)|(Lot No. Item No. Description Quantity)|(批號 項目 物品詳情 數量)|(- E N D -)/';

  function parseData() {
    global $testingItemType;

    $txt = $GLOBALS['DEMO_AUCTION_PDF'];
    $strAuctionList = $this->splitAuctionListText($txt, $testingItemType);
    foreach($strAuctionList as $strAuction) {
      $this->extractAuctionListText($strAuction, $testingItemType);
    }
  }

  function splitAuctionListText($txt, $itemType) {
    // post: array of auction text, to be processed 1 by 1 in the next stage
    $output = array();
    $lotNumRegEx = '/^' . $itemType . '-(\d+)$/i';

    $i = 0;
    $foundBeginning = false;
    $curLines = array();
    $curStartsWithGldFileRef = false;
    $curFoundRemarks = false;
    $curFoundLotNum = false;
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $txt) as $line) {
      // if (!$foundBeginning && mb_substr($line, 0, 13) != "批號 項目 物品詳情 數量") {
      //   continue;
      // } else {
      //   $foundBeginning = true;
      // }

      $curLineIsGldFileRef = substr($line, 0, 14)  == "(GLD File Ref.";
      $curLineIsRemarks = substr($line, 0, 8)  == "Remarks ";
      $curLineIsLotNum = preg_match($lotNumRegEx, $line);

      if ($curLineIsGldFileRef || $curLineIsRemarks || $curLineIsLotNum) {
        $foundBeginning = true;
        
        if ($curLineIsGldFileRef) {
          // it must be the start of the new lot
          if ($i > 0) {
            // put $curLines into $output and start a new lot
            $output[] = implode("\n", $curLines);
          }

          $curLines = array();
          $curStartsWithGldFileRef = true;
          $curFoundRemarks = false;
          $curFoundLotNum = false;
        } else if ($curLineIsRemarks) {
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
            $curFoundLotNum = false;
          }       
        } else {  //$curLineIsLotNum          
          if ($curFoundLotNum) {
            // already found a lot num before, then it is the beginning of a new lot
            if ($i > 0) {
              $output[] = implode("\n", $curLines);
            }

            $curLines = array();
            $curStartsWithGldFileRef = false;
            $curFoundRemarks = false;  
          }

          $curFoundLotNum = true;
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

  function extractAuctionListText($strAuction, $itemType) {
    $matchValues = array();
    $patterns = array(
      "lotNum" => '/^' . $itemType . '-(\d+)[\s|\n]1\./im',
      "gldFileRef" => '/\(GLD File Ref.*:\s*(.*)\)/i',
      "reference" => '/Reference\s*:\s*(.*)/i',
      "departmentEn" => '/Department\s*:\s*(.*)/i',
      "departmentTc" => '/部門\s*:\s*(.*)/i',
      "contactPersonEn" => '/Contact Person\s*:\s*(.*)\s*on\s*/i',
      "contactPersonTc" => '/聯絡人\s*:\s*(.*)\s*電話\s*:/i',
      "contactNumberEn" => '/Contact Person\s*:\s*.*\s*on\s*(.*)/i',
      "contactNumberTc" => '/聯絡人[\n|\s]*:\n*.*電話\s*:\s*(.*)/i',
      "locationEn" => '/Location\s*:\s*((.|\n)*)Contact Person\s*:/i',
      "locationTc" => '/地點\s*:\s*((.|\n)*)聯絡人\s*:/i',
      "remarksEn" => '/Remarks.*:((.|\n)*)注意事項/i',
      "remarksTc" => '/注意事項.*:((.|\n)*)。\n[a-zA-Z]+/i',
      "items" => '/^' . $itemType . '-\d+[\s|\n]1\.((.|\n)*)/im',
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
    if ($matchValues["items"] != "") {
      $matchValues["items"] = "1." . $matchValues["items"];
    }

    // for debug
    // foreach(array_keys($matchValues) as $key) {
    //   echo "$key: " . $matchValues[$key] . "<br>";
    // }
    // echo "<textarea style='width: 1200px; height: 300px'>$strAuction</textarea> <br />";    
    echo $matchValues["lotNum"] . "<br /><textarea style='width: 1200px; height: 300px'>" . $matchValues["items"] . "</textarea><br />";
    $this->extractItems($matchValues["items"]);
    echo "<hr />";
  }

  //pre: 1.Handwriting Board 手寫板 48 Nos. (塊) 2. Barcode Reader (Model: MS9540) 條碼讀取器 2 Nos. (個)
  // split item text from the input first
  // then parse to the next method to build items
  function extractItems($strItems) {
    $output = array();
    // $strItemList = preg_split("/\d+\./", $strItems);

    $strItemList = array();
    $reachEnd = false;
    $curItemNum = 1;
    $curStrItems = $strItems;
    while (!$reachEnd) {
      $nextItemNum = $curItemNum + 1;
      $endPos = strrpos($curStrItems, "\n".$nextItemNum.".");

      if ($endPos === false) {
        $reachEnd = true;
        $strItemList[] = preg_replace("/(\d+\.)/", "", trim($curStrItems));
      } else {
        $strItemList[] = preg_replace("/(\d+\.)/", "", trim(substr($curStrItems, 0, $endPos)), 1);  // get the current item text from the current string
        $curStrItems = substr($curStrItems, $endPos);       // then remove that item from the current string
        ++$curItemNum;
      }
    }
  
    for ($i = 0; $i < Count($strItemList); ++$i) {
      $this->buildItems($strItemList[$i], $i+1);
    }

    return $output;
  }

  //pre: 2. Barcode Reader (Model: MS9540) 條碼讀取器 2 Nos. (個)
  function buildItems($strItem, $num) {
    $itemPropertyList = array_filter(explode("\n", $strItem));

    $bgImage = count($itemPropertyList) == 5 ? 'url("https://dummyimage.com/300x100/fff/888.png&text=++++++{i}")' : 'url("https://dummyimage.com/300x100/f88/666.png&text=++++++{i}")';
    echo "<textarea style='width:300px;height:100px;white-space:pre;overflow-wrap:normal;overflow-x:scroll;background-image:" . str_replace('{i}', $num, $bgImage) . "'>";
    // var_dump($itemPropertyList);
    echo $strItem;
    echo "</textarea>";
    // echo "<textarea style='width: 300px; height: 200px'>" . $strItem . "</textarea>";

    $descriptionEn = "";
    $descriptionTc = "";
    $quantity = "";
    $unitEn = "";
    $unitTc = "";

    return array(
      "descriptionEn" => $descriptionEn,
      "descriptionTc" => $descriptionTc,
      "quantity" => $quantity,
      "unitEn" => $unitEn,
      "unitTc" => $unitTc,
    );
  }
}
?>