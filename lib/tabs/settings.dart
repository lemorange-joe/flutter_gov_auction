import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';
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
                  await S.load(const Locale('en', 'US'));
                  await HiveHelper().writeLocaleCode('en_US');

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
                  await S.load(const Locale('zh', 'HK'));
                  await HiveHelper().writeLocaleCode('zh_HK');

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
                  await S.load(const Locale('zh', 'CN'));
                  await HiveHelper().writeLocaleCode('zh_CN');

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
