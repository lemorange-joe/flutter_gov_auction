// GENERATED CODE - DO NOT MODIFY BY HAND
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'intl/messages_all.dart';

// **************************************************************************
// Generator: Flutter Intl IDE plugin
// Made by Localizely
// **************************************************************************

// ignore_for_file: non_constant_identifier_names, lines_longer_than_80_chars
// ignore_for_file: join_return_with_assignment, prefer_final_in_for_each
// ignore_for_file: avoid_redundant_argument_values, avoid_escaping_inner_quotes

class S {
  S();

  static S? _current;

  static S get current {
    assert(_current != null,
        'No instance of S was loaded. Try to initialize the S delegate before accessing S.current.');
    return _current!;
  }

  static const AppLocalizationDelegate delegate = AppLocalizationDelegate();

  static Future<S> load(Locale locale) {
    final name = (locale.countryCode?.isEmpty ?? false)
        ? locale.languageCode
        : locale.toString();
    final localeName = Intl.canonicalizedLocale(name);
    return initializeMessages(localeName).then((_) {
      Intl.defaultLocale = localeName;
      final instance = S();
      S._current = instance;

      return instance;
    });
  }

  static S of(BuildContext context) {
    final instance = S.maybeOf(context);
    assert(instance != null,
        'No instance of S present in the widget tree. Did you add S.delegate in localizationsDelegates?');
    return instance!;
  }

  static S? maybeOf(BuildContext context) {
    return Localizations.of<S>(context, S);
  }

  /// `APP Name`
  String get appName {
    return Intl.message(
      'APP Name',
      name: 'appName',
      desc: '',
      args: [],
    );
  }

  /// `Done`
  String get done {
    return Intl.message(
      'Done',
      name: 'done',
      desc: '',
      args: [],
    );
  }

  /// `en`
  String get lang {
    return Intl.message(
      'en',
      name: 'lang',
      desc: '',
      args: [],
    );
  }

  /// `Home`
  String get home {
    return Intl.message(
      'Home',
      name: 'home',
      desc: '',
      args: [],
    );
  }

  /// `My Favourite`
  String get myFavourite {
    return Intl.message(
      'My Favourite',
      name: 'myFavourite',
      desc: '',
      args: [],
    );
  }

  /// `News`
  String get news {
    return Intl.message(
      'News',
      name: 'news',
      desc: '',
      args: [],
    );
  }

  /// `There is no news.`
  String get newsEmpty {
    return Intl.message(
      'There is no news.',
      name: 'newsEmpty',
      desc: '',
      args: [],
    );
  }

  /// `Next`
  String get next {
    return Intl.message(
      'Next',
      name: 'next',
      desc: '',
      args: [],
    );
  }

  /// `Close`
  String get semanticsClose {
    return Intl.message(
      'Close',
      name: 'semanticsClose',
      desc: '',
      args: [],
    );
  }

  /// `Read news`
  String get semanticsOpenNews {
    return Intl.message(
      'Read news',
      name: 'semanticsOpenNews',
      desc: '',
      args: [],
    );
  }

  /// `Red`
  String get semanticsRead {
    return Intl.message(
      'Red',
      name: 'semanticsRead',
      desc: '',
      args: [],
    );
  }

  /// `Semantics TBC`
  String get semanticsTbc {
    return Intl.message(
      'Semantics TBC',
      name: 'semanticsTbc',
      desc: '',
      args: [],
    );
  }

  /// `Unread`
  String get semanticsUnread {
    return Intl.message(
      'Unread',
      name: 'semanticsUnread',
      desc: '',
      args: [],
    );
  }

  /// `Settings`
  String get settings {
    return Intl.message(
      'Settings',
      name: 'settings',
      desc: '',
      args: [],
    );
  }

  /// `Skip`
  String get skip {
    return Intl.message(
      'Skip',
      name: 'skip',
      desc: '',
      args: [],
    );
  }

  /// `Lorem ipsum`
  String get tourTitle1 {
    return Intl.message(
      'Lorem ipsum',
      name: 'tourTitle1',
      desc: '',
      args: [],
    );
  }

  /// `Aliquam dictum`
  String get tourTitle2 {
    return Intl.message(
      'Aliquam dictum',
      name: 'tourTitle2',
      desc: '',
      args: [],
    );
  }

  /// `Pellentesque vestibulum risus`
  String get tourTitle3 {
    return Intl.message(
      'Pellentesque vestibulum risus',
      name: 'tourTitle3',
      desc: '',
      args: [],
    );
  }

  /// `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque id ex a massa porttitor pharetra vitae fringilla quam.`
  String get tourContent1 {
    return Intl.message(
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque id ex a massa porttitor pharetra vitae fringilla quam.',
      name: 'tourContent1',
      desc: '',
      args: [],
    );
  }

  /// `Massa nec rhoncus ultrices, urna sem vehicula justo.`
  String get tourContent2 {
    return Intl.message(
      'Massa nec rhoncus ultrices, urna sem vehicula justo.',
      name: 'tourContent2',
      desc: '',
      args: [],
    );
  }

  /// `Pellentesque vestibulum risus sed justo pulvinar, vitae luctus nunc consequat.`
  String get tourContent3 {
    return Intl.message(
      'Pellentesque vestibulum risus sed justo pulvinar, vitae luctus nunc consequat.',
      name: 'tourContent3',
      desc: '',
      args: [],
    );
  }

  /// `(last item)`
  String get zzzzzz {
    return Intl.message(
      '(last item)',
      name: 'zzzzzz',
      desc: '',
      args: [],
    );
  }
}

class AppLocalizationDelegate extends LocalizationsDelegate<S> {
  const AppLocalizationDelegate();

  List<Locale> get supportedLocales {
    return const <Locale>[
      Locale.fromSubtags(languageCode: 'en'),
      Locale.fromSubtags(languageCode: 'zh', countryCode: 'CN'),
      Locale.fromSubtags(languageCode: 'zh', countryCode: 'HK'),
    ];
  }

  @override
  bool isSupported(Locale locale) => _isSupported(locale);
  @override
  Future<S> load(Locale locale) => S.load(locale);
  @override
  bool shouldReload(AppLocalizationDelegate old) => false;

  bool _isSupported(Locale locale) {
    for (var supportedLocale in supportedLocales) {
      if (supportedLocale.languageCode == locale.languageCode) {
        return true;
      }
    }
    return false;
  }
}
