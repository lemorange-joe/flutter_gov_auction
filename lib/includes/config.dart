import 'package:flutter/painting.dart' show Color;

const String env = 'dev';
const String version = '0.1';
const String appCode = 'GAUC';
const String appStoreId = 'com.example.app';
const String appName = 'Example App Name';
const String lemorangeAppApiUrl = 'https://api.lemorange.studio/apps/?appcode={0}';
const String appStoreUrl = 'https://lemorange.studio';  // TBC!!!
const String googlePlayUrl = 'https://lemorange.studio';  // TBC!!!
const String supportEmail = 'info@lemorange.studio';
const int httpTimeout = 5; // in seconds

const String dateFormatEn = 'd MMMM yyyy (EEEE)';
const String dateFormatCh = 'yyyy年M月d日 (EEEE)';
const String shortDateFormat = 'yyyy-M-d';
const String timeFormatEn = 'H:mm a';
const String timeFormatCh = 'aH時mm分';
const String shortTimeFormat = 'HH:mm';

const int snackbarDuration = 5000;  // in milliseconds
const int transitionAnimationDuration = 250;  // in milliseconds

// same as GLD website color
const Color blue = Color.fromARGB(255, 0, 91, 172);
const Color green = Color.fromARGB(255, 0, 166, 80);

const double iconTextSpacing = 4.0;
const double lgBorderRadius = 20.0;
const double mdBorderRadius = 10.0;
const double smBorderRadius = 6.0;

const String searchSeparatorChar = '|';

const List<int> reminderMinutesBefore = <int>[15, 30, 60, 120, 1440, 2880];
