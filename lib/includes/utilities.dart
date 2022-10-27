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

String formatTimeBefore(DateTime dt, String lang) {
  final DateTime now = DateTime.now();

  if (now.difference(dt).inMinutes <= 0) {
    return 'just';
  }

  if (now.difference(dt).inHours <= 0) {
    return '${now.difference(dt).inMinutes} minutes before';
  }

  if (now.difference(dt).inDays <= 0) {
    return '${now.difference(dt).inHours} hours before';
  }

  if (now.difference(dt).inDays <= 5) {
    return '${now.difference(dt).inDays} days before';
  }

  return formatDate(dt, lang);
}

String formatDateTime(DateTime dt, String lang) {
  return '${formatDate(dt, lang)} ${formatTime(dt, lang)}';
}

String formatShortDateTime(DateTime dt, String lang) {
  return DateFormat('${config.shortDateFormat} ${config.shortTimeFormat}', lang).format(dt);
}

String formatSimpleDateTime(DateTime dt) {
  return DateFormat('yyyy-MM-dd HH:mm:ss', 'en').format(dt);
}

double adjustedScale(double scale) {
  return scale * 0.7 + 0.3;
}

double adjustedPhotoScale(double scale) {
  return scale * 0.5 + 0.5;
}
