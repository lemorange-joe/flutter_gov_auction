## change remote origin
git remote set-url origin https://github.com/joekytse/67b30e82-a005-75a88993-c0e3fe49d08d.git
git remote set-url origin https://github.com/lemorange-joe/flutter_government_auction.git

## TODO, 2022/12/29
INSERT INTO `InspectionDate`(`lot_id`, `inspection_day`, `inspection_start_time`, `inspection_end_time`) VALUES (0,1,'09:00','12:30');
INSERT INTO `InspectionDate`(`lot_id`, `inspection_day`, `inspection_start_time`, `inspection_end_time`) VALUES (0,1,'14:00','16:00');
INSERT INTO `InspectionDate`(`lot_id`, `inspection_day`, `inspection_start_time`, `inspection_end_time`) VALUES (0,2,'09:00','12:30');
INSERT INTO `InspectionDate`(`lot_id`, `inspection_day`, `inspection_start_time`, `inspection_end_time`) VALUES (0,2,'14:00','16:00');

## TODO, 2022/12/29
! Verify inspection date list is empty !
https://gauc.lemorange.studio/en/api/admin-getAuction-12-0-C
inspection_date_list

## TODO, 2022/9/19
- check firebase analytics 
## TODO, 2022/9/6
- continue setup push in iOS
https://firebase.flutter.dev/docs/messaging/apple-integration/#linking-apns-with-fcm

## TODO:
- confirm the format of the inspection dates field
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

## Before Launch ✓/✗:
- test add and remove Item Type

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
  ├ ✓ hosting
  ├ ✓ sub-domain
  ├ api
    ├ ✓ url rewrite
    └ ✓ config update
  └ DB
    ├ ✓ import data
    ├ ✓ select featured lots
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
