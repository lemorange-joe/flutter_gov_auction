import 'package:intl/intl.dart';
// import 'package:logger/logger.dart';

class AppInfo {
  AppInfo(this.dataVersion, this.news, this.lastUpdate, this.messageList);

  factory AppInfo.fromJson(Map<String, dynamic> json) {
    final List<PushMessage> messageList = <PushMessage>[];

    for (final dynamic jsonMsg in json['ml'] as List<dynamic>) {
      messageList.add(PushMessage.fromJson(jsonMsg as Map<String, dynamic>));
    }

    return AppInfo(
      json['dv'] as String,
      json['n'] as String,
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['lu'] as String),
      messageList,
    );
  }

  factory AppInfo.empty() {
    return AppInfo('', '', DateTime.now(), <PushMessage>[]);
  }

  final String dataVersion;
  final String news;
  final DateTime lastUpdate;
  final List<PushMessage> messageList;
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
