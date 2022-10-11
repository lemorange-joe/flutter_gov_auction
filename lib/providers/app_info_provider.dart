import 'dart:async';
import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import '../class/app_info.dart';
import '../helpers/api_helper.dart';
import '../helpers/hive_helper.dart';

class AppInfoProvider with ChangeNotifier {
  AppInfoProvider();

  bool loaded = false;
  AppInfo appInfo = AppInfo.empty();

  Future<void> refresh({String lang = 'en'}) async {
    final ApiHelper apiHelper = ApiHelper();

    loaded = false;
    notifyListeners();

    try {
      final Map<String, dynamic> result = await apiHelper.get(lang, 'data', 'appinfo', useDemoData: true) as Map<String, dynamic>;
      appInfo = AppInfo.fromJson(result);
      loaded = true;
      // Logger().d(appInfo);
    } catch (e) {
      // Logger().e(e.toString());
      HiveHelper().writeLog('[App Info] ${e.toString()}');
    }

    notifyListeners();
  }

  String get dataVersion => appInfo.dataVersion;
  String get news => appInfo.news;
  DateTime get lastUpdate => appInfo.lastUpdate;
  List<PushMessage> get messageList => appInfo.messageList;
}
