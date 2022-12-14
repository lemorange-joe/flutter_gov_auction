## change remote origin
git remote set-url origin https://github.com/joekytse/67b30e82-a005-75a88993-c0e3fe49d08d.git
git remote set-url origin https://github.com/lemorange-joe/flutter_government_auction.git

## TODO, batch update AuctionLot.item_condition
UPDATE AuctionLot
SET item_condition_tc = '或不能再用/或不能正常操作/或已有損壞'
WHERE item_condition_en = 'May be Unserviceable/May Not Function Properly/May be Damaged' AND item_condition_tc = ''

UPDATE AuctionLot
SET item_condition_sc = '或不能再用/或不能正常操作/或已有损坏'
WHERE item_condition_en = 'May be Unserviceable/May Not Function Properly/May be Damaged' AND item_condition_sc = ''

## TODO, 2022/9/19
- check firebase analytics 
## TODO, 2022/9/6
- continue setup push in iOS
https://firebase.flutter.dev/docs/messaging/apple-integration/#linking-apns-with-fcm

## TODO:
- reminders:
  - test local notification reminder in ios
  - decide reminder message in ReminderHelper
  - update reminder page layout
- try: flutter_native_splash
- auto request push notification permission when start app
- add statistics page
  - chart widget: https://pub.dev/packages/fl_chart
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
