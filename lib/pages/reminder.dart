import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction_reminder.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';
import '../helpers/reminder_helper.dart';
import '../includes/config.dart' as config;

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
        title: Text(S.of(context).reminder, style: const TextStyle(color: Colors.white, fontSize: 20.0)),
        centerTitle: true,
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0),
          child: ValueListenableBuilder<Box<AuctionReminder>>(
            valueListenable: Hive.box<AuctionReminder>('reminder').listenable(),
            builder: (BuildContext context, _, __) {
              final String lang = S.of(context).lang;
              return Column(
                children: HiveHelper()
                    .getAuctionReminderList()
                    .map((AuctionReminder reminder) => Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: <Widget>[
                            Text(
                              reminder.remindTime.toString().replaceAll(' ', '\n'),
                              style: const TextStyle(fontSize: 10.0),
                            ),
                            const SizedBox(width: 5.0),
                            Expanded(
                              child: Text(
                                '(${reminder.lotId}) Date: ${reminder.auctionStartTime} \n${reminder.lotNum}: ${reminder.getDescription(lang)}',
                              ),
                            ),
                            const SizedBox(width: 5.0),
                            ElevatedButton(
                              onPressed: () {
                                ReminderHelper().removeNotification(reminder.lotId);
                                HiveHelper().deleteAuctionReminder(reminder.lotId);
                              },
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.green[600],
                              ),
                              child: const Icon(MdiIcons.trashCanOutline),
                            ),
                          ],
                        ))
                    .toList(),
              );
            },
          ),
        ),
      ),
    );
  }
}
