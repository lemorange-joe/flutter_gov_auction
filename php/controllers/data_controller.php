<?php
class DataController {
  function getAppInfo($param) {
    echo 'getAppInfo: ' . date('Y/m/d H:i:s');
    Debug_var_dump($param);
    echo $_SERVER['SERVER_NAME'] . "@" . date('Y/m/d H:i:s');
  }
}
?>