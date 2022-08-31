import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';

class SettingsTab extends StatelessWidget {
  const SettingsTab({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        children: <Widget>[
          const SizedBox(height: 100.0),
          Text('Current: ${Intl.getCurrentLocale()} [${S.of(context).lang.toUpperCase()}]'),
          const SizedBox(height: 10.0),
          const Divider(),
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
          const Divider(),
          TextButton(
            onPressed: () async {},
            child: const Text('Light'),
          ),
          TextButton(
            onPressed: () async {},
            child: const Text('Dark'),
          ),
        ],
      ),
    );
  }
}
