import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction_reminder.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';
import '../helpers/reminder_helper.dart';
import '../includes/config.dart' as config;

class ReminderButton extends StatelessWidget {
  const ReminderButton(this.auctionReminder, this.isSaved, {Key? key, this.buttonBackgroundColor = Colors.transparent}) : super(key: key);

  final AuctionReminder auctionReminder;
  final bool isSaved;
  final Color buttonBackgroundColor;

  @override
  Widget build(BuildContext context) {
    final bool auctionExpired = DateTime.now().compareTo(auctionReminder.auctionStartTime) > 0;
    final bool isAfterRemindTime = isSaved && DateTime.now().compareTo(auctionReminder.remindTime) > 0;

    return PopupMenuButton<int>(
      onSelected: (int minutesBefore) async {
        final DateTime reminderTime = auctionReminder.auctionStartTime.add(Duration(minutes: -minutesBefore));
        if (minutesBefore > 0) {
          if (reminderTime.isBefore(DateTime.now())) {
            // can shake the PopupMenuButton? TBC
            // Logger().w('reminder time is before now!');
          } else {
            // final DateTime newRemindTime = auctionReminder.auctionStartTime.add(Duration(minutes: -minutesBefore));  // for testing reminder
            final DateTime newRemindTime = DateTime.now().add(const Duration(seconds: 5));
            final AuctionReminder newReminder = auctionReminder.copyWith(remindTime: newRemindTime);

            if (isSaved) {
              await ReminderHelper().removeNotification(auctionReminder.lotId);
            }
            await ReminderHelper().addNotification(newReminder);
            await HiveHelper().writeAuctionReminder(newReminder);
          }
        } else if (isSaved) {
          await HiveHelper().deleteAuctionReminder(auctionReminder.lotId);
          await ReminderHelper().removeNotification(auctionReminder.lotId);
        }
      },
      itemBuilder: (BuildContext context) {
        final List<int> minutesBeforeList = List<int>.from(config.reminderMinutesBefore)..add(0);
        final List<PopupMenuEntry<int>> menuItemList = minutesBeforeList.map((int minute) {
          final bool reminderExpired = auctionReminder.auctionStartTime.add(Duration(minutes: -minute)).isBefore(DateTime.now());
          String itemText = '';
          if (minute == 0) {
            itemText = S.of(context).clearReminder;
          } else if (minute < 60) {
            itemText = '$minute${S.of(context).minutesBefore}';
          } else if (minute == 60) {
            itemText = '1${S.of(context).hourBefore}';
          } else if (minute < 1440) {
            itemText = '${(minute / 60).floor()}${S.of(context).hoursBefore}';
          } else if (minute == 1440) {
            itemText = '1${S.of(context).dayBefore}';
          } else {
            itemText = '${(minute / 1440).floor()}${S.of(context).daysBefore}';
          }

          return PopupMenuItem<int>(
            enabled: !reminderExpired || minute == 0,
            value: minute,
            child: Text(itemText),
          );
        }).toList();

        if (!isSaved) {
          // remove 0 minute (i.e. cancel) from the list
          menuItemList.removeLast();
        }
        return menuItemList;
      },
      enabled: !auctionExpired,
      child: Container(
        height: 45.0,
        decoration: BoxDecoration(
          color: buttonBackgroundColor,
          borderRadius: BorderRadius.circular(4.0),
        ),
        child: Semantics(
          label: S.of(context).semanticsTbc,
          child: Icon(
            isSaved ? (isAfterRemindTime ? MdiIcons.bellRing : MdiIcons.bell) : MdiIcons.bellOutline,
            color: auctionExpired ? Theme.of(context).disabledColor : Colors.yellow[800]!,
          ),
        ),
      ),
    );
  }
}
