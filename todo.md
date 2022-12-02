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
- parse contacts:
  - Item(s) 1-2: Ms WONG / Mr SO, Item(s) 3-5: Ms FONG / Mr WU, Mr HO / Miss WAN
  - 項目1-2: 黃女士 / 蘇先生 項目3-5: 方女士 / 吳先生 項目6: 何先生 / 尹小姐
  
  - 2860 2598 / 2860 2780, 2860 3485 / 2860 3488, 2860 5134 / 2860 5127
  
  - Item(s) 1-2: 26/F, Arsenal House, Police Headquarters, 1 Arsenal Street, Wan Chai, Hong Kong Item(s) 3-5: 21/F, Arsenal House West Wing, Police Headquarters, 1 Arsenal Street, Wan Chai, Hong KongItem(s) 6: 9/F, Arsenal House West Wing, Police Headquarters
  - 項目1-2: 香港灣仔軍器廠街1號警察總部警政大樓26樓 項目3-5: 香港灣仔軍器廠街1號警察總部警政大樓西翼21樓 項目6: 香港灣仔軍器廠街1號警察總部警政大樓西翼9樓

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
