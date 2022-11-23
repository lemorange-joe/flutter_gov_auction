## change remote origin
git remote set-url origin https://github.com/joekytse/67b30e82-a005-75a88993-c0e3fe49d08d.git
git remote set-url origin https://github.com/lemorange-joe/flutter_government_auction.git

## TODO, 2022/9/19
- check firebase analytics
## TODO, 2022/9/9
- download firebase private key json file from firebase console, and set $GOOGLE_SERVICE_ACCOUNT_FILE_PATH in config
## TODO, 2022/9/6
- continue setup push in iOS
https://firebase.flutter.dev/docs/messaging/apple-integration/#linking-apns-with-fcm


## TODO:
- add: 提貨期限 collection deadline
ALTER TABLE `Auction` ADD collection_deadline datetime NULL AFTER start_time;
UPDATE `Auction` SET collection_deadline = '2022-07-08 16:00' WHERE auction_id = 1 AND auction_num = '1/2022';
UPDATE `Auction` SET collection_deadline = '2022-08-04 16:00' WHERE auction_id = 2 AND auction_num = '2/2022';
UPDATE `Auction` SET collection_deadline = '2022-09-01 16:00' WHERE auction_id = 3 AND auction_num = '3/2022';
UPDATE `Auction` SET collection_deadline = '2022-09-16 16:00' WHERE auction_id = 4 AND auction_num = '4/2022';
UPDATE `Auction` SET collection_deadline = '2022-09-30 16:00' WHERE auction_id = 5 AND auction_num = '5/2022';
UPDATE `Auction` SET collection_deadline = '2022-10-14 16:00' WHERE auction_id = 6 AND auction_num = '6/2022';
UPDATE `Auction` SET collection_deadline = '2022-10-27 16:00' WHERE auction_id = 7 AND auction_num = '7/2022';
UPDATE `Auction` SET collection_deadline = '2022-11-10 16:00' WHERE auction_id = 8 AND auction_num = '8/2022';
UPDATE `Auction` SET collection_deadline = '2022-11-24 16:00' WHERE auction_id = 9 AND auction_num = '9/2022';
UPDATE `Auction` SET collection_deadline = '2022-12-08 16:00' WHERE auction_id = 10 AND auction_num = '10/2022';
ALTER TABLE `Auction` MODIFY collection_deadline datetime NOT NULL;

承購人須要在2022年12月8日1600 時前或特別拍賣條款的指定時間從指定地點提取及運走所承購的拍賣品批次。

A Buyer shall collect and remove the Lot purchased from the Location on or before 1600 hours of 8 December 2022 or as otherwise specified in the Special Conditions of Auction Sale.
--------------

- admin: found empty gld ref/ref/dept/contact/number
- reminders:
  - test local notification reminder in ios
  - decide reminder message in ReminderHelper
  - update reminder page layout
- list item widget:
  - shown as forever scroll at the bottom of home page and auction lot page
  - recently sold
  - hot categories:
    - used cars
    - jewellery
    - digital products
    - books
- auto request push notification permission when start app
- add random data in API to prevent data theft

=================================================================

## Deliverables:
software
├ app
  ├ app name
  ├ admob
    ├ ref: https://developers.google.com/admob/android/app-open
    ├ app open ads
    ├ decide interstitial ads places
    ├ inline ads between auction lots/items
    └ search result show native / medium banenr ads
  └ push
    ├ APNs cert
    ├ manage topic groups in firebase console
    └ create test topic to test push before real publishing
└ server
  ├ hosting
  ├ sub-domain
  ├ api
    ├ url rewrite
    └ config update
  └ DB
    ├ import data
    ├ select featured lots
    ├ assign photos
    └ assign fontawesome icons
assets
├ import keyword photos
├ design
  ├ app logo
  └ press material
└ documents
  ├ declarations
  └ faq
LO website
├ portfolio
└ gauc app details in api
