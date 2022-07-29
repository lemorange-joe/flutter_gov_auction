<?php
include_once ('../include/config.php');

if ($env == 'dev') {
  ini_set('display_errors', 'on');
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', '0');
  error_reporting(0);
  // error_reporting(E_ALL | E_STRICT);  # ...but do log them
}

$request = explode('-', $_REQUEST['req']);
$controller = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
$method = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
$param = array_shift($request);
$lang = "en";
var_dump($request);
if (!empty($request)) {
  $temp_lang = array_shift($request);
  if (strtolower($temp_lang) == "sc") {
    $lang = "sc";
  } else if (strtolower($temp_lang) == "ch") {
    $lang = "ch";
  }
}

echo 'controller: ' . $controller;
echo '<br>method: ' . $method;
echo '<br>lang: ' . $lang;
echo '<hr />';
// ========================================================================

class DataController {
  function getAppInfo($keyword) {
    echo 'getAppInfo: ' . date('Y/m/d H:i:s');
    echo '<br>keyword: ' . $keyword;
  }
}

// ========================================================================

if ($controller == "data") {
  $data = new DataController();

  if (method_exists($data, $method)) {
    $data->$method($param);
  }
}
?>