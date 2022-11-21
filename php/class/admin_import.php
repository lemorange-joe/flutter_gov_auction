<?php
include_once ('../include/enum.php');
include_once ('../include/common.php');

// ==============================================================
// Notes
// -----
// 1. All data extractor regular expression starts with $regex
// 
// ==============================================================

class AdminImport {
  public $regexSkipLine = '/(拍賣物品清單編號*)|(AUCTION LIST *)|(- \d* -$)|(Lot No. Item No. Description Quantity)|(批號 項目 物品詳情 數量)|(- E N D -)/';

  public $itemConditionKeywords = array(
    array("Serviceable But May Not Function Properly/May be Damaged", "仍可使用但或許不能正常操作或已有損壞"),
    array("May be Unserviceable/May Not Function Properly/May be Damaged", "或不能再用/或不能正常操作/或已有損壞"),
    array("Abandoned Regulated Electrical Equipment", "被棄置受管制電器"),
    array("Empty Toner/Ink Cartridges", "已用完的空碳粉匣"),
    array("May be Damaged", "或許已有損壞"),
    array("Unserviceable", "不能再用"),
  );

  function getRegexItemCondition($lang) {
    $outputRegex = '';
    
    if ($lang == "en") {
      $outputRegex = '/(?:';
      for ($i = 0; $i < count($this->itemConditionKeywords); ++$i) {
        $outputRegex .= '(' . str_replace('/', '\/', $this->itemConditionKeywords[$i][0]) . ')|';
      }
      $outputRegex = rtrim($outputRegex, '|');
      $outputRegex .= ')/i';
    } else if ($lang == "tc"){
      $outputRegex = '/(';

      for ($i = 0; $i < count($this->itemConditionKeywords); ++$i) {
        $outputRegex .= '(?:';
          $outputRegex .= str_replace('/', '\/', $this->itemConditionKeywords[$i][0]);  // starts with English condition
          $outputRegex .= '\s*';  // maybe a space between the English and Chinese condition
          $outputRegex .= '\(';   // the starting bracket of the Chinese condition in PDF
            $outputRegex .= '(' . str_replace('/', '\/', $this->itemConditionKeywords[$i][1]) . ')';  // the regex matching bracket
          $outputRegex .= '\)';
        $outputRegex .= ')|';
      }

      $outputRegex = rtrim($outputRegex, '|');
      $outputRegex .= ')/i';
    }

    return $outputRegex;
  }

  function parseData($itemType, $importText) {
    $firstLotNum = "";
    $lastLotNum = "";
    $curLotNum = "";
    $strAuctionList = $this->splitAuctionListText($importText, $itemType);
    for ($lotIndex = 0; $lotIndex < Count($strAuctionList); ++$lotIndex) {
      $this->extractAuctionListText($strAuctionList[$lotIndex], $itemType, $lotIndex, $curLotNum);
      if ($lotIndex == 0) {
        $firstLotNum = $curLotNum;
      }
    }
    $lastLotNum = $curLotNum;
    echo "<div style='display: inline-block; background-color: #bfb; height: 30px; line-height: 30px; padding: 0px 10px'>";
    echo "$firstLotNum ➔ $lastLotNum";
    echo "</div>";
    echo "&nbsp;&nbsp;";
    echo "<div style='display: inline-block; background-color: #ffb; height: 30px; line-height: 30px; padding: 0px 10px; font-weight: bold'>";
    echo "Total: " . Count($strAuctionList);
    echo "</div>";
    echo "<hr />";
  }

