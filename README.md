# gac_util

HKSAR Government Auction project

<ins>Branch: main</ins>

This is the flutter project template for other projects in future.

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

## 2. To generate hive class

- In "lib/class", create the class OR update the class then remove the .g.dart file
- Run the following command:
  > flutter pub run build_runner build


## Auction History
### 2023
1. 2023-01-12 10:30

### 2022
1. 2022-06-16 10:30
2. 2022-07-14 10:30
3. 2022-08-11 10:30
4. 2022-08-25 10:30
5. 2022-09-08 10:30
6. 2022-09-22 10:30
7. 2022-10-06 10:30
8. 2022-10-20 10:30
9. 2022-11-03 10:30
10. 2022-11-17 10:30
11. 2022-12-01 10:30
12. 2022-12-15 10:30

## Inspection Date Wordings Update
> Should {day_of_week} be a public holiday, the inspection day shall be the first working day immediately before the public holiday between {start_time} hours and {end_time} hours.

> (如該{day_of_week}為公眾假期，將改為公眾假期前第一個工作日{start_time}至{end_time}時。

is removed since 8/2022
https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-List-8-2022-en-zh-hk.pdf
