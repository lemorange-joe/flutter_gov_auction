import 'package:intl/intl.dart';
import 'config.dart' as config;

String formatDate(DateTime dt, String lang) {
  return DateFormat(lang == 'en' ? config.dateFormatEn : config.dateFormatCh, lang == 'en' ? 'en' : 'zh').format(dt);
}

String formatTime(DateTime dt, String lang) {
if (lang == 'sc') {
    return DateFormat(config.timeFormatCh, 'zh').format(dt).replaceAll('時', '时');
  } else {
    return DateFormat(lang == 'en' ? config.timeFormatEn : config.timeFormatCh, lang == 'en' ? 'en' : 'zh').format(dt);
  }
}

String formatDateTime(DateTime dt, String lang) {
  return '${formatDate(dt, lang)} ${formatTime(dt, lang)}';
}
