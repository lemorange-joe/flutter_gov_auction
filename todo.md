## change remote origin
git remote set-url origin https://github.com/joekytse/67b30e82-a005-75a88993-c0e3fe49d08d.git
git remote set-url origin https://github.com/lemorange-joe/flutter_government_auction.git

## TODO, 2022/9/9
- download firebase private key json file from firebase console, and set $GOOGLE_SERVICE_ACCOUNT_FILE_PATH in config
## TODO, 2022/9/6
- continue setup push in iOS
https://firebase.flutter.dev/docs/messaging/apple-integration/#linking-apns-with-fcm
- background cannot receive push


## TODO:
- add random data in API to prevent data theft
- check if the intent is necessary
  <intent-filter>
    <action android:name="com.google.firebase.MESSAGING_EVENT" />