import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction_reminder.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';
import '../helpers/reminder_helper.dart';
import '../includes/config.dart' as config;
import '../widgets/common/dialog.dart';
import '../widgets/reminder_button.dart';

class ReminderPage extends StatelessWidget {
  const ReminderPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: config.green,
        leading: IconButton(
          icon: Semantics(
            label: S.of(context).semanticsGoBack,
            button: true,
            enabled: true,
            child: const Icon(MdiIcons.arrowLeft, color: Colors.white),
          ),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: Text(S.of(context).auctionReminder, style: const TextStyle(color: Colors.white, fontSize: 20.0)),
        actions: <Widget>[
          ValueListenableBuilder<Box<AuctionReminder>>(
            valueListenable: Hive.box<AuctionReminder>('reminder').listenable(),
            builder: (BuildContext context, _, __) {
              final bool deleteAllEnabled = HiveHelper().getAuctionReminderList().isNotEmpty;

              return deleteAllEnabled
                  ? IconButton(
                      onPressed: () async {
                        await CommonDialog.show2(
                          context,
                          S.of(context).deleteReminders,
                          S.of(context).confirmDeleteAllReminders,
                          S.of(context).ok,
                          () {
                            HiveHelper().clearAllAuctionReminder();
                            ReminderHelper().removeAllNotification();
                            Navigator.of(context).pop();
                          },
                          S.of(context).cancel,
                          () {
                            Navigator.of(context).pop();
                          },
                        );
                      },
                      icon: Semantics(
                        label: S.of(context).semanticsDeleteAllSaved,
                        button: true,
                        child: const Icon(
                          MdiIcons.deleteForeverOutline,
                          color: Colors.white,
                        ),
                      ),
                    )
                  : Container();
            },
          ),
        ],
        centerTitle: true,
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0),
          child: ValueListenableBuilder<Box<AuctionReminder>>(
            valueListenable: Hive.box<AuctionReminder>('reminder').listenable(),
            builder: (BuildContext context, _, __) {
              final List<AuctionReminder> reminderList = HiveHelper().getAuctionReminderList();

              return reminderList.isEmpty
                  ? Center(
                      child: Padding(
                        padding: EdgeInsets.only(bottom: MediaQuery.of(context).size.height * 0.3),
                        child: Text(S.of(context).auctionReminderEmpty),
                      ),
                    )
                  : ListView.builder(
                      itemCount: reminderList.length,
                      itemBuilder: (BuildContext context, int i) {
                        return Dismissible(
                          key: ValueKey<int>(reminderList[i].auctionId),
                          background: buildDismissibleBackground(context, AlignmentDirectional.centerStart),
                          secondaryBackground: buildDismissibleBackground(context, AlignmentDirectional.centerEnd),
                          onDismissed: (DismissDirection direction) {
                            ReminderHelper().removeNotification(reminderList[i].auctionId);
                            HiveHelper().deleteAuctionReminder(reminderList[i].auctionId);
                          },
                          child: buildReminderListItem(context, reminderList[i]),
                        );
                      },
                    );
            },
          ),
        ),
      ),
    );
  }

  Widget buildDismissibleBackground(BuildContext context, AlignmentDirectional alignment) {
    return Container(
      alignment: alignment,
      color: Colors.red,
      child: const Padding(
        padding: EdgeInsets.symmetric(horizontal: 24.0),
        child: Icon(
          MdiIcons.trashCanOutline,
          color: Colors.white,
          size: 32.0,
        ),
      ),
    );
  }

  Widget buildReminderListItem(BuildContext context, AuctionReminder reminder) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Text(
          reminder.remindTime.toString().replaceAll(' ', '\n'),
          style: const TextStyle(fontSize: 10.0),
        ),
        const SizedBox(width: 5.0),
        Expanded(
          child: Text(
            '(${reminder.auctionId}) Date: ${reminder.auctionStartTime}',
          ),
        ),
        const SizedBox(width: 5.0),
        ReminderButton(reminder, true),
      ],
    );
  }
}
