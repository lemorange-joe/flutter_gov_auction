<?php
include_once ('../include/config.php');
include_once ('../include/adodb5/adodb.inc.php');
include_once ('../class/auction_item_type.php');

class AppData {
	private static $instance = null;

	private $auctionItemTypeList;

	public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new AppData();
    }
    return self::$instance;
  }

	private function __construct()
  {
		if (file_exists($GLOBALS["APP_DATA_FILE"])) {
			$data = unserialize(file_get_contents($GLOBALS["APP_DATA_FILE"]));
			$this->auctionItemTypeList = $data->auctionItemTypeList;
		} else {
			$this->refresh();
		}
  }

	function __get($prop) {
		return $this->$prop;
  }

  function __set($prop, $val) {
		$this->$prop = $val;
  }

	//invoke by other methods to notify $_APP get latest data from DB
	public function refresh()
	{
    $conn = new stdClass();
		$conn = ADONewConnection('mysqli');
		$conn->PConnect($GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'] , $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_NAME']);
    $conn->Execute("SET NAMES UTF8");

		$selectSql = "SELECT code, description_en, description_tc, description_sc FROM ItemType ORDER BY seq";
		$result = $conn->Execute($selectSql)->GetRows();
    $rowNum = count($result);

    $this->auctionItemTypeList = array();
    for($i = 0; $i < $rowNum; ++$i) {
        $this->auctionItemTypeList[] = new AuctionItemType($result[$i]['code'], $result[$i]['description_en'], $result[$i]['description_tc'], $result[$i]['description_sc']);
    }

		//save the current $_APP data into the data file
    $data = new StdClass();
		$data->auctionItemTypeList = $this->auctionItemTypeList;
		file_put_contents($GLOBALS["APP_DATA_FILE"], serialize($data));

    $conn->close();
	}
}
