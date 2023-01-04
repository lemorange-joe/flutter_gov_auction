import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../../helpers/hive_helper.dart';

class CommonDialog {
  const CommonDialog();

  static const double _dialogContentHeightFactor = 0.333;

  static Future<void> show(BuildContext context, String title, String content, String buttonText, Function() onButtonPressed,
      {bool isModal = false, Color barrierColor = Colors.black54, Color buttonTextColor = Colors.blue, Color? buttonBackgroundColor}) async {
    return showDialog<void>(
      context: context,
      barrierColor: barrierColor,
      barrierDismissible: !isModal,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(
            title,
            style: Theme.of(context).textTheme.bodyText1!.copyWith(
                  fontSize: 18.0,
                  fontWeight: FontWeight.bold,
                ),
          ),
          content: ConstrainedBox(
            constraints: BoxConstraints(
              maxHeight: MediaQuery.of(context).size.height * _dialogContentHeightFactor,
            ),
            child: Scrollbar(
              child: SingleChildScrollView(
                child: Text(
                  content,
                  style: Theme.of(context).textTheme.bodyText1!.copyWith(
                        fontSize: 16.0,
                      ),
                ),
              ),
            ),
          ),
          actions: <Widget>[
            TextButton(
              style: TextButton.styleFrom(backgroundColor: buttonBackgroundColor),
              child: Text(
                buttonText,
                style: TextStyle(
                  color: buttonTextColor,
                  fontSize: 18.0,
                ),
              ),
              onPressed: () {
                onButtonPressed();
              },
            ),
          ],
        );
      },
    );
  }

  static Future<void> show2(
      BuildContext context, String title, String content, String buttonText1, Function() onButtonPressed1, String buttonText2, Function() onButtonPressed2,
      {bool isModal = false,
      Color barrierColor = Colors.black54,
      Color button1TextColor = Colors.blue,
      Color button2TextColor = Colors.blue,
      Color? button1BackgroundColor,
      Color? button2BackgroundColor}) async {
    return showDialog<void>(
      context: context,
      barrierColor: barrierColor,
      barrierDismissible: !isModal,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(
            title,
            style: Theme.of(context).textTheme.bodyText1!.copyWith(
                  fontSize: 22.0,
                  fontWeight: FontWeight.bold,
                ),
          ),
          content: ConstrainedBox(
            constraints: BoxConstraints(
              maxHeight: MediaQuery.of(context).size.height * _dialogContentHeightFactor,
            ),
            child: Scrollbar(
              child: SingleChildScrollView(
                child: Text(
                  content,
                  style: Theme.of(context).textTheme.bodyText1!.copyWith(
                        fontSize: 20.0,
                      ),
                ),
              ),
            ),
          ),
          actions: <Widget>[
            TextButton(
              style: TextButton.styleFrom(backgroundColor: button1BackgroundColor),
              child: Text(
                buttonText1,
                style: TextStyle(
                  color: button1TextColor,
                  fontSize: 18.0,
                ),
              ),
              onPressed: () {
                onButtonPressed1();
              },
            ),
            TextButton(
              style: TextButton.styleFrom(backgroundColor: button2BackgroundColor),
              child: Text(
                buttonText2,
                style: TextStyle(
                  color: button2TextColor,
                  fontSize: 18.0,
                ),
              ),
              onPressed: () {
                onButtonPressed2();
              },
            ),
          ],
        );
      },
    );
  }

  static Future<void> showWithTips(
      BuildContext context, String title, String content, String tipsKey, String checkboxContent, String buttonText, Function() onButtonPressed,
      {bool isModal = false, Color barrierColor = Colors.black54, Color buttonTextColor = Colors.blue, Color? buttonBackgroundColor}) async {
    return showDialog<void>(
      context: context,
      barrierColor: barrierColor,
      barrierDismissible: !isModal,
      builder: (BuildContext context) {
        return AlertDialog(
          contentPadding: const EdgeInsets.symmetric(vertical: 24.0),
          title: Text(
            title,
            style: Theme.of(context).textTheme.bodyText1!.copyWith(
                  fontSize: 18.0,
                  fontWeight: FontWeight.bold,
                ),
          ),
          content: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: <Widget>[
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24.0),
                child: ConstrainedBox(
                  constraints: BoxConstraints(
                    maxHeight: MediaQuery.of(context).size.height * _dialogContentHeightFactor,
                  ),
                  child: Scrollbar(
                    child: SingleChildScrollView(
                      child: Expanded(
                        child: Text(
                          content,
                          style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                fontSize: 16.0,
                              ),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 12.0),
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  const SizedBox(width: 12.0),
                  ValueListenableBuilder<Box<bool>>(
                    valueListenable: Hive.box<bool>('tips').listenable(),
                    builder: (BuildContext context, _, __) {
                      final HiveHelper hiveHelper = HiveHelper();
                      final bool checked = hiveHelper.getTips(tipsKey);
                      return Transform.scale(
                        scale: MediaQuery.of(context).textScaleFactor,
                        child: Checkbox(
                          value: checked,
                          onChanged: (bool? value) {
                            if (value!) {
                              hiveHelper.writeTips(tipsKey);
                            } else {
                              hiveHelper.deleteTips(tipsKey);
                            }
                          },
                        ),
                      );
                    },
                  ),
                  Expanded(
                    child: GestureDetector(
                      onTap: () {
                        final HiveHelper hiveHelper = HiveHelper();
                        final bool checked = hiveHelper.getTips(tipsKey);
                        if (checked) {
                          hiveHelper.deleteTips(tipsKey);
                        } else {
                          hiveHelper.writeTips(tipsKey);
                        }
                      },
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: <Widget>[
                          SizedBox(height: 12.0 / MediaQuery.of(context).textScaleFactor),
                          Text(
                            checkboxContent,
                            style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 14.0),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
          actions: <Widget>[
            TextButton(
              style: TextButton.styleFrom(backgroundColor: buttonBackgroundColor),
              child: Text(
                buttonText,
                style: TextStyle(
                  color: buttonTextColor,
                  fontSize: 18.0,
                ),
              ),
              onPressed: () {
                onButtonPressed();
              },
            ),
          ],
        );
      },
    );
  }
}
