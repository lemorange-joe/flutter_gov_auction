import 'package:flutter/material.dart';

class CommonDialog {
  const CommonDialog();

  static Future<void> show(BuildContext context, String title, String content, String buttonText, Function() onButtonPressed, {bool isModal = false, Color barrierColor = Colors.black54}) async {
    return showDialog<void>(
      context: context,
      barrierColor: Colors.black54,
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
          content: Scrollbar(
            child: SingleChildScrollView(
              child: Text(
                content,
                style: Theme.of(context).textTheme.bodyText2!.copyWith(
                      fontSize: 20.0,
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

  static Future<void> show2(BuildContext context, String title, String content, String buttonText1, Function() onButtonPressed1, String buttonText2, Function() onButtonPressed2, {bool isModal = false, Color barrierColor = Colors.black54}) async {
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
          content: Scrollbar(
            child: SingleChildScrollView(
              child: Text(
                content,
                style: Theme.of(context).textTheme.bodyText2!.copyWith(
                      fontSize: 20.0,
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
}
