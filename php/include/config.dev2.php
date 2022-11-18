<?php
// office dev
$ENV = 'dev';
$ADMIN_VERSION = '0.0.1';

$DB_HOST = "localhost";
$DB_USERNAME = "root";
$DB_PASSWORD = "root";
$DB_NAME = "gov_auction";

$FCM_ENDPOINT = "https://fcm.googleapis.com/v1/projects/gov-auction/messages:send";
$GOOGLE_SERVICE_ACCOUNT_FILE_PATH = "/Volumes/untitled/config/gov-auction-firebase-adminsdk-tyayj-98166bb8ea.json";

$AUCTION_IMAGE_FOLDER = "/Volumes/untitled/FlutterProjects/67b30e82-a005-75a88993-c0e3fe49d08d/php/images/";
$AUCTION_IMAGE_ROOT_URL = "http://gauc.local/images/";

$CACHE_DIR = "/tmp";
$CACHE_PERIOD = 1 * 60;  // seconds
$RELATED_RECORD_PAGE_SIZE = 5;
$APP_DATA_FILE = "/tmp/gauc.data";

$LOGIN_SALT = "lX9#30er";
$PUSH_PASSWORD_HASHED = "21a361d96e3e13f5f109748c2a9d2434"; //push

$ENABLE_DEVELOPER = true;
$DEVELOPER_GAUC_ID = "szQ91kQeak8SoJA7y4b3kQX4"; // for flutter app to pass to api to verify the request is from developer, can change anytime to invalidate previous developer id

$PUSH_MESSAGE_DAYS = 30;
?>