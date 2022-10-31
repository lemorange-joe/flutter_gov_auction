import 'package:intl/intl.dart';
// import 'package:logger/logger.dart';

class AppInfo {
  AppInfo(this.forceUpgrade, this.dataVersion, this.news, this.lastUpdate, this.noticeLinkList, this.messageList, this.itemTypeList);

  factory AppInfo.fromJson(Map<String, dynamic> json) {
    final List<NoticeLink> noticeLinkList = <NoticeLink>[];
    final List<PushMessage> messageList = <PushMessage>[];
    final Map<String, String> itemTypeMap = <String, String>{};

    for (final dynamic jsonNoticeLink in json['nll'] as List<dynamic>) {
      noticeLinkList.add(NoticeLink.fromJson(jsonNoticeLink as Map<String, dynamic>));
    }

    for (final dynamic jsonMsg in json['ml'] as List<dynamic>) {
      messageList.add(PushMessage.fromJson(jsonMsg as Map<String, dynamic>));
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
    );
  }

  factory AppInfo.empty() {
    return AppInfo(true, '', '', DateTime.now(), <NoticeLink>[], <PushMessage>[], <String, String>{});
  }

  final bool forceUpgrade;
  final String dataVersion;
  final String news;
  final DateTime lastUpdate;
  final List<NoticeLink> noticeLinkList;
  final List<PushMessage> messageList;
  final Map<String, String> itemTypeList;
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
