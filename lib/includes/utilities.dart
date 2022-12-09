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
   const Map<String, Map<String, String>> langMapping = <String, Map<String, String>>{
    'en': <String, String>{
      'just': 'just added',
      'minute': ' minute before',
      'minutes': ' minutes before',
      'hour': ' hour before',
      'hours': ' hours before',
      'day': ' day before',
      'days': ' days before',
    },
    'tc': <String, String>{
      'just': '剛剛加入',
      'minute': '分鐘前',
      'minutes': '分鐘前',
      'hour': '小時前',
      'hours': '小時前',
      'day': '天前',
      'days': '天前',
    },
    'sc': <String, String>{
      'just': '刚刚加入',
      'minute': '分钟前',
      'minutes': '分钟前',
      'hour': '小时前',
      'hours': '小时前',
      'day': '天前',
      'days': '天前',
    },
  };

  Map<String, String> selectedLangMapping = langMapping['en']!;
  if (lang.toLowerCase() == 'tc') {
    selectedLangMapping = langMapping['tc']!;
  } else if (lang.toLowerCase() == 'sc') {
    selectedLangMapping = langMapping['sc']!;
  }

  if (now.difference(dt).inMinutes <= 0) {
    return selectedLangMapping['just']!;
  }

  if (now.difference(dt).inHours <= 0) {
    final int diff = now.difference(dt).inMinutes;
    return '$diff${diff > 1 ? selectedLangMapping['minutes'] : selectedLangMapping['minute']}';
  }

  if (now.difference(dt).inDays <= 0) {
    final int diff = now.difference(dt).inHours;
    return '$diff${diff > 1 ? selectedLangMapping['hours'] : selectedLangMapping['hour']}';
  }

  if (now.difference(dt).inDays <= 5) {
    final int diff = now.difference(dt).inDays;
    return '$diff${diff > 1 ? selectedLangMapping['days'] : selectedLangMapping['day']}';
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

String formatDayOfWeek(int dow, String lang) {
  const List<String> langEn = <String>['All Dates', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
  const List<String> langCh = <String>['全部日子', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日'];
  if (lang == 'en') {
    return 1 <= dow && dow <= 7 ? langEn[dow] : langEn[0];
  }

  return 1 <= dow && dow <= 7 ? langCh[dow] : langCh[0];
}

double adjustedScale(double scale) {
  return scale * 0.7 + 0.3;
}

double adjustedPhotoScale(double scale) {
  return scale * 0.5 + 0.5;
}

String formatDigits(int num) {
  return NumberFormat(',###').format(num);
}
