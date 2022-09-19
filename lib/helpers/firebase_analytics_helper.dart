import 'package:firebase_analytics/firebase_analytics.dart';
// import 'package:logger/logger.dart';

class FirebaseAnalyticsHelper {
  factory FirebaseAnalyticsHelper() {
    return _firebaseAnalyticsHelper;
  }

  FirebaseAnalyticsHelper._internal();

  static late FirebaseAnalytics _analytics;
  static final FirebaseAnalyticsHelper _firebaseAnalyticsHelper = FirebaseAnalyticsHelper._internal();
  static bool enabled = false;

  void init(bool allowAnalytics) {
    _analytics = FirebaseAnalytics.instance;
    enabled = allowAnalytics;
  }

  FirebaseAnalytics get analytics {
    return _analytics;
  }

  Map<String, dynamic> _prefixParameters(Map<String, dynamic>? parameters) {
    //prefix the the parameters for easy view in analytics report
    final Map<String, dynamic> output = <String, dynamic>{};
    if (parameters != null) {
      parameters.forEach((String k, dynamic v) => output['app_$k'] = v);
    }
    return output;
  }

  Future<void> logAppOpen() async {
    if (!enabled) {
      return;
    }
    
    await _analytics.logAppOpen();
  }

  Future<void> logSearch(String keyword) async {
    if (!enabled) {
      return;
    }

    await _analytics.logSearch(searchTerm: keyword);
  }

  Future<void> logEvent(String event, {Map<String, dynamic>? parameters}) async {
    if (!enabled) {
      return;
    }

    await _analytics.logEvent(name: event, parameters: _prefixParameters(parameters));
  }

  Future<void> logPageView(String page) async {
    if (!enabled) {
      return;
    }
    
    await _analytics.logScreenView(screenClass: 'page', screenName: page);
  }

  Future<void> logTabView(String tab) async {
    if (!enabled) {
      return;
    }

    await _analytics.logScreenView(screenClass: 'tab', screenName: tab);
  }
}
