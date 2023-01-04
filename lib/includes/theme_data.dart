import 'package:flutter/material.dart';

ThemeData lightTheme = ThemeData(
  brightness: Brightness.light,
  backgroundColor: Colors.white,
  fontFamily: 'Noto Sans TC',
  // fontFamily: 'Times New Roman',
  textTheme: TextTheme(
    bodyText2: TextStyle(color: Colors.grey[700]),
  ),
);

ThemeData darkTheme = ThemeData(
  brightness: Brightness.dark,
  backgroundColor: Colors.grey[700],
  fontFamily: 'Noto Sans TC',
  // fontFamily: 'Times New Roman',
  primaryColor: Colors.blue,
  textTheme: const TextTheme(
    bodyText2: TextStyle(color: Colors.white70),
  ),
);
