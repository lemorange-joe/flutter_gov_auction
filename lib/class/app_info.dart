import 'package:intl/intl.dart';

class AppInfo {
  AppInfo(this.dataVersion, this.news, this.lastUpdate);

  factory AppInfo.fromJson(Map<String, dynamic> json) {
    return AppInfo(
      json['dv'] as String,
      json['n'] as String,
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['lu'] as String),
    );
  }

  factory AppInfo.empty() {
    return AppInfo('', '', DateTime.now());
  }

  final String dataVersion;
  final String news;
  final DateTime lastUpdate;
}
