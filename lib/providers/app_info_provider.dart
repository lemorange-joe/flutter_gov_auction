import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_config/flutter_config.dart';
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
      final Map<String, dynamic> result = await apiHelper.post(lang, 'data', 'appinfo', parameters: <String, dynamic>{'version': FlutterConfig.get('VERSION')}) as Map<String, dynamic>;
      if (result['fu'] != null && result['fu'] as String == 'Y') {
        appInfo = AppInfo.empty();
      } else {
        appInfo = AppInfo.fromJson(result);
      }
      loaded = true;
      // Logger().d(appInfo);
    } catch (e) {
      // Logger().e(e.toString());
      HiveHelper().writeLog('[App Info] ${e.toString()}');
    }

    notifyListeners();
  }

  bool get forceUpgrade => appInfo.forceUpgrade;
  String get dataVersion => appInfo.dataVersion;
  String get news => appInfo.news;
  String get remarks => appInfo.remarks;
  int get upcomingAuctionId => appInfo.upcomingAuctionId;
  DateTime get lastUpdate => appInfo.lastUpdate;
  List<NoticeLink> get noticeLinkList => appInfo.noticeLinkList;
  List<PushMessage> get messageList => appInfo.messageList;
  String getItemTypeName(String typeCode) => appInfo.getItemTypeName(typeCode);
  List<String> get hotSearchList => appInfo.hotSearchList;
  List<CatalogLocation> get catalogLocationList => appInfo.catalogLocationList;
  Map<String, String> get gridCategoryList => appInfo.gridCategoryList;

  NoticeLink getAuctionStandardTandCLink(String defaultName, String lang)  {
    for(final NoticeLink noticeLink in appInfo.noticeLinkList) {
      if (noticeLink.url.contains('terms-and-conditions')) {
        return noticeLink;
      }
    }
    return NoticeLink(defaultName, FlutterConfig.get('GLD_WEBSITE').toString().replaceAll('{lang}', lang));
  }
}
