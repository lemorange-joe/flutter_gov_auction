// import 'dart:ui';
// import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:clear_all_notifications/clear_all_notifications.dart';
import 'package:flutter/material.dart';
// import 'package:flutter/services.dart';
import 'package:flutter_config/flutter_config.dart';
// import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/app_info.dart';
import '../class/auction_reminder.dart';
import '../class/saved_auction.dart';
import '../generated/l10n.dart';
import '../helpers/easter_egg_helper.dart';
import '../helpers/firebase_analytics_helper.dart';
import '../helpers/hive_helper.dart';
import '../helpers/notification_helper.dart';
import '../helpers/reminder_helper.dart';
import '../includes/benchmark_data.dart' as benchmark_data;
import '../includes/config.dart' as config;
import '../includes/utilities.dart' as utilities;
import '../providers/app_info_provider.dart';
import '../providers/auction_provider.dart';
import '../widgets/common/dialog.dart';
import '../widgets/common/share.dart';
import '../widgets/common/snackbar.dart';
import '../widgets/easter_egg.dart';
// import '../widgets/related_apps.dart';
import '../widgets/ui/animated_loading.dart';
import '../widgets/ui/animated_logo.dart';

class DebugPage extends StatefulWidget {
  const DebugPage({Key? key}) : super(key: key);

  @override
  State<DebugPage> createState() => _DebugPageState();
}

class _DebugPageState extends State<DebugPage> {
  bool? _notifiationAuthorized;
  String _easterEggResult = '';
  late TextEditingController _easterEggController;
  List<String> benchmarkHistory = <String>[];

  @override
  void initState() {
    super.initState();

    _easterEggController = TextEditingController();
  }

