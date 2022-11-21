import 'package:intl/intl.dart';
// import 'package:logger/logger.dart';

class AppInfo {
  AppInfo(this.forceUpgrade, this.dataVersion, this.news, this.lastUpdate, this.noticeLinkList, this.messageList, this.itemTypeList, this.hotSearchList,
      this.catalogLocationList);

  factory AppInfo.fromJson(Map<String, dynamic> json) {
    final List<NoticeLink> noticeLinkList = <NoticeLink>[];
    final List<PushMessage> messageList = <PushMessage>[];
    final List<CatalogLocation> catalogLocationList = <CatalogLocation>[];
    final Map<String, String> itemTypeMap = <String, String>{};

    if (json['nll'] != null) {
      for (final dynamic jsonNoticeLink in json['nll'] as List<dynamic>) {
        noticeLinkList.add(NoticeLink.fromJson(jsonNoticeLink as Map<String, dynamic>));
      }
    }

    if (json['ml'] != null) {
      for (final dynamic jsonMsg in json['ml'] as List<dynamic>) {
        messageList.add(PushMessage.fromJson(jsonMsg as Map<String, dynamic>));
      }
    }

    json['cll'] = <dynamic>[  // TODO(joe): for testing only, to be removed!
      <String, dynamic>{'a': '香港北角\r\n渣華道333號\r\n北角政府合署10樓\r\n政府物流服務署接待處', 'm': '333 Java Road, North Point'}, 
      <String, dynamic>{'a': '香港柴灣\r\n創富道11號\r\n政府物料營運中心1樓', 'm': '11 Chong Fu Road, Chai Wan'}, 
      <String, dynamic>{'a': '香港中區立法會道1號\n立法會綜合大樓', 'm': '1 Legislative Council Road, Central'}, 
    ];
    if (json['cll'] != null) {
      for (final dynamic jsonMsg in json['cll'] as List<dynamic>) {
        catalogLocationList.add(CatalogLocation.fromJson(jsonMsg as Map<String, dynamic>));
      }
    }

    (json['itm'] as Map<String, dynamic>).forEach((String key, dynamic val) {
      itemTypeMap.addAll(<String, String>{key: val as String});
    });

    return AppInfo(
      false,
      json['dv'] as String,
      json['n'] as String,
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['lu'] as String),
      noticeLinkList,
      messageList,
      itemTypeMap,
      json['hsl'] == null ? <String>[] : List<String>.from(json['hsl'] as List<dynamic>),
      catalogLocationList,
    );
  }

  factory AppInfo.empty() {
    return AppInfo(true, '', '', DateTime.now(), <NoticeLink>[], <PushMessage>[], <String, String>{}, <String>[], <CatalogLocation>[]);
  }

  final bool forceUpgrade;
  final String dataVersion;
  final String news;
  final DateTime lastUpdate;
  final List<NoticeLink> noticeLinkList;
  final List<PushMessage> messageList;
  final Map<String, String> itemTypeList;
  final List<String> hotSearchList;
  final List<CatalogLocation> catalogLocationList;
}

class NoticeLink {
  NoticeLink(this.title, this.url);

  factory NoticeLink.fromJson(Map<String, dynamic> json) {
    return NoticeLink(
      json['t'] as String,
      json['u'] as String,
    );
  }

  final String title;
  final String url;
}

class PushMessage {
  PushMessage(this.pushId, this.title, this.body, this.pushDate);

  factory PushMessage.fromJson(Map<String, dynamic> json) {
    return PushMessage(
      json['id'] as int,
      json['t'] as String,
      json['b'] as String,
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['d'] as String),
    );
  }

  factory PushMessage.empty() {
    return PushMessage(0, '', '', DateTime.now());
  }

  final int pushId;
  final String title;
  final String body;
  final DateTime pushDate;
}


class CatalogLocation {
  CatalogLocation(this.address, this.mapAddress);

  factory CatalogLocation.fromJson(Map<String, dynamic> json) {
    return CatalogLocation(
      json['a'] as String,
      json['m'] as String,
    );
  }

  final String address;
  final String mapAddress;
}
