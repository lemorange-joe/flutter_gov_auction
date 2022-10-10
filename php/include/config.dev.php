<?php
// home dev
$ENV = 'dev';

$DB_HOST = "localhost";
$DB_USERNAME = "root";
$DB_PASSWORD = "root";
$DB_NAME = "gov_auction";

$FCM_ENDPOINT = "https://fcm.googleapis.com/v1/projects/gov-auction/messages:send";
$GOOGLE_SERVICE_ACCOUNT_FILE_PATH = "/temp/gov-auction-firebase-adminsdk-tyayj-98166bb8ea.json";

$CACHE_DIR = "/tmp";
$CACHE_PERIOD = 1 * 60;  // seconds
$RELATED_RECORD_PAGE_SIZE = 5;

$LOGIN_SALT = "lX9#30er";
$PUSH_PASSWORD_HASHED = "729780585edf5eb1d800fcb75f693296";

$PUSH_MESSAGE_DAYS = 30;
?>