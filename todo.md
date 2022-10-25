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
- favourite auction save descriptions in hive, do not query api when showing favourite list 
- swipe prev/next auction lot
- add random data in API to prevent data theft
- notice:

Application Form for Inclusion in the Registered Person List for Participation in Auctions
https://www.gld.gov.hk/assets/gld/download-files/auction/GLD-Auction-Bidder-Application.pdf

Standard Terms and Conditions of Auction Sale (Rev. January 2021)
https://www.gld.gov.hk/assets/gld/download-files/auction/terms-and-conditions-en-2021-01.pdf

Warning and Debarment System for Auctions held by Government Logistics Department (Rev. January 2021)
https://www.gld.gov.hk/assets/gld/download-files/auction/Warning-and-Debarment-System-en-2021-01.pdf

----
參與拍賣的競投人士登記申請書
https://www.gld.gov.hk/assets/gld/download-files/auction/GLD-Auction-Bidder-Application.pdf

拍賣標準條款及條件(2021年1月修訂版)
https://www.gld.gov.hk/assets/gld/download-files/auction/terms-and-conditions-zh-hk-2021-01.pdf

政府物流服務署所舉行拍賣的警告及禁制制度(2021年1月修訂版)
https://www.gld.gov.hk/assets/gld/download-files/auction/Warning-and-Debarment-System-zh-hk-2021-01.pdf

----
参与拍卖的竞投人士登记申请书
https://www.gld.gov.hk/assets/gld/download-files/auction/GLD-Auction-Bidder-Application.pdf

拍卖标准条款及条件(2021年1月修订版)
https://www.gld.gov.hk/assets/gld/download-files/auction/terms-and-conditions-zh-cn-2021-01.pdf

政府物流服务署所举行拍卖的警告及禁制制度(2021年1月修订版)
https://www.gld.gov.hk/assets/gld/download-files/auction/Warning-and-Debarment-System-zh-cn-2021-01.pdf


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
    ├ password protect
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
