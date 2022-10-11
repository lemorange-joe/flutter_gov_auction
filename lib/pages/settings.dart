import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:intl/intl.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../generated/l10n.dart';
import '../helpers/firebase_analytics_helper.dart';
import '../helpers/hive_helper.dart';
import '../helpers/notification_helper.dart';
import '../includes/config.dart' as config;
import '../providers/app_info_provider.dart';
import '../providers/auction_provider.dart';

class SettingsPage extends StatefulWidget {
  const SettingsPage({Key? key}) : super(key: key);

  @override
  State<SettingsPage> createState() => _SettingsPageState();
}

class _SettingsPageState extends State<SettingsPage> {
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
        title: Text(S.of(context).noticeToUser, style: const TextStyle(color: Colors.white, fontSize: 20.0)),
        centerTitle: true,
      ),
      body: SafeArea(
        child: Center(
          child: ValueListenableBuilder<Box<dynamic>>(
              valueListenable: Hive.box<dynamic>('preferences').listenable(),
              builder: (BuildContext context, _, __) {
                final String localeCode = HiveHelper().getLocaleCode();
                final int fontSize = HiveHelper().getFontSize();
                final String theme = HiveHelper().getTheme();
          
                return Column(
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
                            Provider.of<AuctionProvider>(context, listen: false).refreshLang(S.of(context).lang);
                          },
                          style: TextButton.styleFrom(
                            backgroundColor: localeCode == 'en_US' ? config.green : config.blue,
                            foregroundColor: Colors.white,
                          ),
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
                            Provider.of<AuctionProvider>(context, listen: false).refreshLang(S.of(context).lang);
                          },
                          style: TextButton.styleFrom(
                            backgroundColor: localeCode == 'zh_HK' ? config.green : config.blue,
                            foregroundColor: Colors.white,
                          ),
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
                            Provider.of<AuctionProvider>(context, listen: false).refreshLang(S.of(context).lang);
                          },
                          style: TextButton.styleFrom(
                            backgroundColor: localeCode == 'zh_CN' ? config.green : config.blue,
                            foregroundColor: Colors.white,
                          ),
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
                          style: TextButton.styleFrom(
                            backgroundColor: theme == 'light' ? config.green : config.blue,
                            foregroundColor: Colors.white,
                          ),
                          child: const Text('Light'),
                        ),
                        const SizedBox(width: 10.0),
                        TextButton(
                          onPressed: () async {
                            await HiveHelper().writeTheme('dark');
                          },
                          style: TextButton.styleFrom(
                            backgroundColor: theme == 'dark' ? config.green : config.blue,
                            foregroundColor: Colors.white,
                          ),
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
                          style: TextButton.styleFrom(
                            backgroundColor: fontSize == 75 ? config.green : config.blue,
                            foregroundColor: Colors.white,
                          ),
                          child: const Text('Small'),
                        ),
                        const SizedBox(width: 10.0),
                        TextButton(
                          onPressed: () async {
                            await HiveHelper().writeFontSize(100);
                          },
                          style: TextButton.styleFrom(
                            backgroundColor: fontSize == 100 ? config.green : config.blue,
                            foregroundColor: Colors.white,
                          ),
                          child: const Text('Mid'),
                        ),
                        const SizedBox(width: 10.0),
                        TextButton(
                          onPressed: () async {
                            await HiveHelper().writeFontSize(125);
                          },
                          style: TextButton.styleFrom(
                            backgroundColor: fontSize == 125 ? config.green : config.blue,
                            foregroundColor: Colors.white,
                          ),
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
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: <Widget>[
                        const Text('Allow analytics:'),
                        ValueListenableBuilder<Box<dynamic>>(
                          valueListenable: Hive.box<dynamic>('preferences').listenable(),
                          builder: (BuildContext context, _, __) {
                            final bool allow = HiveHelper().getAllowAnalytics();
                            return Switch(
                              value: allow,
                              activeColor: Colors.green,
                              onChanged: (bool value) async {
                                FirebaseAnalyticsHelper.enabled = value;
                                await HiveHelper().writeAllowAnalytics(value);
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
                );
              }),
        ),
      ),
    );
  }
}
