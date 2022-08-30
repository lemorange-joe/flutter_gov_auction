import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:flutter_config/flutter_config.dart';
import 'package:http/http.dart' as http;
import 'package:logger/logger.dart';
import '../include/demo_data_en.dart';
import '../include/demo_data_sc.dart';
import '../include/demo_data_tc.dart';

class ApiHelper {
  ApiHelper();

  Future<dynamic> get(
    String lang,
    String controller,
    String method, {
    Map<String, dynamic> parameters = const <String, dynamic>{},
    bool useDemoData = false,
    int demoDataDelay = 2,
  }) async {
    String strParams = '?';
    if (parameters.isNotEmpty) {
      parameters.forEach((String k, dynamic v) => strParams += '${Uri.encodeComponent(k)}=${Uri.encodeComponent(v.toString())}&');
    }
    strParams = strParams.substring(0, strParams.length - 1); // remove the last char, either '?' or &'

    final String apiUrl = FlutterConfig.get('API_URL')
        .toString()
        .replaceFirst('{lang}', lang)
        .replaceFirst('{controller}', controller)
        .replaceFirst('{method}', method)
        .replaceFirst('{params}', strParams.isEmpty ? '' : '-$strParams');

    final http.Client client = http.Client();
    dynamic returnData;

    if (useDemoData) {
      Map<String, String> demoData;
      switch (lang) {
        case 'en':
          demoData = demoDataEn;
          break;
        case 'tc':
          demoData = demoDataTc;
          break;
        case 'sc':
          demoData = demoDataSc;
          break;
        default:
          demoData = demoDataTc;
          break;
      }

      await Future<void>.delayed(Duration(seconds: demoDataDelay));
      final String demoDataKey = '$controller-$method';

      if (demoData.containsKey(demoDataKey)) {
        final dynamic jsonResult = jsonDecode(demoData[demoDataKey]!) as dynamic;

        if ((jsonResult as Map<String, dynamic>)['status'] == 'success') {
          returnData = jsonResult['data'] as dynamic;
        } else if (jsonResult['status'] == 'fail') {
          throw Exception('Demo data fail!');
        } else {
          throw Exception('Demo data error!');
        }
      } else {
        throw Exception('Demo data not found!');
      }
    } else {
      try {
        final http.Response response = await client.get(Uri.parse(apiUrl));
        if (response.statusCode == 200) {
          final dynamic jsonResult = jsonDecode(response.body) as dynamic;
          if ((jsonResult as Map<String, dynamic>)['status'] == 'success') {
            returnData = jsonResult['data'] as dynamic;
          } else if (jsonResult['status'] == 'fail') {
            throw Exception('API fail!');
          } else {
            throw Exception('API error!');
          }
        } else {
          throw HttpException('${response.statusCode}');
        }
      } catch (e) {
        Logger().e('URL: $apiUrl, ${e.toString()}');
        rethrow;
      }
    }

    return returnData;
  }
}
