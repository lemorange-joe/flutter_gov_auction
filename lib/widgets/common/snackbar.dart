import 'package:flutter/material.dart';
import '../../includes/config.dart' as config;
import '../../includes/global.dart';

class CommonSnackbar {
  const CommonSnackbar();

  static void show(BuildContext context, IconData? icon, Color iconColor, String message, {Color? textColor, int duration = config.snackbarDuration}) {
    globalScaffoldMessengerKey.currentState!.hideCurrentSnackBar();

    globalScaffoldMessengerKey.currentState!.showSnackBar(
      SnackBar(
        padding: const EdgeInsets.all(10.0),
        duration: Duration(milliseconds: duration),
        behavior: SnackBarBehavior.floating,
        margin: EdgeInsets.only(
          bottom: MediaQuery.of(context).padding.bottom + 20.0,
          right: 20.0,
          left: 20.0,
        ),
        content: Row(
          children: <Widget>[
            if (icon != null)
              Icon(
                icon,
                color: iconColor,
                size: 24.0 * MediaQuery.of(context).textScaleFactor,
              ),
            const SizedBox(width: 10.0),
            Flexible(
              child: Text(
                message,
                style: TextStyle(
                  fontSize: 18.0,
                  color: textColor ?? Theme.of(context).backgroundColor,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
