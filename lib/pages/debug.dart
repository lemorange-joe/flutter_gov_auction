// import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_config/flutter_config.dart';
// import 'package:hive_flutter/hive_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
// import 'package:provider/provider.dart';
// import '../class/auction.dart';
// import '../generated/l10n.dart';
// import '../helpers/hive_helper.dart';
// import '../include/enums.dart';
// import '../includes/config.dart' as config;

class DebugPage extends StatelessWidget {
  const DebugPage({Key? key}) : super(key: key);

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
                ...buildFlutterConfigSection(context),
                const Divider(),
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
      DefaultTextStyle(
        style: const TextStyle(fontSize: 12.0, color: Colors.deepPurple),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0),
          child: Row(
            children: <Widget>[
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text('MODE: ${FlutterConfig.get("MODE")}', textAlign: TextAlign.start),
                  Text('VERSION: ${FlutterConfig.get("VERSION")}', textAlign: TextAlign.start),
                  Text('API_ROOT_URL: ${FlutterConfig.get("API_ROOT_URL")}', textAlign: TextAlign.start),
                ],
              )
            ],
          ),
        ),
      ),
    ];
  }
}
