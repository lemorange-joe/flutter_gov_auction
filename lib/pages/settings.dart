import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';

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
        leading: IconButton(
          icon: const Icon(MdiIcons.arrowLeft, color: Colors.white),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: const Text(
          'Settings',
          style: TextStyle(color: Colors.white),
        ),
        centerTitle: true,
      ),
      body: Center(
        child: Column(
          children: <Widget>[
            const SizedBox(height: 100.0),
            Text('Current: ${Intl.getCurrentLocale()} [${S.of(context).lang.toUpperCase()}]'),
            const SizedBox(height: 10.0),
            TextButton(
              onPressed: () async {
                await S.load(const Locale('en', 'US'));
                await HiveHelper().writeLocaleCode('en_US');
              },
              child: const Text('EN'),
            ),
            const SizedBox(height: 10.0),
            TextButton(
              onPressed: () async {
                await S.load(const Locale('zh', 'HK'));
                await HiveHelper().writeLocaleCode('zh_HK');
              },
              child: const Text('TC'),
            ),
            const SizedBox(height: 10.0),
            TextButton(
              onPressed: () async {
                await S.load(const Locale('zh', 'CN'));
                await HiveHelper().writeLocaleCode('zh_CN');
              },
              child: const Text('SC'),
            ),
            const SizedBox(height: 10.0),
          ],
        ),
      ),
    );
  }
}
