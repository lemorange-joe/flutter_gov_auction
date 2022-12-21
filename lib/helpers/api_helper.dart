import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:flutter_config/flutter_config.dart';
import 'package:http/http.dart' as http;
import 'package:logger/logger.dart';
import '../helpers/hive_helper.dart';
import '../includes/demo_data_en.dart';
import '../includes/demo_data_sc.dart';
import '../includes/demo_data_tc.dart';
import '../includes/utilities.dart' as utilities;

class ApiHelper {
  ApiHelper();

  Future<dynamic> get(
    String lang,
    String controller,
    String method, {
    List<String> urlParameters = const <String>[],
    Map<String, dynamic> parameters = const <String, dynamic>{},
    bool useDemoData = false,
    int demoDataDelay = 2,
  }) async {
    String urlParams = urlParameters.join('-');
    String strParams = '?';
    if (parameters.isNotEmpty) {
      parameters.forEach((String k, dynamic v) => strParams += '${Uri.encodeComponent(k)}=${Uri.encodeComponent(v.toString())}&');
    }
    strParams = strParams.substring(0, strParams.length - 1); // remove the last char, either '?' or &'

    String apiUrl = FlutterConfig.get('API_URL')
        .toString()
        .replaceFirst('{lang}', lang)
        .replaceFirst('{controller}', controller)
        .replaceFirst('{method}', method)
        .replaceFirst('{params}', urlParams.isEmpty ? '' : '-$urlParams');
    apiUrl += strParams.isEmpty ? '' : strParams;

    dynamic returnData;

    if (useDemoData) {
      if (method == 'relatedLots') {
        urlParams = '13-${urlParameters[1]}';
      } else if (method == 'relatedItems') {
        urlParams = '31-${urlParameters[1]}';
      }

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
      final String demoDataKey = '$controller-$method${urlParams.isEmpty ? '' : '-$urlParams'}';

      if (demoData.containsKey(demoDataKey)) {
        final dynamic jsonResult = jsonDecode(demoData[demoDataKey]!) as dynamic;

        if ((jsonResult as Map<String, dynamic>)['s'] == 'success') {
          returnData = jsonResult['d'] as dynamic;
        } else if (jsonResult['s'] == 'fail') {
          throw Exception('Demo data fail!');
        } else {
          throw Exception('Demo data error!');
        }
      } else {
        throw Exception('Demo data not found!');
      }
    } else {
      try {
        final Map<String, String> headers = <String, String>{};
        final String developerGaucId = HiveHelper().getDeveloper();
        if (developerGaucId.isNotEmpty) {
          headers['gauc-id'] = developerGaucId;
        }

        final http.Client client = http.Client();
        final http.Response response = await client.get(Uri.parse(apiUrl), headers: headers);
        if (response.statusCode == 200) {
          final dynamic jsonResult = jsonDecode(response.body) as dynamic;
          if ((jsonResult as Map<String, dynamic>)['s'] == 'success') {
            if (jsonResult['k'] != null) {
              // secret key exists, the data is encrypted
              Logger().w('key: ${jsonResult['k'].toString()}'); // TODO(joe): for debug, to be remove after tested encryption
              returnData = jsonDecode(utilities.extractApiPayload(jsonResult['d'].toString(), jsonResult['k'].toString())) as dynamic;
            } else {
              returnData = jsonResult['d'] as dynamic;
            }
          } else if (jsonResult['status'] == 'fail') {
            throw Exception('API fail!');
          } else {
            throw Exception('API error!');
          }
        } else {
          throw HttpException('${response.statusCode}');
        }
      } catch (e) {
        // Logger().e('URL: $apiUrl, ${e.toString()}');
        HiveHelper().writeLog('[API] URL: $apiUrl, ${e.toString()}');
        rethrow;
      }
    }

    return returnData;
  }

  Future<dynamic> post(
    String lang,
    String controller,
    String method, {
    Map<String, dynamic> parameters = const <String, dynamic>{},
    bool useDemoData = false,
    int demoDataDelay = 2,
  }) async {
    final Map<String, dynamic> postData = <String, dynamic>{};

    if (parameters.isNotEmpty) {
      parameters.forEach((String k, dynamic v) => postData[k] = v);
    }

    final String apiUrl = FlutterConfig.get('API_URL')
        .toString()
        .replaceFirst('{lang}', lang)
        .replaceFirst('{controller}', controller)
        .replaceFirst('{method}', method)
        .replaceFirst('{params}', '');

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
        returnData = jsonDecode(demoData[demoDataKey]!) as dynamic;
      } else {
        throw Exception('Demo data not found!');
      }
    } else {
      try {
        final Map<String, String> headers = <String, String>{};
        final String developerGaucId = HiveHelper().getDeveloper();
        if (developerGaucId.isNotEmpty) {
          headers['gauc-id'] = developerGaucId;
        }

        final http.Client client = http.Client();
        final http.Response response = await client.post(
          Uri.parse(apiUrl),
          body: postData,
          headers: headers,
        );

        if (response.statusCode == 200) {
          final dynamic jsonResult = jsonDecode(response.body) as dynamic;
          if ((jsonResult as Map<String, dynamic>)['s'] == 'success') {
            returnData = jsonResult['d'] as dynamic;
          } else if (jsonResult['s'] == 'fail') {
            throw Exception('API fail!');
          } else {
            throw Exception('API error!');
          }
        } else {
          throw HttpException('${response.statusCode}');
        }
      } catch (e) {
        // Logger().e('URL: $apiUrl, ${e.toString()}');
        HiveHelper().writeLog('[API] URL: $apiUrl, ${e.toString()}');
        rethrow;
      }
    }

    return returnData;
  }
}