  @override
  void dispose() {
    _easterEggController.dispose();

    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(MdiIcons.arrowLeft, color: Colors.white),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: const Text('???? Debug ????'),
        centerTitle: true,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Center(
            child: Column(
              children: <Widget>[
                const SizedBox(height: 10.0),
                ...buildFlutterConfigSection(context),
                const Divider(),
                ...buildGlobalWidgetSection(context),
                const Divider(),
                ...buildAppInfoSection(context),
                const Divider(),
                ...buildCryptographySection(context),
                const Divider(),
                ...buildAuctionSection(context),
                const Divider(),
                ...buildHiveSection(context),
                const Divider(),
                ...buildReminderSection(context),
                const Divider(),
                ...buildPushSection(context),
                const Divider(),
                ...buildFirebaseAnalyticsSection(context),
                const Divider(),
                ...buildLogSection(context),
                const Divider(),
                ...buildEasterEggSection(context),
                const Divider(),
                // const RelatedApps(),
                // const Divider(),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: <Widget>[
                    SizedBox(
                      height: 50.0,
                      width: 50.0,
                      child: LemorangeLogo(),
                    ),
                    LemorangeLoading(size: 60.0 * MediaQuery.of(context).textScaleFactor),
                  ],
                ),
                const Divider(),
                const SizedBox(height: 30.0),
              ],
            ),
          ),
        ),
      ),
    );
  }

  List<Widget> buildFlutterConfigSection(BuildContext context) {
    return <Widget>[
      const Text(
        'Flutter Config',
        style: TextStyle(decoration: TextDecoration.underline),
      ),
      const SizedBox(height: 10.0),
      DefaultTextStyle(
        style: TextStyle(fontSize: 12.0, color: Theme.of(context).textTheme.bodyText1!.color),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0),
          child: Row(
            children: <Widget>[
              Center(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    Text('MODE: ${FlutterConfig.get("MODE")}', textAlign: TextAlign.start),
                    Text('VERSION: ${FlutterConfig.get("VERSION")}', textAlign: TextAlign.start),
                    Text('API_URL: ${FlutterConfig.get("API_URL")}', textAlign: TextAlign.start),
                  ],
                ),
              )
            ],
          ),
        ),
      ),
      const SizedBox(height: 10.0),
      Text('Developer ID: ${HiveHelper().getDeveloper()}'),
      const SizedBox(height: 5.0),
      ElevatedButton(
        onPressed: () {
          HiveHelper().writeDeveloper('').then((_) {
            Navigator.of(context).pop();
          });
        },
        style: ElevatedButton.styleFrom(backgroundColor: Colors.black87),
        child: const Text('Remove developer'),
      ),
    ];
  }

  List<Widget> buildAppInfoSection(BuildContext context) {
    return <Widget>[
      const Text(
        'App Info Provider',
        style: TextStyle(decoration: TextDecoration.underline),
      ),
      const SizedBox(height: 10.0),
      DefaultTextStyle(
        style: const TextStyle(fontSize: 12.0, color: Colors.deepPurple),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0),
          child: Row(
            children: <Widget>[
              Consumer<AppInfoProvider>(
                builder: (BuildContext context, AppInfoProvider appInfo, Widget? _) {
                  return Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: <Widget>[
                      Text('Data Version: ${appInfo.dataVersion}', textAlign: TextAlign.start),
                      SizedBox(
                        width: MediaQuery.of(context).size.width - 24.0,
                        child: Text('News: ${appInfo.news}', textAlign: TextAlign.start),
                      ),
                      Text('Last Update: ${utilities.formatDateTime(appInfo.lastUpdate, S.of(context).lang)}', textAlign: TextAlign.start),
                      Text('Loaded: ${appInfo.loaded}', textAlign: TextAlign.start),
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: <Widget>[
                          const Text('Push: '),
                          Column(
                            children: appInfo.messageList.map((PushMessage msg) => Text('[${msg.pushDate}] ${msg.title}')).toList(),
                          )
                        ],
                      ),
                      const Text('Item types'),
                      ...appInfo.appInfo.itemTypeList.entries.map((MapEntry<String, String> itemType) => Text('${itemType.key} - ${itemType.value}')).toList(),
                    ],
                  );
                },
              )
            ],
          ),
        ),
      ),
    ];
  }

  List<Widget> buildCryptographySection(BuildContext context) {
    const String encrypted = 'NFpldEtCbStUdWlQWVR5Uk1laXR3ZzUrYzUrWFJYeFF4VlRUb0I1dGtvND0=';
    const String secret = 'ow2YByrZPpIa9YnETS9gFT3eVNAqEAVB';
    final String decrypted = utilities.extractApiPayload(encrypted, secret);
    return <Widget>[
      const Text(
        'Cryptography',
        style: TextStyle(decoration: TextDecoration.underline),
      ),
      const SizedBox(height: 10.0),
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12.0),
        child: Row(
          children: <Widget>[
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: <Widget>[
                const Text('Encrypted', style: TextStyle(fontWeight: FontWeight.bold)),
                SizedBox(
                  width: MediaQuery.of(context).size.width - 24.0,
                  child: const Text(encrypted),
                ),
                const SizedBox(height: 12.0),
                const Text('Decrypted', style: TextStyle(fontWeight: FontWeight.bold)),
                SizedBox(
                  width: MediaQuery.of(context).size.width - 24.0,
                  child: Text(decrypted),
                ),
                SizedBox(
                  width: MediaQuery.of(context).size.width - 24.0,
                  child: Center(
                    child: ElevatedButton(
                      onPressed: () {
                        setState(() {
                          benchmarkHistory.clear();

                          for (int i = 0; i < benchmark_data.encryptedList.length; ++i) {
                            final benchmark_data.AesBenchmarkData encryptedItem = benchmark_data.encryptedList[i];
                            final DateTime startTime = DateTime.now();
                            String result = 'Failed';
                            String decrypted = '';
                            try {
                              decrypted = utilities.extractApiPayload(encryptedItem.encrypted, encryptedItem.key);
                              result = 'Success';
                            } catch (_) {}

                            final DateTime endTime = DateTime.now();
                            benchmarkHistory.add('${i + 1}. $result, length: ${decrypted.length}, elapsed: ${endTime.difference(startTime).inMilliseconds}ms');
                          }
                        });
                      },
                      style: ElevatedButton.styleFrom(backgroundColor: Colors.purple[900]),
                      child: const Text('Start Benchmark'),
                    ),
                  ),
                ),
                if (benchmarkHistory.isNotEmpty)
                  const Text(
                    'Result',
                    style: TextStyle(decoration: TextDecoration.underline),
                  ),
                ...benchmarkHistory
                    .map((String history) => Text(
                          history,
                          style: const TextStyle(fontSize: 12.0),
                        ))
                    .toList(),
              ],
            ),
          ],
        ),
      ),
    ];
  }

  List<Widget> buildAuctionSection(BuildContext context) {
    return <Widget>[
      const Text(
        'Auction Provider',
        style: TextStyle(decoration: TextDecoration.underline),
      ),
      const SizedBox(height: 10.0),
      DefaultTextStyle(
        style: const TextStyle(fontSize: 12.0, color: Colors.deepPurple),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0),
          child: Row(
            children: <Widget>[
              Consumer<AuctionProvider>(
                builder: (BuildContext context, AuctionProvider auctionProvider, Widget? _) {
                  return Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: <Widget>[
                      Text('Loaded: ${auctionProvider.loaded}', textAlign: TextAlign.start),
                      Text('Loaded Details: ${auctionProvider.loadedDetails}', textAlign: TextAlign.start),
                    ],
                  );
                },
              )
            ],
          ),
        ),
      ),
    ];
  }

  List<Widget> buildHiveSection(BuildContext context) {
    return <Widget>[
      const Text(
        'Hive',
        style: TextStyle(decoration: TextDecoration.underline),
      ),
      const SizedBox(height: 10.0),
      DefaultTextStyle(
        style: TextStyle(fontSize: 12.0, color: Colors.blue[900]),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0),
          child: Row(
            children: <Widget>[
              ValueListenableBuilder<Box<String>>(
                valueListenable: Hive.box<String>('history').listenable(),
                builder: (BuildContext context, _, __) {
                  return Text(HiveHelper().getReadMessageList().join(', '));
                },
              )
            ],
          ),
        ),
      ),
      Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: <Widget>[
          ElevatedButton(
            onPressed: () {
              HiveHelper().clearPushMessage();
            },
            style: ElevatedButton.styleFrom(backgroundColor: Colors.pink[400]),
            child: const Text('Clear All'),
          ),
          ElevatedButton(
            onPressed: () {
              HiveHelper().cleanReadMessage(<int>[81, 83, 85, 87, 89]);
            },
            style: ElevatedButton.styleFrom(backgroundColor: Colors.pink[700]),
            child: const Text('Clean (81,83,85,87,89)'),
          ),
        ],
      ),
      const SizedBox(height: 10.0),
      const Text('Saved Auction'),
      const SizedBox(height: 5.0),
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12.0),
        child: ValueListenableBuilder<Box<SavedAuction>>(
          valueListenable: Hive.box<SavedAuction>('saved_auction').listenable(),
          builder: (BuildContext context, _, __) {
            final String lang = S.of(context).lang;
            return Column(
              children: HiveHelper()
                  .getSavedAuctionList()
                  .map((SavedAuction auction) => Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: <Widget>[
                          Text(auction.savedDate.toString().replaceAll(' ', '\n'), style: const TextStyle(fontSize: 10.0)),
                          const SizedBox(width: 5.0),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: <Widget>[
                              Text(
                                auction.auctionStartTime.toString(),
                                style: const TextStyle(fontWeight: FontWeight.bold),
                              ),
                              Text(
                                '${auction.auctionId}_${auction.lotNum}: ${auction.getDescription(lang)}',
                              ),
                            ],
                          ),
                        ],
                      ))
                  .toList(),
            );
          },
        ),
      ),
      const SizedBox(height: 5.0),
      ElevatedButton(
        onPressed: () async {
          await HiveHelper().clearAllSavedAuction();
        },
        style: ElevatedButton.styleFrom(backgroundColor: Colors.purple[300]),
        child: const Text('Clear saved auction'),
      ),
      const SizedBox(height: 10.0),
      const Text('Finished Tips'),
      const SizedBox(height: 5.0),
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12.0),
        child: ValueListenableBuilder<Box<bool>>(
          valueListenable: Hive.box<bool>('tips').listenable(),
          builder: (BuildContext context, _, __) {
            return Text(HiveHelper().getAllTips().join(', '));
          },
        ),
      ),
      const SizedBox(height: 5.0),
      ElevatedButton(
        onPressed: () async {
          await HiveHelper().clearAllTips();
        },
        style: ElevatedButton.styleFrom(backgroundColor: const Color.fromARGB(255, 93, 49, 101)),
        child: const Text('Clear all tips'),
      ),
    ];
  }

  List<Widget> buildReminderSection(BuildContext context) {
    return <Widget>[
      const Text(
        'Reminder',
        style: TextStyle(decoration: TextDecoration.underline),
      ),
      const SizedBox(height: 10.0),
      Row(
        children: <Widget>[
          Expanded(child: Container()),
          const Expanded(child: Text('Auction Reminder')),
          Expanded(
            child: TextButton(
              onPressed: () {
                final AuctionReminder reminder = AuctionReminder(
                  1234,
                  DateTime.now().add(const Duration(seconds: 5)),
                  DateTime.now().add(const Duration(days: 3)),
                );

                ReminderHelper().addNotification(reminder);
                HiveHelper().writeAuctionReminder(reminder);
              },
              child: Icon(
                MdiIcons.plusBox,
                color: Colors.green[700],
              ),
            ),
          ),
        ],
      ),
      const SizedBox(height: 5.0),
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12.0),
        child: ValueListenableBuilder<Box<AuctionReminder>>(
          valueListenable: Hive.box<AuctionReminder>('reminder').listenable(),
          builder: (BuildContext context, _, __) {
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
                              '(${reminder.auctionId}) ${reminder.auctionStartTime}',
                            ),
                          ),
                          const SizedBox(width: 5.0),
                          ElevatedButton(
                            onPressed: () {
                              ReminderHelper().removeNotification(reminder.auctionId);
                              HiveHelper().deleteAuctionReminder(reminder.auctionId);
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
      const SizedBox(height: 5.0),
      ElevatedButton(
        onPressed: () async {
          await ReminderHelper().removeAllNotification();
          await HiveHelper().clearAllAuctionReminder();
        },
        style: ElevatedButton.styleFrom(backgroundColor: Colors.green[400]),
        child: const Text(
          'Clear all reminders\n(from hive and local notifications)',
          textAlign: TextAlign.center,
        ),
      ),
    ];
  }

  List<Widget> buildGlobalWidgetSection(BuildContext context) {
    return <Widget>[
      const Text(
        'Show UI widgets',
        style: TextStyle(decoration: TextDecoration.underline),
      ),
      const SizedBox(height: 10.0),
      DefaultTextStyle(
        style: const TextStyle(fontSize: 12.0, color: Colors.deepPurple),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: <Widget>[
              ElevatedButton(
                onPressed: () {
                  CommonSnackbar.show(
                    context,
                    MdiIcons.wifi,
                    Colors.green,
                    'Lorem ipsum dolor sit amet consec',
                  );
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green,
                  shape: const CircleBorder(),
                  fixedSize: const Size(48, 48),
                ),
                child: const Icon(MdiIcons.wifi),
              ),
              ElevatedButton(
                onPressed: () {
                  CommonSnackbar.show(
                    context,
                    MdiIcons.wifiAlert,
                    Colors.grey,
                    '?????????????????????????????????????????????????????????????????????',
                    duration: 999999,
                    textColor: Colors.red[400],
                  );
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.grey,
                  shape: const CircleBorder(),
                  fixedSize: const Size(48, 48),
                ),
                child: const Icon(MdiIcons.wifiAlert),
              ),
              ElevatedButton(
                onPressed: () {
                  CommonDialog.show(
                    context,
                    'Lorem ipsum',
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ultricies facilisis aliquet. Nulla facilisi. Duis ac eros est. Donec luctus pharetra porttitor. Nulla interdum velit purus, vitae tincidunt arcu pretium in. Nulla facilisi. Phasellus libero urna, faucibus vitae posuere porttitor, porta quis elit. Sed nec tincidunt ante. Donec efficitur pharetra nulla a maximus.',
                    'OK',
                    () {
                      Navigator.pop(context);
                    },
                  );
                },
                style: ElevatedButton.styleFrom(
                  shape: const CircleBorder(),
                  fixedSize: const Size(48, 48),
                ),
                child: const Icon(MdiIcons.messageOutline),
              ),
              ElevatedButton(
                onPressed: () {
                  CommonDialog.show2(
                    context,
                    '??????????????????',
                    '????????????????????????????????????????????????????????????????????????????????????\n\n?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????\n\n???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????',
                    'OK',
                    () {
                      Navigator.pop(context);
                    },
                    'Cancel',
                    () {
                      Navigator.pop(context);
                    },
                  );
                },
                style: ElevatedButton.styleFrom(
                  shape: const CircleBorder(),
                  fixedSize: const Size(48, 48),
                ),
                child: const Icon(MdiIcons.messageProcessingOutline),
              ),
              ElevatedButton(
                onPressed: () {
                  CommonShare.share(
                    context,
                    'https://www.legco.gov.hk',
                    '????????????',
                  );
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.purple,
                  shape: const CircleBorder(),
                  fixedSize: const Size(48, 48),
                ),
                child: const Icon(MdiIcons.shareVariant),
              ),
            ],
          ),
        ),
      ),
      const SizedBox(height: 10.0),
      Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: <Widget>[
          ElevatedButton(
            onPressed: () {
              Navigator.pushNamed(context, 'tour', arguments: <String, dynamic>{'popPage': true});
            },
            style: ElevatedButton.styleFrom(backgroundColor: Colors.grey[800]),
            child: const Text('Tour'),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pushNamed(context, 'agreement');
            },
            style: ElevatedButton.styleFrom(backgroundColor: Colors.blueGrey[700]),
            child: const Text('Agreement'),
          ),
        ],
      ),
    ];
  }

  List<Widget> buildPushSection(BuildContext context) {
    return <Widget>[
      const Text(
        'Push',
        style: TextStyle(decoration: TextDecoration.underline),
      ),
      const SizedBox(height: 10.0),
      Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: <Widget>[
          const Text('Authorization Status: '),
          Text(_notifiationAuthorized != null ? _notifiationAuthorized.toString() : config.emptyCharacter),
        ],
      ),
      ElevatedButton(
        onPressed: () async {
          final bool result = await NotificationHelper().requestPermission();
          setState(() {
            _notifiationAuthorized = result;
          });
          NotificationHelper().getToken().then((String token) {
            Logger().i('token: $token');
          });
        },
        child: const Text('Request Permissions'),
      ),
      const SizedBox(height: 10.0),
      const Text('FCM Token'),
      const SizedBox(height: 5.0),
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12.0),
        child: FutureBuilder<String>(
          future: NotificationHelper().getToken(),
          builder: (BuildContext context, AsyncSnapshot<dynamic> snapshot) {
            if (snapshot.connectionState != ConnectionState.done) {
              return const CircularProgressIndicator();
            }

            return SelectableText(snapshot.data as String);
          },
        ),
      ),
      const SizedBox(height: 10.0),
      Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: <Widget>[
          ValueListenableBuilder<Box<dynamic>>(
            valueListenable: Hive.box<dynamic>('notification').listenable(),
            builder: (BuildContext context, _, __) {
              return Text('Subscribed: ${HiveHelper().getSubscribeTopic()}');
            },
          ),
        ],
      ),
      const SizedBox(height: 10.0),
      ElevatedButton(
        onPressed: () async {
          await ClearAllNotifications.clear();
        },
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.red,
        ),
        child: const Text('Clear All Badges'),
      ),
    ];
  }

  List<Widget> buildFirebaseAnalyticsSection(BuildContext context) {
    return <Widget>[
      const Text(
        'Firebase Analytics',
        style: TextStyle(decoration: TextDecoration.underline),
      ),
      const SizedBox(height: 10.0),
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12.0),
        child: Row(
          children: <Widget>[
            const Text('Enable (Hive): '),
            ValueListenableBuilder<Box<dynamic>>(
              valueListenable: Hive.box<dynamic>('preferences').listenable(),
              builder: (BuildContext context, _, __) {
                return Text(HiveHelper().getAllowAnalytics().toString());
              },
            ),
          ],
        ),
      ),
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12.0),
        child: Row(
          children: <Widget>[
            const Text('Enable (Helper): '),
            Text(FirebaseAnalyticsHelper.enabled.toString()),
          ],
        ),
      ),
      const SizedBox(height: 10.0),
      ElevatedButton(
        onPressed: () async {
          await FirebaseAnalyticsHelper().logEvent('test_log_event');
          Logger().d('[${DateTime.now()}] logged');
        },
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.yellow[800],
        ),
        child: const Text('Test Log Event'),
      ),
    ];
  }

  List<Widget> buildLogSection(BuildContext context) {
    return <Widget>[
      Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: <Widget>[
          Expanded(
            child: IconButton(
              onPressed: () async {
                await HiveHelper().clearAllLog();
              },
              icon: const Icon(MdiIcons.trashCan),
            ),
          ),
          const Text(
            'Hive Log',
            style: TextStyle(decoration: TextDecoration.underline),
          ),
          Expanded(
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: <Widget>[
                IconButton(
                  onPressed: () async {
                    await HiveHelper().writeLog('Test write log');
                  },
                  icon: const Icon(MdiIcons.plus),
                ),
                IconButton(
                  onPressed: () async {
                    await HiveHelper().writeLog('????????????');
                  },
                  icon: const Icon(MdiIcons.plusBox),
                ),
              ],
            ),
          ),
        ],
      ),
      const SizedBox(height: 10.0),
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12.0),
        child: Row(
          children: <Widget>[
            ValueListenableBuilder<Box<String>>(
              valueListenable: Hive.box<String>('log').listenable(),
              builder: (BuildContext context, _, __) {
                final List<MapEntry<String, String>> logList = HiveHelper().getAllLog();

                return Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: logList
                        .map(
                          (MapEntry<String, String> entry) => SizedBox(
                            width: MediaQuery.of(context).size.width - 24.0,
                            child: Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: <Widget>[
                                Text(
                                  '${entry.key.replaceAll(' ', '\n')}  ',
                                  style: const TextStyle(fontSize: 10.0),
                                ),
                                Flexible(child: Text(entry.value)),
                              ],
                            ),
                          ),
                        )
                        .toList());
              },
            ),
          ],
        ),
      ),
      const SizedBox(height: 10.0),
      ElevatedButton(
        onPressed: () async {
          await HiveHelper().clearAllLog();
        },
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.blueGrey,
        ),
        child: const Text('Clear All Log'),
      ),
    ];
  }

  List<Widget> buildEasterEggSection(BuildContext context) {
    return <Widget>[
      const Text(
        'Easter Egg',
        style: TextStyle(decoration: TextDecoration.underline),
      ),
      const SizedBox(height: 10.0),
      Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: <Widget>[
          SizedBox(
            width: 200.0,
            child: TextField(
              controller: _easterEggController,
            ),
          ),
          const SizedBox(width: 20.0),
          ElevatedButton(
            onPressed: () {
              setState(() {
                _easterEggResult = '${_easterEggController.text}: ${EasterEggHelper.check(context, _easterEggController.text)}';
              });
            },
            style: ElevatedButton.styleFrom(backgroundColor: Colors.yellow),
            child: const Text(
              'Check',
              style: TextStyle(color: Colors.black87),
            ),
          ),
        ],
      ),
      const SizedBox(height: 6.0),
      SizedBox(
        height: 30.0,
        child: Text(_easterEggResult),
      ),
      const SizedBox(height: 6.0),
      Container(
        decoration: BoxDecoration(
          boxShadow: const <BoxShadow>[BoxShadow(color: Colors.black26, offset: Offset(0, 4), blurRadius: 5.0)],
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            stops: const <double>[0.0, 1.0],
            colors: <Color>[
              Colors.red,
              Colors.yellow[400]!,
              // Colors.deepPurple.shade400,
              // Colors.deepPurple.shade200,
            ],
          ),
          // color: Colors.deepPurple.shade300,
          borderRadius: BorderRadius.circular(8.0),
        ),
        child: ElevatedButton(
          style: ButtonStyle(
            shape: MaterialStateProperty.all<RoundedRectangleBorder>(
              RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8.0),
              ),
            ),
            fixedSize: MaterialStateProperty.all(const Size(160, 50)),
            backgroundColor: MaterialStateProperty.all(Colors.transparent),
            // elevation: MaterialStateProperty.all(3),
            shadowColor: MaterialStateProperty.all(Colors.transparent),
          ),
          onPressed: () {
            showDialog<void>(
              context: context,
              barrierColor: Colors.transparent,
              builder: (BuildContext context) {
                return GestureDetector(
                  onTap: () {
                    Navigator.pop(context);
                  },
                  child: const EasterEgg(),
                );
              },
            );
          },
          child: const Padding(
            padding: EdgeInsets.symmetric(vertical: 1.0),
            child: Text(
              'Show Easter Egg',
              style: TextStyle(
                fontSize: 16.0,
                color: Colors.white,
              ),
            ),
          ),
        ),
      ),
      const SizedBox(height: 10.0),
    ];
  }
}
