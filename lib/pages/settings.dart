import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
// import 'package:intl/intl.dart';
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
  const SettingsPage(this.changeLangCallback, {Key? key}) : super(key: key);

  final Function? changeLangCallback;

  @override
  State<SettingsPage> createState() => _SettingsPageState();
}

class _SettingsPageState extends State<SettingsPage> {
  bool _savingFirebaseMessaging = false;

  @override
  Widget build(BuildContext context) {
    final ButtonStyle selectedButtonStyle = TextButton.styleFrom(
      backgroundColor: config.green,
      foregroundColor: Colors.white,
    );

    final ButtonStyle unselectedButtonStyle = TextButton.styleFrom(
      side: BorderSide(color: Theme.of(context).textTheme.bodyText1!.color!),
      backgroundColor: Theme.of(context).backgroundColor,
      foregroundColor: Theme.of(context).textTheme.bodyText1!.color,
    );

    final TextStyle fieldTextStyle = Theme.of(context).textTheme.bodyText1!.copyWith(
          fontSize: 15.0,
          fontWeight: FontWeight.bold,
        );

    return Scaffold(
      appBar: AppBar(
        backgroundColor: config.blue,
        leading: IconButton(
          icon: Semantics(
            label: S.of(context).semanticsGoBack,
            button: true,
            enabled: true,
            child: const Icon(MdiIcons.arrowLeft, color: Colors.white),
          ),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: Text(S.of(context).settings, style: const TextStyle(color: Colors.white, fontSize: 20.0)),
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
                    const SizedBox(height: 5.0),
                    // Text('Current: ${Intl.getCurrentLocale()} [${S.of(context).lang.toUpperCase()}]'),
                    Card(
                      elevation: 0.3,
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
                        child: Row(
                          children: <Widget>[
                            Text(
                              S.of(context).settingLanguage,
                              style: fieldTextStyle,
                            ),
                            Expanded(
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.end,
                                children: <Widget>[
                                  TextButton(
                                    onPressed: () async {
                                      final HiveHelper hiveHelper = HiveHelper();
                                      await S.load(const Locale('en', 'US'));
                                      await hiveHelper.writeLocaleCode('en_US');

                                      if (widget.changeLangCallback != null) {
                                        final Function callback = widget.changeLangCallback!;
                                        Function.apply(callback, null); // to prevent linting issue: avoid_dynamic_calls
                                      }

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
                                    style: localeCode == 'en_US' ? selectedButtonStyle : unselectedButtonStyle,
                                    child: const Text('English'),
                                  ),
                                  const SizedBox(width: 10.0),
                                  TextButton(
                                    onPressed: () async {
                                      final HiveHelper hiveHelper = HiveHelper();
                                      await S.load(const Locale('zh', 'HK'));
                                      await HiveHelper().writeLocaleCode('zh_HK');

                                      if (widget.changeLangCallback != null) {
                                        final Function callback = widget.changeLangCallback!;
                                        Function.apply(callback, null); // to prevent linting issue: avoid_dynamic_calls
                                      }

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
                                    style: localeCode == 'zh_HK' ? selectedButtonStyle : unselectedButtonStyle,
                                    child: const Text('繁體中文'),
                                  ),
                                  const SizedBox(width: 10.0),
                                  TextButton(
                                    onPressed: () async {
                                      final HiveHelper hiveHelper = HiveHelper();
                                      await S.load(const Locale('zh', 'CN'));
                                      await hiveHelper.writeLocaleCode('zh_CN');

                                      if (widget.changeLangCallback != null) {
                                        final Function callback = widget.changeLangCallback!;
                                        Function.apply(callback, null); // to prevent linting issue: avoid_dynamic_calls
                                      }

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
                                    style: localeCode == 'zh_CN' ? selectedButtonStyle : unselectedButtonStyle,
                                    child: const Text('简体中文'),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    Card(
                      elevation: 0.3,
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
                        child: Row(
                          children: <Widget>[
                            Text(
                              S.of(context).settingTheme,
                              style: fieldTextStyle,
                            ),
                            Expanded(
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.end,
                                children: <Widget>[
                                  TextButton(
                                    onPressed: () async {
                                      await HiveHelper().writeTheme('light');
                                    },
                                    style: theme == 'light' ? selectedButtonStyle : unselectedButtonStyle,
                                    child: Text(S.of(context).settingThemeLight),
                                  ),
                                  const SizedBox(width: 10.0),
                                  TextButton(
                                    onPressed: () async {
                                      await HiveHelper().writeTheme('dark');
                                    },
                                    style: theme == 'dark' ? selectedButtonStyle : unselectedButtonStyle,
                                    child: Text(S.of(context).settingThemeDark),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    Card(
                      elevation: 0.3,
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
                        child: Row(
                          children: <Widget>[
                            Text(
                              S.of(context).settingFontSize,
                              style: fieldTextStyle,
                            ),
                            Expanded(
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.end,
                                children: <Widget>[
                                  TextButton(
                                    onPressed: () async {
                                      await HiveHelper().writeFontSize(75);
                                    },
                                    style: fontSize == 75 ? selectedButtonStyle : unselectedButtonStyle,
                                    child: Text(S.of(context).settingFontSizeSmall),
                                  ),
                                  const SizedBox(width: 10.0),
                                  TextButton(
                                    onPressed: () async {
                                      await HiveHelper().writeFontSize(100);
                                    },
                                    style: fontSize == 100 ? selectedButtonStyle : unselectedButtonStyle,
                                    child: Text(S.of(context).settingFontSizeMid),
                                  ),
                                  const SizedBox(width: 10.0),
                                  TextButton(
                                    onPressed: () async {
                                      await HiveHelper().writeFontSize(125);
                                    },
                                    style: fontSize == 125 ? selectedButtonStyle : unselectedButtonStyle,
                                    child: Text(S.of(context).settingFontSizeLarge),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    Card(
                      elevation: 0.3,
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
                        child: Row(
                          children: <Widget>[
                            Expanded(
                              child: Text(
                                S.of(context).receiveNotification,
                                style: fieldTextStyle,
                              ),
                            ),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.end,
                              children: <Widget>[
                                ValueListenableBuilder<Box<dynamic>>(
                                  valueListenable: Hive.box<dynamic>('notification').listenable(),
                                  builder: (BuildContext context, _, __) {
                                    final String subscribedTopic = HiveHelper().getSubscribeTopic();
                                    return _savingFirebaseMessaging
                                        ? const Padding(
                                            padding: EdgeInsets.symmetric(horizontal: 20.0, vertical: 14.0),
                                            child: SizedBox(
                                              width: 20.0,
                                              height: 20.0,
                                              child: CircularProgressIndicator.adaptive(),
                                            ),
                                          )
                                        : Switch(
                                            value: subscribedTopic.isNotEmpty,
                                            activeColor: Colors.green,
                                            onChanged: (bool value) async {
                                              setState(() {
                                                _savingFirebaseMessaging = true;
                                              });
                                              if (value) {
                                                final String newTopic = await NotificationHelper().subscribeNewsTopic(S.of(context).lang);
                                                await HiveHelper().writeSubscribeTopic(newTopic);
                                              } else {
                                                await NotificationHelper().unsubscribeTopic(subscribedTopic);
                                                await HiveHelper().writeSubscribeTopic('');
                                              }
                                              setState(() {
                                                _savingFirebaseMessaging = false;
                                              });
                                            },
                                          );
                                  },
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),
                    Card(
                      elevation: 0.3,
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
                        child: Row(
                          children: <Widget>[
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: <Widget>[
                                  Text(
                                    S.of(context).allowAnalytics,
                                    style: fieldTextStyle,
                                  ),
                                  Text(
                                    S.of(context).allowAnalyticsDescription,
                                    style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 12.0),
                                  ),
                                ],
                              ),
                            ),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.end,
                              children: <Widget>[
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
                          ],
                        ),
                      ),
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
