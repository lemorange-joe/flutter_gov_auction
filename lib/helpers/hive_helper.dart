import 'package:hive/hive.dart';
// import 'package:logger/logger.dart';
import '../class/auction_reminder.dart';
import '../class/saved_auction.dart';
import '../includes/config.dart' as config;
import '../includes/utilities.dart' as utilities;

class HiveHelper {
  factory HiveHelper() {
    return _hiveHelper;
  }

  HiveHelper._internal();

  static final Box<dynamic> _prefBox = Hive.box<dynamic>('preferences');
  static final Box<dynamic> _notificationBox = Hive.box<dynamic>('notification');
  static final Box<String> _historyBox = Hive.box<String>('history');
  static final Box<String> _searchHistoryBox = Hive.box<String>('search_history');
  static final Box<SavedAuction> _savedAuctionBox = Hive.box<SavedAuction>('saved_auction');
  static final Box<AuctionReminder> _reminderBox = Hive.box<AuctionReminder>('reminder');
  static final Box<bool> _tipsBox = Hive.box<bool>('tips'); // keys: check_auction_details_on_gld_website
  static final Box<String> _logBox = Hive.box<String>('log');

  static bool _enableLog = false;
  static final HiveHelper _hiveHelper = HiveHelper._internal();

  Future<void> init(String path, bool enableLog) async {
    Hive.registerAdapter(SavedAuctionAdapter());
    Hive.registerAdapter(AuctionReminderAdapter());

    Hive.init(path);
    _enableLog = enableLog;

    await Hive.openBox<dynamic>('preferences');
    await Hive.openBox<dynamic>('notification');
    await Hive.openBox<String>('history');
    await Hive.openBox<String>('search_history');
    await Hive.openBox<SavedAuction>('saved_auction');
    await Hive.openBox<AuctionReminder>('reminder');
    await Hive.openBox<bool>('tips');
    if (_enableLog) {
      await Hive.openBox<String>('log');
    }
  }

  // --------------------------------------------
  // preferences box:
  Future<void> writeAgreed(bool val) async {
    await _prefBox.put('agreed', val);
  }

  Future<void> writeAllowAnalytics(bool val) async {
    await _prefBox.put('allow_analytics', val);
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

  Future<void> writeFirstLaunch(bool val) async {
    await _prefBox.put('first_launch', val);
  }

  Future<void> writeDeveloper(String val) async {
    await _prefBox.put('developer', val);
  }

  bool getAgreed() {
    return _prefBox.get('agreed', defaultValue: false) as bool;
  }

  bool getAllowAnalytics() {
    return _prefBox.get('allow_analytics', defaultValue: false) as bool;
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

  bool getFirstLaunch() {
    return _prefBox.get('first_launch', defaultValue: true) as bool;
  }

  String getDeveloper() {
    return _prefBox.get('developer', defaultValue: '') as String;
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

  // --------------------------------------------
  // search history box:
  Future<void> writeSearchHistory(String searchKeyword) async {
    final List<String> searchHistoryList = getSearchHistoryList();
    if (searchHistoryList.contains(searchKeyword)) {
      searchHistoryList.remove(searchKeyword);
    }
    searchHistoryList.insert(0, searchKeyword);
    await _searchHistoryBox.put('auction_search', searchHistoryList.join(config.searchSeparatorChar));
  }

  List<String> getSearchHistoryList() {
    final String strHistory = _searchHistoryBox.get('auction_search', defaultValue: '')!;
    return strHistory.isEmpty ? <String>[] : strHistory.split(config.searchSeparatorChar);
  }

  Future<void> clearSearchHistory() async {
    await _searchHistoryBox.clear();
  }
  // search history box
  // --------------------------------------------

  // --------------------------------------------
  // saved auction box:
  Future<void> writeSavedAuction(SavedAuction auction) async {
    await _savedAuctionBox.put(auction.hiveKey, auction);
  }

  List<SavedAuction> getSavedAuctionList() {
    return _savedAuctionBox.values.toList();
  }

  List<String> getSavedAuctionKeyList() {
    return _savedAuctionBox.values.map((SavedAuction auction) => auction.hiveKey).toList();
  }

  Future<void> deleteSavedAuction(SavedAuction auction) async {
    _savedAuctionBox.delete(auction.hiveKey);
  }

  Future<void> clearAllSavedAuction() async {
    _savedAuctionBox.clear();
  }
  // saved auction box:
  // --------------------------------------------

  // --------------------------------------------
  // reminder box:
  Future<void> writeAuctionReminder(AuctionReminder reminder) async {
    await _reminderBox.put(reminder.auctionId, reminder);
  }

  List<AuctionReminder> getAuctionReminderList() {
    return _reminderBox.values.toList();
  }

  List<int> getAuctionReminderIdList() {
    return _reminderBox.values.map((AuctionReminder reminder) => reminder.auctionId).toList();
  }

  Future<void> deleteAuctionReminder(int auctionId) async {
    _reminderBox.delete(auctionId);
  }

  Future<void> clearAllAuctionReminder() async {
    _reminderBox.clear();
  }
  // reminder box
  // --------------------------------------------

  // --------------------------------------------
  // tips box:
  Future<void> writeTips(String tipsKey) async {
    await _tipsBox.put(tipsKey, true);
  }

  bool getTips(String tipsKey) {
    return _tipsBox.containsKey(tipsKey) && _tipsBox.get(tipsKey)!;
  }

  List<String> getAllTips() {
    return _tipsBox.keys.map((dynamic key) => key.toString()).toList();
  }

  Future<void> deleteTips(String tipsKey) async {
    _tipsBox.delete(tipsKey);
  }

  Future<void> clearAllTips() async {
    _tipsBox.clear();
  }
  // tips box
  // --------------------------------------------

  // --------------------------------------------
  // log box:
  Future<bool> writeLog(String val) async {
    if (!_enableLog) {
      return false;
    }

    await _logBox.put('${utilities.formatSimpleDateTime(DateTime.now())}.${DateTime.now().millisecond.toString().padLeft(3, '0')}', val);
    return true;
  }

  List<MapEntry<String, String>> getAllLog() {
    if (!_enableLog) {
      return <MapEntry<String, String>>[];
    }
    
    return _logBox.keys.toList().reversed.map((dynamic key) => MapEntry<String, String>(key as String, _logBox.get(key, defaultValue: '')!)).toList();
  }

  Future<bool> clearAllLog() async {
    if (!_enableLog) {
      return false;
    }

    await _logBox.clear();
    return true;
  }
  // log box
  // --------------------------------------------
}
