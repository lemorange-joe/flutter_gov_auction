import 'package:firebase_messaging/firebase_messaging.dart';

class NotificationHelper {
  factory NotificationHelper() {
    return _notificationHelper;
  }

  NotificationHelper._internal();

  late FirebaseMessaging messaging;
  static final NotificationHelper _notificationHelper = NotificationHelper._internal();

  void init() {
    messaging = FirebaseMessaging.instance;
  }

  Future<bool> requestPermission() async {
    final NotificationSettings settings = await messaging.requestPermission();
    return settings.authorizationStatus == AuthorizationStatus.authorized || settings.authorizationStatus == AuthorizationStatus.provisional;
  }
}
