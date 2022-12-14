## change remote origin
git remote set-url origin https://github.com/joekytse/67b30e82-a005-75a88993-c0e3fe49d08d.git
git remote set-url origin https://github.com/lemorange-joe/flutter_government_auction.git

## TODO, 2022/9/19
- check firebase analytics 
## TODO, 2022/9/6
- continue setup push in iOS
https://firebase.flutter.dev/docs/messaging/apple-integration/#linking-apns-with-fcm


## TODO:
- standardize naming for SavedAuction, SavedAuctionLot
- auction lot tab:
  - hide app bar
  - change dense header color (ios issue?)
  - remove LOT from app bar
  - switch back to app, keep showing last viewed tab
  - click currently viewing tab bar item, scroll to list top
- Auction lot card:
  -  add inkwell on pressed
  -  show year for prev years
  -  fix featured icon and calendar min size
- auction list:
  - group auction by year
- click featured lot card open auction lot page directly
- auction lot page: item list change to selectable text
- saved page default show past saved auction if no upcoming saved
- fix link color for dark theme
- cannot show saved favourite reminder dialog in ios
  
- auction lot disclaimer
  - Special Conditions of Auction Sale
  - GLD pdf link
- replace LemorangeLoading widget with GLD's loading style
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
- test add and remove Auction Item Type

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
