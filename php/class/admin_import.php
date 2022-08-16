<?php
include_once ('../include/enum.php');
include_once ('../include/common.php');

class AdminImport {
  public $regSkipLine = '/(拍賣物品清單編號*)|(AUCTION LIST *)|(- \d* -$)|(Lot No. Item No. Description Quantity)|(批號 項目 物品詳情 數量)|(- E N D -)/';

  function parseData($itemType, $importText) {
    $strAuctionList = $this->splitAuctionListText($importText, $itemType);
    for ($lotIndex = 0; $lotIndex < Count($strAuctionList); ++$lotIndex) {
      $this->extractAuctionListText($strAuctionList[$lotIndex], $itemType, $lotIndex);
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

  function extractAuctionListText($strAuction, $itemType, $lotIndex) {
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

    
    $colWidth = "100";
    $colWidth2 = "800";
    $separatorHeight = "8";
    echo "<div style='display:inline-block;width:".$colWidth."px'>Lot Num:</div><input id='tbLotNum_$lotIndex' value='".str_replace("'", '"', $matchValues["lotNum"])."'>";
    echo "<div style='height:".$separatorHeight."px'></div>";
    echo "<div style='display:flex;width:1800px'>";
      echo "<div style='width:900px'>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>GLD Ref</div><input id='tbGldRef_$lotIndex' style='width:".$colWidth2."px' value='".str_replace("'", '"', $matchValues['gldFileRef'])."'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>Ref</div><input id='tbRef_$lotIndex' style='width:".$colWidth2."px' value='".str_replace("'", '"', $matchValues['reference'])."'></div>";
        echo "<div style='height:".$separatorHeight."px'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>Dept</div><input id='tbDeptEn_$lotIndex' style='width:".$colWidth2."px' value='".str_replace("'", '"', $matchValues['departmentEn'])."'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>部門</div><input id='tbDeptTc_$lotIndex' style='width:".$colWidth2."px' value='".str_replace("'", '"', $matchValues['departmentTc'])."'></div>";
        echo "<div style='height:".$separatorHeight."px'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>Contact</div><input id='tbContactEn_$lotIndex' style='width:".$colWidth2."px' value='".str_replace("'", '"', $matchValues['contactPersonEn'])."'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>聯絡人</div><input id='tbContactTc_$lotIndex' style='width:".$colWidth2."px' value='".str_replace("'", '"', $matchValues['contactPersonTc'])."'></div>";
        echo "<div style='height:".$separatorHeight."px'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>Number</div><input id='tbNumberEn_$lotIndex' style='width:".$colWidth2."px' value='".str_replace("'", '"', $matchValues['contactNumberEn'])."'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>電話</div><input id='tbNumberTc_$lotIndex' style='width:".$colWidth2."px' value='".str_replace("'", '"', $matchValues['contactNumberTc'])."'></div>";
        echo "<div style='height:".$separatorHeight."px'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>Location</div><input id='tbLocationEn_$lotIndex' style='width:".$colWidth2."px' value='".str_replace("'", '"', $matchValues['locationEn'])."'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>地點</div><input id='tbLocationTc_$lotIndex' style='width:".$colWidth2."px' value='".str_replace("'", '"', $matchValues['locationTc'])."'></div>";
        echo "<div style='height:".$separatorHeight."px'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>Remarks</div>";
        echo "<textarea id='tbRemarksEn_$lotIndex' style='width:".$colWidth2."px;height:60px'>".$matchValues['remarksEn']."</textarea></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>注意</div>";
        echo "<textarea id='tbRemarksTc_$lotIndex' style='width:".$colWidth2."px;height:60px'>".$matchValues['remarksTc']."</textarea></div>";
      echo "</div>";
      echo "<div style='width:600px'>";
        echo "<textarea style='width:600px;height:380px' disabled='disabled'>$strAuction</textarea>";    
      echo "</div>";
    echo "</div>";
    echo "<br style='clear: both' />";
    echo "<div id='divItems_$lotIndex' style='width:1500px'>";
        $total = $this->extractItems($matchValues["items"], $lotIndex);
    echo "</div>";
    echo "<button id='btnAddItem_$lotIndex' data-total='$total' onclick='AddItem($lotIndex)'>+</button>";
    echo "<hr />";
  }

  //pre: 1.Handwriting Board 手寫板 48 Nos. (塊) 2. Barcode Reader (Model: MS9540) 條碼讀取器 2 Nos. (個)
  // split item text from the input first
  // then parse to the next method to build items
  function extractItems($strItems, $lotIndex) {
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
  
    $total = Count($strItemList);
    for ($itemIndex = 0; $itemIndex < $total; ++$itemIndex) {
      $this->buildItems($strItemList[$itemIndex], $lotIndex, $itemIndex);
    }

    return $total;
  }

  //pre: 2. Barcode Reader (Model: MS9540) 條碼讀取器 2 Nos. (個)
  function buildItems($strItem, $lotIndex, $itemIndex) {
    $itemPropertyList = array_filter(explode("\n", $strItem));

    $bgImage = count($itemPropertyList) == 5 ? 'url("https://dummyimage.com/250x100/fff/888.png&text=++++++{i}")' : 'url("https://dummyimage.com/250x100/f88/666.png&text=++++++{i}")';
    echo "<textarea id='tbItem_$lotIndex"."_$itemIndex' style='width:250px;height:100px;background-image:" . str_replace('{i}', $itemIndex+1, $bgImage) . "'>";
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

  function parseResultData($importText) {
    $lotNumRegEx = '/^([a-zA-Z]+-\d+)$/i';
    $lines = preg_split("/((\r?\n)|(\r\n?))/", $importText);
    
    $lotList = array();
    $i = 0;
    while ($i < Count($lines)) {
      $line = trim($lines[$i]);

      if (preg_match($lotNumRegEx, $line, $matches))
      {
        $lotNum = "";
        if (count($matches) > 1) {
          $lotNum = $matches[1];
        }

        $price = str_replace(",", "", str_replace("$", "", trim($lines[++$i])));
        $lotList[$lotNum] = $price;

      }

      ++$i;
    }
    
    ksort($lotList);
    $keys = array_keys($lotList);
    $total = Count($lotList);
    for ($j = 0; $j < $total; ++$j) {
      echo "<div>";
      echo "<span style='display:inline-block;width:40px'>".($j+1).".</span>";
      echo "<input id='tbLotNum_".$j."' type='text' style='width: 100px' value='".str_replace("'", '"', $keys[$j])."'>&nbsp;&nbsp;";
      echo "<input id='tbPrice_".$j."' type='text' style='width: 200px' value='".str_replace("'", '"', $lotList[$keys[$j]])."'>";
      echo "</div>\n";
    }
    
    return $total;
  }
}
?>