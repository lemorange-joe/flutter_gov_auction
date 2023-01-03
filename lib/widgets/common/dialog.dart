import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../../helpers/hive_helper.dart';

class CommonDialog {
  const CommonDialog();

  static const double _dialogContentHeightFactor = 0.333;

  static Future<void> show(BuildContext context, String title, String content, String buttonText, Function() onButtonPressed,
      {bool isModal = false, Color barrierColor = Colors.black54}) async {
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
                  style: Theme.of(context).textTheme.bodyText2!.copyWith(
                        fontSize: 16.0,
                      ),
                ),
              ),
            ),
          ),
          actions: <Widget>[
            TextButton(
              child: Text(
                buttonText,
                style: const TextStyle(
                  color: Colors.blue,
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
      {bool isModal = false, Color barrierColor = Colors.black54}) async {
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
                  style: Theme.of(context).textTheme.bodyText2!.copyWith(
                        fontSize: 20.0,
                      ),
                ),
              ),
            ),
          ),
          actions: <Widget>[
            TextButton(
              child: Text(
                buttonText1,
                style: const TextStyle(
                  color: Colors.blue,
                  fontSize: 18.0,
                ),
              ),
              onPressed: () {
                onButtonPressed1();
              },
            ),
            TextButton(
              child: Text(
                buttonText2,
                style: const TextStyle(
                  color: Colors.blue,
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
      {bool isModal = false, Color barrierColor = Colors.black54}) async {
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
                          style: Theme.of(context).textTheme.bodyText2!.copyWith(
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
                      return Checkbox(
                        value: checked,
                        onChanged: (bool? value) {
                          if (value!) {
                            hiveHelper.writeTips(tipsKey);
                          } else {
                            hiveHelper.deleteTips(tipsKey);
                          }
                        },
                      );
                    },
                  ),
                  // TODO(joe): apply remarks style to the checkbox row
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
                          const SizedBox(height: 10.0),
                          Text(checkboxContent),
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
              child: Text(
                buttonText,
                style: const TextStyle(
                  color: Colors.blue,
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
