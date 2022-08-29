# gov_auction

HKSAR Government Auction project

## Getting Started

This project is a starting point for a Flutter application.

A few resources to get you started if this is your first Flutter project:

- [Lab: Write your first Flutter app](https://docs.flutter.dev/get-started/codelab)
- [Cookbook: Useful Flutter samples](https://docs.flutter.dev/cookbook)

For help getting started with Flutter development, view the
[online documentation](https://docs.flutter.dev/), which offers tutorials,
samples, guidance on mobile development, and a full API reference.

## FlutterConfig Environment Variables

### iOS

When deploying the app to iOS devices, the _ios/.envfile_ file **MUST** be updated to specify the required env file. The text should be put into the _ios/.envfile_ file according to the following modes:
Debug mode (in Visual Studio Code)

> .env.dev

Release mode (flutter run --release)

> .env.prod

### Android

When debugging or building release for Android, the corresponding env file will be included automatically as below:
Debug mode (in Visual Studio Code): _.env.dev_
Release mode (flutter run --release): _.env.prod_

Unlike iOS, there is no need to change any files/settings to load the required env file.

# Tips

## 1. To generate launcher icons

Run the following commands:

> flutter pub get
> flutter pub run flutter_launcher_icons:main