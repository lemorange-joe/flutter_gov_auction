<?php
// home dev
$ENV = 'dev';
$ADMIN_VERSION = '0.0.1';

$DB_HOST = "localhost";
$DB_USERNAME = "root";
$DB_PASSWORD = "root";
$DB_NAME = "gov_auction";

$FCM_ENDPOINT = "https://fcm.googleapis.com/v1/projects/gov-auction/messages:send";
$GOOGLE_SERVICE_ACCOUNT_FILE_PATH = "/temp/gov-auction-firebase-adminsdk-tyayj-98166bb8ea.json";

$AUCTION_IMAGE_FOLDER = "/tmp/";
$AUCTION_IMAGE_ROOT_URL = "http://gauc.local/images/";

$CACHE_DIR = "/tmp";
$CACHE_PERIOD = 1 * 60;  // seconds
$APP_DATA_FILE = "/tmp/gauc.data";

$LOGIN_SALT = "lX9#30er";
$PUSH_PASSWORD_HASHED = "729780585edf5eb1d800fcb75f693296";

$ENABLE_DEVELOPER = true;
$DEVELOPER_GAUC_ID = "oJA7y4b3kQX4szQ91kQeak8S"; // for flutter app to pass to api to verify the request is from developer, can change anytime to invalidate previous developer id

$PUSH_MESSAGE_DAYS = 30;
?>