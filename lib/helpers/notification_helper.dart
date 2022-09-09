// import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:logger/logger.dart';
// import '../widgets/common/snackbar.dart';

class NotificationHelper {
  factory NotificationHelper() {
    return _notificationHelper;
  }

  NotificationHelper._internal();

  bool isFlutterLocalNotificationsInitialized = false;
  late FirebaseMessaging messaging;
  late AndroidNotificationChannel channel;
  late FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin;
  static final NotificationHelper _notificationHelper = NotificationHelper._internal();

  Future<void> init() async {
    messaging = FirebaseMessaging.instance;
    flutterLocalNotificationsPlugin = FlutterLocalNotificationsPlugin();
    await setupFlutterNotifications();

    final RemoteMessage? initialMessage = await FirebaseMessaging.instance.getInitialMessage();

    if (initialMessage != null) {
      Logger().w('--- initial message ---');
      Logger().d(initialMessage);
    }
  }

  Future<bool> requestPermission() async {
    final NotificationSettings settings = await messaging.requestPermission();
    return settings.authorizationStatus == AuthorizationStatus.authorized || settings.authorizationStatus == AuthorizationStatus.provisional;
  }

  Future<String> getToken() async {
    final String? token = await FirebaseMessaging.instance.getToken();

    return token ?? '';
  }

  Future<void> setupFlutterNotifications() async {
    if (isFlutterLocalNotificationsInitialized) {
      return;
    }
    channel = const AndroidNotificationChannel(
      'auction_news', // id
      'Latest Auction News', // title
      description: 'This channel is used for receiving latest auction news.', // description
      importance: Importance.high,
    );

    /// Create an Android Notification Channel.
    ///
    /// We use this channel in the `AndroidManifest.xml` file to override the
    /// default FCM channel to enable heads up notifications.
    await flutterLocalNotificationsPlugin.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()?.createNotificationChannel(channel);

    /// Update the iOS foreground notification presentation options to allow
    /// heads up notifications.
    await FirebaseMessaging.instance.setForegroundNotificationPresentationOptions(
      alert: true,
      badge: true,
      sound: true,
    );
    isFlutterLocalNotificationsInitialized = true;
  }

  void listenForegroundMessage() {
    FirebaseMessaging.onMessage.listen((RemoteMessage message) async {
      showFlutterNotification(message);
    });

    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      Logger().w('Got a message opened App');
      Logger().i('Message data: ${message.data}');

      if (message.notification != null) {
        Logger().i('Message also contained a notification: ${message.notification}');
      }
    });
  }

  void showFlutterNotification(RemoteMessage message) {
    final RemoteNotification? notification = message.notification;
    final AndroidNotification? android = message.notification?.android;

    // Logger().w('showFlutterNotification!');
    // Logger().i(message.data);

    if (notification != null && android != null && !kIsWeb) {
      flutterLocalNotificationsPlugin.show(
        notification.hashCode,
        notification.title,
        notification.body,
        NotificationDetails(
          android: AndroidNotificationDetails(
            channel.id,
            channel.name,
            channelDescription: channel.description,
            icon: '@mipmap/ic_firebase_notification',
          ),
        ),
      );
    }
  }

  Future<String> subscribeNewsTopic(String lang) async {
    final String topic = 'news_${lang.toLowerCase()}';
    try {
      await FirebaseMessaging.instance.subscribeToTopic(topic);
      return topic;
    } catch (_) {
      return '';
    }
  }

  Future<bool> unsubscribeTopic(String topic) async {
    try {
      await FirebaseMessaging.instance.unsubscribeFromTopic(topic);
      return true;
    } catch (_) {
      return false;
    }
  }
}