  function splitAuctionListText($txt, $itemType) {
    // post: array of auction text, to be processed 1 by 1 in the next stage
    $output = array();
    $regexLotNum = '/^' . $itemType . '-(\d+)$/i';

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
      $curLineIsLotNum = preg_match($regexLotNum, $line);

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
        if (!preg_match($this->regexSkipLine, $line)) {
          $curLines[] = $line;
        }
        ++$i;
      }
    }
    $output[] = implode("\n", $curLines);

    return $output;
  }

  function extractAuctionListText($strAuction, $itemType, $lotIndex, &$outputLotNum) {
    $matchValues = array();
    $regexDataPatterns = array(
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
      "itemConditionEn" => $this->getRegexItemCondition("en"),
      "itemConditionTc" => $this->getRegexItemCondition("tc"),
      "items" => '/^' . $itemType . '-\d+[\s|\n]1\.((.|\n)*)/im',
    );

    // loop through the patterns and assign the matched values into $matchValues
    foreach(array_keys($regexDataPatterns) as $key) {
      preg_match_all($regexDataPatterns[$key], $strAuction, $matches);

      if (count($matches) > 1 && count($matches[1]) > 0) {
        if ($key == "itemConditionEn") {
          $matchValues[$key] = implode("\n", array_unique($matches[0]));
        } else if ($key == "itemConditionTc") {
          // itemConditionTc cannot fully function now, extra English keywords are also matched, need manual handle
          // can fix getRegexItemCondition("tc") later when available
          $tempConditions = array();
          for ($i = 0; $i < count($matches[1]); ++$i) {
            for ($j = 0; $j < count($this->itemConditionKeywords); ++$j) {
              if (strpos($matches[1][$i], $this->itemConditionKeywords[$j][1]) !== false) {
                $tempConditions[] = $this->itemConditionKeywords[$j][1];
                break;
              }
            } 
          }
          $matchValues[$key] = implode("\n", array_unique($tempConditions));
        } else {
          $matchValues[$key] = $matches[1][0];
        }
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
    $colWidth2 = "680";
    $separatorHeight = "8";
    $outputLotNum = $matchValues["lotNum"];

    echo "<div style='display:flex; justify-content: space-between; width: 800px'>";
      echo "<div><div style='display:inline-block;width:".$colWidth."px'>Lot Num:</div><input id='tbLotNum_$lotIndex' value='".str_replace("'", '"', $matchValues["lotNum"])."'></div>";
      if ($lotIndex > 0) {
        echo "<button onclick='CopyInfo($lotIndex)' style='margin-right: 12px'>Copy from above</button>";
      }
    echo "</div>";
    echo "<div style='height:".$separatorHeight."px'></div>";
    echo "<div style='display:flex;width:1400px'>";
      echo "<div style='width:800px'>";
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
        echo "<div style='height:".$separatorHeight."px'></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>Conditions</div>";
        echo "<textarea id='tbItemConditionEn_$lotIndex' style='width:".$colWidth2."px;height:60px'>".$matchValues['itemConditionEn']."</textarea></div>";
        echo "<div style='display:flex'><div style='width:".$colWidth."px'>狀態</div>";
        echo "<textarea id='tbItemConditionTc_$lotIndex' style='width:".$colWidth2."px;height:60px'>".$matchValues['itemConditionTc']."</textarea></div>";
      echo "</div>";
      echo "<div style='width:500px'>";
        echo "<textarea style='width:600px;height:515px' disabled='disabled'>$strAuction</textarea>";    
      echo "</div>";
    echo "</div>";
    echo "<br style='clear: both' />";
    echo "<div id='divItems_$lotIndex' style='width:1400px'>";
        $total = $this->extractItems($matchValues["items"], $lotIndex);
    echo "</div>";
    echo "<button id='btnAddItem_$lotIndex' data-total='$total' onclick='AddItem($lotIndex)'>+</button>";
    echo "<hr />";
  }

  //pre: 1.Handwriting Board 手寫板 48 Nos. (塊) 2. Barcode Reader (Model: MS9540) 條碼讀取器 2 Nos. (個)
  // split item text from the input first
  // then parse to the next method to build items
  function extractItems($strItems, $lotIndex) {
    // for debug
    // echo "extractItems: <br>";
    // echo $strItems;
    // echo "<hr>";
    $strItemList = array();
    $reachEnd = false;
    $curItemNum = 1;
    $curStrItems = $strItems;
    $regexNumberBullet = "/(\d+\.)/";
    while (!$reachEnd) {
      $nextItemNum = $curItemNum + 1;
      // try different search pattern for the number bullet, e.g.:
      // 1.
      // 1. xxx
      // MS301 1. xxx
      $endPos = strrpos($curStrItems, "\n".$nextItemNum.".\n");
      if ($endPos === false) {
        $endPos = strrpos($curStrItems, "\n".$nextItemNum.". ");
      } else if ($endPos === false) {
        $endPos = strrpos($curStrItems, " ".$nextItemNum.". ");
      }

      if ($endPos === false) {
        $reachEnd = true;
        $strItemList[] = preg_replace($regexNumberBullet, "", trim($curStrItems)); // remove the number bullets
      } else {
        $strItemList[] = preg_replace($regexNumberBullet, "", trim(substr($curStrItems, 0, $endPos)), 1);  // get the current item text from the current string
        $curStrItems = substr($curStrItems, $endPos);       // then remove that item from the current string
        ++$curItemNum;
      }
    }
  
    $total = Count($strItemList);
    $regexSkipItemCondition = $this->getRegexItemCondition("tc");
    for ($itemIndex = 0; $itemIndex < $total; ++$itemIndex) {
      $textList = explode("\n", $strItemList[$itemIndex]);
      $outputList = array();
      
      for ($i = 0; $i < count($textList); ++$i) {
        if (!preg_match($regexSkipItemCondition, $textList[$i])){
          $outputList[] = $textList[$i];
        }
      }
      // remove empty items from the array and re-index it from 0
      $outputList = array_values(array_filter($outputList));

      // check the output of current item first
      // 1. 5 lines;
      // 2. 3rd line (i.e. quantity) is a number; nd
      // 3. 5th line (i.e. unit in Chinese) has <= 6 characters
      $itemCorrect = count($outputList) == 5 && is_numeric(trim($outputList[2])) && mb_strlen(trim($outputList[4])) <= 6;

      if (!$itemCorrect) {
        // cannot copy line break from the PDF file, e.g. Bracelet/Bangle 手鐲/手鏈 59 Nos. (隻)
        // or weird format, e.g. multiple line breaks
        $outputList = $this->specialFixItemText($this->implodeItemLines($outputList));
      }
      
      $this->buildItems(implode("\n", $outputList), $lotIndex, $itemIndex);
    }

    return $total;
  }

  function implodeItemLines($itemTextList) {
    // special handle imploding Chinese and English characters
    $output = trim($itemTextList[0]);
    for ($i = 1; $i < count($itemTextList); ++$i) {
      $lastChar = mb_substr($output, -1);
      $nextChar = mb_substr($itemTextList[$i], 0, 1);
      $lastCharAsciiCode = mb_ord($lastChar);
      $nextCharAsciiCode = mb_ord($nextChar);
      $lastCharIsAscii = $lastChar == ")" || (65 <= $lastCharAsciiCode && $lastCharAsciiCode <= 90) || (97 <= $lastCharAsciiCode && $lastCharAsciiCode <= 122);
      $nextCharIsAscii = $nextChar == "(" || (65 <= $nextCharAsciiCode && $nextCharAsciiCode <= 90) || (97 <= $nextCharAsciiCode && $nextCharAsciiCode <= 122);

      if (($lastCharIsAscii && $nextCharIsAscii) || ($lastCharIsAscii && ctype_digit($nextChar)) || (ctype_digit($lastChar) && $nextCharAsciiCode)) {
        // if the end and start of characters are ASCII characters, add space in between
        $output .= " ";
      }
      $output .= trim($itemTextList[$i]);
    }

    return $output;
  }

  // special handle single line item text or weird format, e.g. Bracelet/Bangle 手鐲/手鏈 59 Nos. (隻)
  // because sometimes cannot copy line break from PDF file
  function specialFixItemText($itemText) {
    // for debug
    // echo "special fix: <br>";
    // Debug_var_dump($itemText);
    // echo "<hr>";
    $tempList = array();
    $startPos = 0;
    $i = 1;

    // 1. find the first Chinese characters
    while ($i < mb_strlen($itemText)) {
      if (mb_ord(mb_substr($itemText, $i, 1)) >= 19968) {
        break;
      }
      ++$i;
    }
    $tempList[] = trim(mb_substr($itemText, $startPos, $i - $startPos));
    $startPos = $i;
    if ($i >= mb_strlen($itemText) - 1) {
      return $tempList;
    }

    // 2. find the item quantity (i.e. digit followed by a space)
    while ($i < mb_strlen($itemText)) {
      if (ctype_digit(mb_substr($itemText, $i, 1)) && $this->isNumberFollowedBySpace(mb_substr($itemText, $i))) {
        break;
      }
      ++$i;
    }
    $tempList[] = trim(mb_substr($itemText, $startPos, $i - $startPos));
    $startPos = $i;
    if ($i >= mb_strlen($itemText) - 1) {
      return $tempList;
    }

    // 3. find the next ascii characters
    while ($i < mb_strlen($itemText)) {
      $asciiCode = mb_ord(mb_substr($itemText, $i, 1));
      if ((65 <= $asciiCode && $asciiCode <= 90) || (97 <= $asciiCode && $asciiCode <= 122) || mb_substr($itemText, $i, 1) == "(") {
        break;
      }
      ++$i;
    }
    $tempList[] = trim(mb_substr($itemText, $startPos, $i - $startPos));
    $startPos = $i;
    if ($i >= mb_strlen($itemText) - 1) {
      return $tempList;
    }

    // 4. find the next "(" or Chinese characters
    while ($i < mb_strlen($itemText)) {
      $chr = mb_substr($itemText, $i, 1);
      if ($chr == "(" || mb_ord($chr) >= 19968) {
        break;
      }
      ++$i;
    }
    $tempList[] = trim(mb_substr($itemText, $startPos, $i - $startPos));
    $startPos = $i;
    if ($i >= mb_strlen($itemText) - 1) {
      return $tempList;
    }

    $tempList[] = trim(mb_substr($itemText, $i));
    return $tempList;
  }

  //pre: 2. Barcode Reader (Model: MS9540) 條碼讀取器 2 Nos. (個)
  function buildItems($strItem, $lotIndex, $itemIndex) {
    $itemPropertyList = array_filter(explode("\n", $strItem));
    $id = "tbItem_$lotIndex"."_$itemIndex";
    $itemCorrect = count($itemPropertyList) == 5 && is_numeric(trim($itemPropertyList[2])) && mb_strlen(trim($itemPropertyList[4])) <= 6;
    // item correct conditions:
    // 1. 5 lines;
    // 2. 3rd line (i.e. quantity) is a number; nd
    // 3. 5th line (i.e. unit in Chinese) has <= 6 characters

    $className = "auction-item-textarea item" . str_pad($itemIndex+1, 2, "0", STR_PAD_LEFT);
    if (!$itemCorrect) {
      $className .= " wrong";
    }

    echo "<div style='display:inline-block;padding: 0 5px 5px 5px'>";
      echo "<textarea id='$id' class='$className' style='width:250px;height:100px' onkeyup='CheckTextarea(\"$id\")'>";
      echo trim($strItem);
      echo "</textarea>";
      echo "<div style='display:flex;justify-content:space-around'>";
      echo "<button style='width:50px;height:18px;font-size:12px' title='Move the 5th line and append to the English (1st) row (with space)' onclick='FixTextarea(\"".$id."\", \"en\")'>Fix En</button>";
        echo "<button style='width:50px;height:18px;font-size:12px' title='Move the 5th line and append to the Chinese (2nd) row (without space)' onclick='FixTextarea(\"".$id."\", \"tc\")'>Fix Tc</button>";
        echo "<button style='width:50px;height:18px;font-size:12px' title='Remove Move the 5th line' onclick='FixTextarea(\"".$id."\", \"\")'>−</button>";
        echo "<button style='width:50px;height:18px;font-size:16px;line-height:12px' title='Rollback to the original content' onclick='FixTextarea(\"".$id."\", \"undo\")'>⟲</button>";
      echo "</div>";
    echo "</div>";

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
    $regexLotNum = '/^([a-zA-Z]+-\d+)$/i';
    $lines = preg_split("/((\r?\n)|(\r\n?))/", $importText);
    
    $lotList = array();
    $i = 0;

    while ($i < Count($lines)) {
      $line = trim($lines[$i]);
      if (preg_match($regexLotNum, $line, $matches))
      {
        // lot num and price are on different line
        $lotNum = "";
        if (count($matches) > 1) {
          $lotNum = $matches[1];
        }

        $price = str_replace(",", "", str_replace("$", "", trim($lines[++$i])));
        $lotList[$lotNum] = $price;
      } else {
        // multiple lot num and price on the same line
        $tokenList = explode(" ", $line);
        $tokenCount = count($tokenList);
        
        $j = 0;
        while ($j < $tokenCount) {
          if (preg_match($regexLotNum, $tokenList[$j]) && ($j + 1 < $tokenCount) && !preg_match($regexLotNum, $tokenList[$j+1])) {
            $lotNum = $tokenList[$j];
            $price = str_replace(",", "", str_replace("$", "", trim($tokenList[++$j])));
            $lotList[$lotNum] = $price;
          }
          ++$j;
        }
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

  // utility function
  function isNumberFollowedBySpace($txt) {
    $i = 1;
    while ($i < mb_strlen($txt)) {
      $curChar = mb_substr($txt, $i, 1);
      if ($curChar == " ") {
        // find the space, the number is of length $i
        return $i;
      }

      if ($curChar != "." && !ctype_digit($curChar)) {
        // the number is followed by other characters, it is not the item quantity
        return 0;
      }

      // it is digit, countinue check the next char
      ++$i;
    }

    return $i;
  }
}
?>