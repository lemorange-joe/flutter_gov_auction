import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';
import '../helpers/notification_helper.dart';
import '../providers/app_info_provider.dart';

class SettingsTab extends StatefulWidget {
  const SettingsTab({Key? key}) : super(key: key);

  @override
  State<SettingsTab> createState() => _SettingsTabState();
}

class _SettingsTabState extends State<SettingsTab> {
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        children: <Widget>[
          const SizedBox(height: 20.0),
          Text('Current: ${Intl.getCurrentLocale()} [${S.of(context).lang.toUpperCase()}]'),
          const SizedBox(height: 10.0),
          const Divider(),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              TextButton(
                onPressed: () async {
                  final HiveHelper hiveHelper = HiveHelper();
                  await S.load(const Locale('en', 'US'));
                  await hiveHelper.writeLocaleCode('en_US');

                  final String subscribedTopic = hiveHelper.getSubscribeTopic();
                  if (subscribedTopic.isNotEmpty) {
                    final NotificationHelper notificationHelper = NotificationHelper();
                    await notificationHelper.unsubscribeTopic(subscribedTopic); // 1. unsubscribe the old topic first
                    final String newTopic = await notificationHelper.subscribeNewsTopic('en'); // 2. subscribe the topic in new lang
                    hiveHelper.writeSubscribeTopic(newTopic); // 3. save the new topic in hive
                  }

                  if (!mounted) {
                    return;
                  }
                  Provider.of<AppInfoProvider>(context, listen: false).refresh(lang: S.of(context).lang);
                },
                child: const Text('EN'),
              ),
              const SizedBox(width: 10.0),
              TextButton(
                onPressed: () async {
                  final HiveHelper hiveHelper = HiveHelper();
                  await S.load(const Locale('zh', 'HK'));
                  await HiveHelper().writeLocaleCode('zh_HK');

                  final String subscribedTopic = hiveHelper.getSubscribeTopic();
                  if (subscribedTopic.isNotEmpty) {
                    final NotificationHelper notificationHelper = NotificationHelper();
                    await notificationHelper.unsubscribeTopic(subscribedTopic); // 1. unsubscribe the old topic first
                    final String newTopic = await notificationHelper.subscribeNewsTopic('tc'); // 2. subscribe the topic in new lang
                    hiveHelper.writeSubscribeTopic(newTopic); // 3. save the new topic in hive
                  }

                  if (!mounted) {
                    return;
                  }
                  Provider.of<AppInfoProvider>(context, listen: false).refresh(lang: S.of(context).lang);
                },
                child: const Text('TC'),
              ),
              const SizedBox(width: 10.0),
              TextButton(
                onPressed: () async {
                  final HiveHelper hiveHelper = HiveHelper();
                  await S.load(const Locale('zh', 'CN'));
                  await hiveHelper.writeLocaleCode('zh_CN');

                  final String subscribedTopic = hiveHelper.getSubscribeTopic();
                  if (subscribedTopic.isNotEmpty) {
                    final NotificationHelper notificationHelper = NotificationHelper();
                    await notificationHelper.unsubscribeTopic(subscribedTopic); // 1. unsubscribe the old topic first
                    final String newTopic = await notificationHelper.subscribeNewsTopic('sc'); // 2. subscribe the topic in new lang
                    hiveHelper.writeSubscribeTopic(newTopic); // 3. save the new topic in hive
                  }

                  if (!mounted) {
                    return;
                  }
                  Provider.of<AppInfoProvider>(context, listen: false).refresh(lang: S.of(context).lang);
                },
                child: const Text('SC'),
              ),
            ],
          ),
          const Divider(),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              TextButton(
                onPressed: () async {
                  await HiveHelper().writeTheme('light');
                },
                child: const Text('Light'),
              ),
              const SizedBox(width: 10.0),
              TextButton(
                onPressed: () async {
                  await HiveHelper().writeTheme('dark');
                },
                child: const Text('Dark'),
              ),
            ],
          ),
          const Divider(),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              TextButton(
                onPressed: () async {
                  await HiveHelper().writeFontSize(75);
                },
                child: const Text('Small'),
              ),
              const SizedBox(width: 10.0),
              TextButton(
                onPressed: () async {
                  await HiveHelper().writeFontSize(100);
                },
                child: const Text('Mid'),
              ),
              const SizedBox(width: 10.0),
              TextButton(
                onPressed: () async {
                  await HiveHelper().writeFontSize(125);
                },
                child: const Text('Large'),
              ),
            ],
          ),
          const Divider(),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              const Text('Receive notification'),
              ValueListenableBuilder<Box<dynamic>>(
                valueListenable: Hive.box<dynamic>('notification').listenable(),
                builder: (BuildContext context, _, __) {
                  final String subscribedTopic = HiveHelper().getSubscribeTopic();
                  return Switch(
                    value: subscribedTopic.isNotEmpty,
                    activeColor: Colors.green,
                    onChanged: (bool value) async {
                      if (value) {
                        final String newTopic = await NotificationHelper().subscribeNewsTopic(S.of(context).lang);
                        await HiveHelper().writeSubscribeTopic(newTopic);
                      } else {
                        await NotificationHelper().unsubscribeTopic(subscribedTopic);
                        await HiveHelper().writeSubscribeTopic('');
                      }
                    },
                  );
                },
              ),
            ],
          ),
          const Divider(),
          const SizedBox(height: 15.0),
          ElevatedButton(
            onPressed: () {
              Navigator.pushNamed(context, 'debug');
            },
            child: const Text('Debug Page'),
          ),
          const SizedBox(height: 15.0),
        ],
      ),
    );
  }
}
