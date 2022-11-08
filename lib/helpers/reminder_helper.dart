import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:intl/intl.dart';
import 'package:logger/logger.dart';
import 'package:timezone/timezone.dart' as tz;
import '../class/auction_reminder.dart';
import '../class/time_zone.dart';
import '../generated/l10n.dart';

class ReminderHelper {
  factory ReminderHelper() {
    return _reminderHelper;
  }

  ReminderHelper._internal();

  static const String darwinNotificationCategoryText = 'textCategory';

  static final ReminderHelper _reminderHelper = ReminderHelper._internal();
  static const AndroidNotificationDetails androidNotificationDetails = AndroidNotificationDetails(
    'auction_reminder',
    'Auction Reminder',
    channelDescription: 'Auction app description...',
    importance: Importance.max,
    priority: Priority.high,
    icon: '@mipmap/ic_firebase_notification',
    showWhen: false,
  );
  static const DarwinNotificationDetails darwinNotificationDetails = DarwinNotificationDetails(
    categoryIdentifier: darwinNotificationCategoryText,
  );

  static const NotificationDetails notificationDetails = NotificationDetails(
    android: androidNotificationDetails,
    iOS: darwinNotificationDetails,
    macOS: darwinNotificationDetails,
  );

  late FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin;
  static late tz.Location location;

  Future<void> init() async {
    final TimeZone timeZone = TimeZone();
    final String timeZoneName = await timeZone.getTimeZoneName();
    location = await timeZone.getLocation(timeZoneName);
    tz.setLocalLocation(location);
    flutterLocalNotificationsPlugin = FlutterLocalNotificationsPlugin();
  }

  Future<int> addNotification(AuctionReminder reminder) async {
    try {
      await flutterLocalNotificationsPlugin.zonedSchedule(
        reminder.lotId, // use lotId as the notification ID
        S.current.auctionReminder,
        '[${DateFormat('HH:mm').format(reminder.remindTime)}] Lot: ${reminder.lotNum} @ ${DateFormat('yyyy-MM-dd HH:mm').format(reminder.auctionStartTime)}',
        tz.TZDateTime.from(reminder.remindTime, location),
        notificationDetails,
        uiLocalNotificationDateInterpretation: UILocalNotificationDateInterpretation.absoluteTime,
        androidAllowWhileIdle: true,
      );
    } on Exception catch (e) {
      Logger().e(e.toString());
      return 0;
    }

    return 1;
  }

  // use lotId as the notification ID
  Future<int> removeNotification(int id) async {
    try {
      await flutterLocalNotificationsPlugin.cancel(id);
    } on Exception catch (e) {
      Logger().e(e.toString());
      return 0;
    }

    return 1;
  }

  Future<int> removeAllNotification() async {
    try {
      await flutterLocalNotificationsPlugin.cancelAll();
    } on Exception catch (e) {
      Logger().e(e.toString());
      return 0;
    }

    return 1;
  }
}
