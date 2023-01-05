import 'package:flutter/material.dart';
import '../../generated/l10n.dart';
import '../../includes/config.dart' as config;

class Calendar extends StatelessWidget {
  const Calendar(this.dt, {Key? key, this.showBorder = false, this.size = defaultSize, this.color}) : super(key: key);
  
  static const double defaultSize = 36.0;

  final DateTime dt;
  final bool showBorder;
  final double size;
  final Color? color;

  @override
  Widget build(BuildContext context) {
    const Map<String, List<String>> monthMap = <String, List<String>>{
      'en': <String>['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
      'tc': <String>['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
      'sc': <String>['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
    };

    final String strMonth = monthMap[S.of(context).lang]![dt.month - 1];
    return SizedBox(
      height: size * MediaQuery.of(context).textScaleFactor,
      width: size * MediaQuery.of(context).textScaleFactor,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: <Widget>[
          Expanded(
            flex: 2,
            child: Container(
              decoration: BoxDecoration(
                color: color ?? Colors.red[900],
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(config.smBorderRadius),
                  topRight: Radius.circular(config.smBorderRadius),
                ),
              ),
              child: Center(
                child: Text(
                  strMonth,
                  style: TextStyle(
                    fontFamily: 'Montserrat',
                    color: Colors.white,
                    fontSize: 8.0 * size / defaultSize,
                  ),
                ),
              ),
            ),
          ),
          Expanded(
            flex: 3,
            child: Container(
              decoration: BoxDecoration(
                color: Theme.of(context).backgroundColor,
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(config.smBorderRadius),
                  bottomRight: Radius.circular(config.smBorderRadius),
                ),
                border: showBorder
                    ? const Border(
                        left: BorderSide(),
                        right: BorderSide(),
                        bottom: BorderSide(),
                        top: BorderSide(),
                      )
                    : null,
              ),
              child: Center(
                child: Text(
                  dt.day.toString(),
                  style: Theme.of(context).textTheme.bodyText1!.copyWith(
                        fontFamily: 'Montserrat',
                        fontSize: 13.0 * size / defaultSize,
                      ),
                ),
              ),
            ),
          )
        ],
      ),
    );
  }
}
