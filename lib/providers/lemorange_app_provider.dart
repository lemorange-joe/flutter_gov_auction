import 'dart:async';
import 'dart:convert';
import 'dart:io' show Platform;
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:logger/logger.dart';
import '../includes/config.dart' as config;

class LemorangeAppProvider extends ChangeNotifier {
  bool _fetched = false;
  bool _error = false;
  List<LemorangeApp> apps = <LemorangeApp>[];

  bool get hasError => _error;

  Future<void> getApps() async {
    if (!_fetched || _error) {
      _fetched = true;
      _error = false;

      final String url = config.lemorangeAppApiUrl.replaceAll('{0}', config.appCode);

      try {
        await http.get(Uri.parse(url)).timeout(const Duration(seconds: config.httpTimeout)).then((http.Response response) {
          if (response.statusCode == 200) {
            final dynamic jsonData = json.decode(response.body);

            if ((jsonData as Map<String, dynamic>)['status'] == 1) {
              final List<dynamic> appList = jsonData['data'] as List<dynamic>;
              apps = <LemorangeApp>[];

              for (final dynamic app in appList) {
                final Map<String, dynamic> titleLang = (app as Map<String, dynamic>)['title'] as Map<String, dynamic>;
                final Map<String, dynamic> descriptionLang = app['description'] as Map<String, dynamic>;
                apps.add(LemorangeApp(titleLang, descriptionLang, app['thumbnail'] as String, (Platform.isIOS ? app['iosUrl'] : app['androidUrl']) as String));
              }
            }
          } else {
            _fetched = true;
            _error = true;
          }

          notifyListeners();
        });
      } catch (e) {
        _fetched = true;
        _error = true;
        Logger().e(e);
        notifyListeners();
      }
    }
  }
}

class LemorangeApp {
  LemorangeApp(this.title, this.description, this.thumbnail, this.url);

  final Map<String, dynamic> title;
  final Map<String, dynamic> description;
  final String thumbnail;
  final String url;
}
