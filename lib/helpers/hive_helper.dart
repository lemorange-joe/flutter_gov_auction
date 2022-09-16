import 'package:hive/hive.dart';

class HiveHelper {
  factory HiveHelper() {
    return _hiveHelper;
  }

  HiveHelper._internal();

  static final Box<dynamic> _prefBox = Hive.box<dynamic>('preferences');
  static final Box<dynamic> _notificationBox = Hive.box<dynamic>('notification');
  static final Box<String> _historyBox = Hive.box<String>('history');
  static final HiveHelper _hiveHelper = HiveHelper._internal();

  Future<void> init(String path) async {
    Hive.init(path);
    await Hive.openBox<dynamic>('preferences');
    await Hive.openBox<dynamic>('notification');
    await Hive.openBox<String>('history');
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

  Future<void> writeSubscribeTopic(String topic) async {
    await _notificationBox.put('topic', topic);
  }

  bool getAllowNotification() {
    return _notificationBox.get('allow', defaultValue: false) as bool;
  }

  String getSubscribeTopic() {
    return _notificationBox.get('topic', defaultValue: '') as String;
  }
  // notification box
  // --------------------------------------------

  // --------------------------------------------
  // history box:
  Future<void> addReadMessage(int id) async {
    final List<int> readMessageList = getReadMessageList();
    readMessageList.add(id);
    await _historyBox.put('push_message', readMessageList.toSet().join(','));
  }

  Future<void> cleanReadMessage(List<int> idList) async {
    final List<int> readMessageList = getReadMessageList();
    readMessageList.removeWhere((int id) => !idList.contains(id));
    await _historyBox.put('push_message', readMessageList.join(','));
  }

  List<int> getReadMessageList() {
    return (_historyBox.get('push_message', defaultValue: '')!).split(',').map((String id) => id.isEmpty ? 0 : int.parse(id)).toList();
  }

  Future<void> clearPushMessage() async {
    await _historyBox.put('push_message', '');
  }
  // history box
  // --------------------------------------------
}
