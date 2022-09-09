import 'dart:io';
import 'package:clear_all_notifications/clear_all_notifications.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_config/flutter_config.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:hive_flutter/hive_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:path_provider/path_provider.dart';
import 'package:provider/provider.dart';
import './firebase_options.dart';
import './generated/l10n.dart';
import './helpers/hive_helper.dart';
import './helpers/notification_helper.dart';
import './include/global.dart';
import './include/theme_data.dart';
import './providers/app_info_provider.dart';
import './routes.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized(); // Required by FlutterConfig
  final Directory appDocDir = await getApplicationDocumentsDirectory();
  await FlutterConfig.loadEnvVariables();
  await HiveHelper().init(appDocDir.path);
  await Firebase.initializeApp(options: DefaultFirebaseOptions.currentPlatform);

  await NotificationHelper().init();
  NotificationHelper().listenForegroundMessage();
  // have to set onBackgroundMessage as top level function
  FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);

  await ClearAllNotifications.clear();
  
  runApp(const MyApp());
}

Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  NotificationHelper().showFlutterNotification(message);
}

class MyApp extends StatefulWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  State<MyApp> createState() => _MyAppState();
}

class _MyAppState extends State<MyApp> {
  @override
  Widget build(BuildContext context) {
    SystemChrome.setPreferredOrientations(<DeviceOrientation>[
      DeviceOrientation.portraitUp,
    ]);

    return ValueListenableBuilder<Box<dynamic>>(
        valueListenable: Hive.box<dynamic>('preferences').listenable(),
        builder: (BuildContext context, _, __) {
          final HiveHelper hiveHelper = HiveHelper();

          return MultiProvider(
            providers: <ChangeNotifierProvider<dynamic>>[
              ChangeNotifierProvider<AppInfoProvider>(create: (_) => AppInfoProvider()),
            ],
            child: MaterialApp(
              title: 'Flutter Demo',
              scaffoldMessengerKey: globalScaffoldMessengerKey,
              theme: hiveHelper.getTheme() == 'dark' ? darkTheme : lightTheme,
              locale: Locale(
                hiveHelper.getLocaleCode().split('_')[0],
                hiveHelper.getLocaleCode().split('_')[1],
              ),
              localizationsDelegates: const <LocalizationsDelegate<dynamic>>[
                S.delegate,
                GlobalMaterialLocalizations.delegate,
                GlobalWidgetsLocalizations.delegate,
                GlobalCupertinoLocalizations.delegate,
              ],
              supportedLocales: S.delegate.supportedLocales,
              initialRoute: 'home',
              onGenerateRoute: Routes().getRoutes,
              builder: (BuildContext context, Widget? child) {
                final MediaQueryData data = MediaQuery.of(context);
                return MediaQuery(
                  data: data.copyWith(textScaleFactor: data.textScaleFactor * hiveHelper.getFontSize() / 100), // to be controlled by font size saved in hive
                  child: child!,
                );
              },
            ),
          );
        });
  }
}
