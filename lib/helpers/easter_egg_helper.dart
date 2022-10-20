// ignore_for_file: avoid_classes_with_only_static_members
import 'package:flutter/material.dart';
import '../widgets/easter_egg.dart';

class EasterEggHelper {
  static const List<List<int>> _codes = <List<int>>[
    <int>[35613, 35486, 24515],
    <int>[35613, 35582, 28982],
  ];

  static bool check(BuildContext context, String txt) {
    for (final List<int> c in _codes) {
      if (String.fromCharCodes(c) == txt) {        
        showDialog<void>(
          context: context,
          barrierColor: Colors.transparent,
          builder: (BuildContext context) {
            return GestureDetector(
              onTap: () {
                Navigator.pop(context);
              },
              child: const EasterEgg(),
            );
          },
        );

        return true;
      }
    }
    return false;
  }
}
