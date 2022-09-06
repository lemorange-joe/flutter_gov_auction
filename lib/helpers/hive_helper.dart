import 'package:hive/hive.dart';

class HiveHelper {
  factory HiveHelper() {
    return _hiveHelper;
  }

  HiveHelper._internal();

  static final Box<dynamic> _prefBox = Hive.box<dynamic>('preferences');
  static final Box<dynamic> _notificationBox = Hive.box<dynamic>('notification');
  static final HiveHelper _hiveHelper = HiveHelper._internal();

  Future<void> init(String path) async {
    Hive.init(path);
    await Hive.openBox<dynamic>('preferences');
  }

  // --------------------------------------------
  // preferences box:
  Future<void> writeAgreed(bool val) async {
    await _prefBox.put('agreed', val);
  }

  Future<void> writeFontSize(int val) async {
    await _prefBox.put('fontSize', val);
  }

  Future<void> writeLocaleCode(String val) async {
    await _prefBox.put('localeCode', val);
  }

  Future<void> writeTheme(String val) async {
    await _prefBox.put('theme', val);
  }

  Future<void> writeFirstLaunch(DateTime val) async {
    await _prefBox.put('firstLaunch', val);
  }

  bool getAgreed() {
    return _prefBox.get('agreed', defaultValue: false) as bool;
  }

  int getFontSize() {
    return _prefBox.get('fontSize', defaultValue: 100) as int;
  }

  String getLocaleCode() {
    return _prefBox.get('localeCode', defaultValue: 'zh_HK') as String;
  }

  String getTheme() {
    return _prefBox.get('theme', defaultValue: 'light') as String;
  }

  DateTime getFirstLaunch() {
    return _prefBox.get('firstLaunch', defaultValue: DateTime(2022)) as DateTime;
  }
  // preferences box
  // --------------------------------------------


  // --------------------------------------------
  // notification box:
  Future<void> writeAllowNotification(bool val) async {
    await _notificationBox.put('allow', val);
  }

  Future<void> writeSubscribeChannel(List<String> channelList) async {
    await _notificationBox.put('channel', channelList.join(','));
  }

  bool getAllowNotification() {
    return _notificationBox.get('allow', defaultValue: false) as bool;
  }

  List<String> getSubscribeChannel() {
    return (_notificationBox.get('channel', defaultValue: '') as String).split(',');
  }
  // notification box
  // --------------------------------------------
}
