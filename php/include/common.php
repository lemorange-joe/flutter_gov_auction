<?php
function Debug_var_dump($var)
{
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}

//this function also appear in pinyindict.php
function GetSafeMySqlString($str)
{
	return str_replace('"', '""', str_replace('\'', '\'\'', str_replace('\\', '', $str)));
}

function RandomArray($curItemNo, $pickOutNo, $sorted)
{
	//pick out $maxRelatedWord number of char from the latest of related words pool
	$numbers = range(0, $curItemNo - 1);
	shuffle($numbers); //randomize the top items

	if ($sorted)
	{
		$arrayIndex = array_slice($numbers, 0, $pickOutNo);
		sort($arrayIndex); //sort the slice of the array
		return $arrayIndex;
	}
	else
	{
		return $numbers;
	}
}

function Slice2DArray($multiArray, $index)
{
	//return a single dimension array from a 2D array
	for ($i = 0; $i < count($multiArray); ++$i)
	{
		$returnArray[] = $multiArray[$i][$index];
	}

	return $returnArray;
}

function Combine2DArray($arr1, $arr2)
{
	//combine 2 1D array to a 2D array
	for ($i = 0; $i < count($arr1); ++$i)
	{
		$returnArray[$i][0] = $arr1[$i];
		$returnArray[$i][1] = $arr2[$i];
	}

	return $returnArray;
}

function utf8ord($c) {
    $h = ord($c[0]);
    if ($h <= 0x7F) {
        return $h;
    } else if ($h < 0xC2) {
        return false;
    } else if ($h <= 0xDF) {
        return ($h & 0x1F) << 6 | (ord($c[1]) & 0x3F);
    } else if ($h <= 0xEF) {
        return ($h & 0x0F) << 12 | (ord($c[1]) & 0x3F) << 6
                                 | (ord($c[2]) & 0x3F);
    } else if ($h <= 0xF4) {
        return ($h & 0x0F) << 18 | (ord($c[1]) & 0x3F) << 12
                                 | (ord($c[2]) & 0x3F) << 6
                                 | (ord($c[3]) & 0x3F);
    } else {
        return false;
    }
}

//this function also appear in chardict.php
function html_to_utf8($data)
{
    return preg_replace("/\\&\\#([0-9]{3,10})\\;/e", '_html_to_utf8("\\1")', $data);
}

function _html_to_utf8($data)
{
	if ($data > 127)
    {
        $i = 5;
        while (($i--) > 0)
        {
            if ($data != ($a = $data % ($p = pow(64, $i))))
            {
                $ret = chr(base_convert(str_pad(str_repeat(1, $i + 1), 8, "0"), 2, 10) + (($data - $a) / $p));
                for ($i; $i > 0; $i--)
                    $ret .= chr(128 + ((($data % pow(64, $i)) - ($data % ($p = pow(64, $i - 1)))) / $p));
                break;
            }
        }
	}
    else
    	$ret = "&#$data;";
    return $ret;
}

function Left($str, $howManyCharsFromLeft)
{
  return substr($str, 0, $howManyCharsFromLeft);
}

function Right($str, $howManyCharsFromRight)
{
  $strLen = strlen($str);
  return substr($str, $strLen - $howManyCharsFromRight, $strLen);
}

function IsSpecialChar($char)
{
	$isSpecial = false;
	$specialCharList = array("，",
							 "、",
							 "。",
							 "．",
							 "／",
							 "｜",
							 "－",
							 "：",
							 "；",
							 "（", "）",
							 "〔", "〕",
							 "｛", "｝",
							 "「", "」",
							 "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ",
							 "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
							 "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ",
							 "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
							 "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ"
							 );

	return in_array($char, $specialCharList);
}

function GetCharList($word) //assume all are chinese characters
{
	for ($i = 0; $i < strlen($word); $i+=3)
	{
		$returnCharList[] = substr($word, $i, 3);
	}

	return $returnCharList;
}

function GetLocalDateTime($timestamp) //from unix timestamp to "yyyy-mm-dd hh:mm:ss" format
{
	if (!preg_match('~^[1-9][0-9]*$~', $timestamp)) return false;
	return date('Y-m-d H:i:s', $timestamp);
}

function AES_128_Decrypt($encrypted_text, $password)
{
	$size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv   = mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);

	preg_match('/([\x20-\x7E]*)/',mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $password, pack("H*", $encrypted_text), MCRYPT_MODE_ECB, $iv), $a);
	return $a[0];
}


function AES_128_Encrypt($text, $password)
{
	$size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);

	// The following line was needed because I didn't get the same hex value as expected by forwarding agency
	// I think its their bug
	// Try to remove the line. If it works, too - fine!
	$text .= chr(3).chr(3).chr(3);

	return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $password, $text, MCRYPT_MODE_ECB, $iv));
}

function AES_256_Decrypt($encrypted_text, $password)
{
	$size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
	$iv   = mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);

	preg_match('/([\x20-\x7E]*)/',mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $password, pack("H*", $encrypted_text), MCRYPT_MODE_ECB, $iv), $a);
	return $a[0];
}

function AES_256_Encrypt($text, $password)
{

	$size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);

	// The following line was needed because I didn't get the same hex value as expected by forwarding agency
	// I think its their bug
	// Try to remove the line. If it works, too - fine!
	$text .= chr(3).chr(3).chr(3);

	return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $password, $text, MCRYPT_MODE_ECB, $iv));
}

//Ref: http://board.phpbuilder.com/showthread.php?10360595-TUTORIAL-Converting-from-MySQL-to-MySQLi
if (!function_exists('mysqli_result')) {
	function mysqli_result($result, $pos, $field)
	{
		$i=0;
		while ($row = $result->fetch_array(MYSQLI_BOTH)) {
			if ($i==$pos) return $row[$field];
			$i++;
		}
		return '';
	}
}
