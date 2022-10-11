// import 'dart:ui';
// import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:clear_all_notifications/clear_all_notifications.dart';
import 'package:flutter/material.dart';
// import 'package:flutter/services.dart';
import 'package:flutter_config/flutter_config.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/app_info.dart';
import '../generated/l10n.dart';
import '../helpers/easter_egg_helper.dart';
import '../helpers/firebase_analytics_helper.dart';
import '../helpers/hive_helper.dart';
import '../helpers/notification_helper.dart';
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
        title: const Text('🐛 Debug 🐛'),
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
                ...buildAuctionSection(context),
                const Divider(),
                ...buildHiveSection(context),
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
                    '喜際代我北拿巴我才早一第電外表國，究講我節些有',
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
                    '建政特有來都',
                    '無皮北的。這來了要路要始坐校等大可小一打本精畫，需推出！\n\n物上少魚動思……人賽之夠，技好進開能魚、多高及那行方子經呢。人興布部終生向形海不選言一，在間正喜活覺聲部功於，在色成了子那：合表是有樂分始然的結兒條……你兒作還辦很不那法回嗎聯色：臺苦小修經卻兩帶入中影，去道水有都著立親農千子劇成通外評幾人明民德的加的對且況片！\n\n充英位大究親南學我義你樹的好；過室他水斷；必行天百民他國：之之天安之賽沒想起天本的不得想上精我下自，性她越行時著實主子學合具以的間消人不體定吸不後風？包重一，況食蘭其河去朋本精這質國趣息環給管後日接為的。時人呢政……陸係真自一過你味你們異商服力報？當認兩西華性生用初讀如賽小器……金進些高是到東手接改是答。',
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
                    '分享連結',
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
          Text(_notifiationAuthorized != null ? _notifiationAuthorized.toString() : '-'),
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
          Expanded(child: Container()),
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
                    await HiveHelper().writeLog('測試記錄');
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
                    children: logList.map((MapEntry<String, String> entry) => Text('[${entry.key}] ${entry.value}')).toList());
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
