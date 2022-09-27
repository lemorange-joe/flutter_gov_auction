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

  /// `en`
  String get lang {
    return Intl.message(
      'en',
      name: 'lang',
      desc: '',
      args: [],
    );
  }

  /// `Agree`
  String get agree {
    return Intl.message(
      'Agree',
      name: 'agree',
      desc: '',
      args: [],
    );
  }

  /// `Agreement`
  String get agreement {
    return Intl.message(
      'Agreement',
      name: 'agreement',
      desc: '',
      args: [],
    );
  }

  /// `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elementum, lectus ut congue consectetur, odio quam pharetra mi, ac bibendum massa mi nec est. Quisque quis tristique odio. Ut imperdiet libero arcu, vel tempor eros laoreet ut. Sed vehicula urna nec laoreet tristique. Integer ac tellus at nisl dapibus commodo. Maecenas accumsan ut neque in luctus. Donec a luctus ipsum, quis tincidunt arcu. Sed blandit vulputate arcu. Pellentesque non urna erat.`
  String get agreementParagraph1 {
    return Intl.message(
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elementum, lectus ut congue consectetur, odio quam pharetra mi, ac bibendum massa mi nec est. Quisque quis tristique odio. Ut imperdiet libero arcu, vel tempor eros laoreet ut. Sed vehicula urna nec laoreet tristique. Integer ac tellus at nisl dapibus commodo. Maecenas accumsan ut neque in luctus. Donec a luctus ipsum, quis tincidunt arcu. Sed blandit vulputate arcu. Pellentesque non urna erat.',
      name: 'agreementParagraph1',
      desc: '',
      args: [],
    );
  }

  /// `Nullam vitae scelerisque est. Aenean placerat erat sed augue eleifend, vel faucibus elit auctor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Vestibulum in interdum diam.`
  String get agreementParagraph2 {
    return Intl.message(
      'Nullam vitae scelerisque est. Aenean placerat erat sed augue eleifend, vel faucibus elit auctor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Vestibulum in interdum diam.',
      name: 'agreementParagraph2',
      desc: '',
      args: [],
    );
  }

  /// `hasellus quis nisi faucibus, molestie est non, tempor felis. Duis quam nibh, tristique gravida risus sit amet, malesuada condimentum risus. Vivamus id dapibus nibh. Praesent porttitor ultrices tellus, sed varius lorem viverra et. Pellentesque orci elit, malesuada ut erat ac, rutrum feugiat ex. Cras vehicula turpis magna. Nullam feugiat orci vitae urna molestie, id iaculis massa iaculis. Nulla facilisi. Vestibulum efficitur dolor nulla, at cursus dui accumsan vel. Phasellus vulputate sem non augue fermentum, eleifend egestas leo faucibus. Fusce id hendrerit nibh. Nunc tempor nunc aliquam nisl ornare euismod.`
  String get agreementParagraph3 {
    return Intl.message(
      'hasellus quis nisi faucibus, molestie est non, tempor felis. Duis quam nibh, tristique gravida risus sit amet, malesuada condimentum risus. Vivamus id dapibus nibh. Praesent porttitor ultrices tellus, sed varius lorem viverra et. Pellentesque orci elit, malesuada ut erat ac, rutrum feugiat ex. Cras vehicula turpis magna. Nullam feugiat orci vitae urna molestie, id iaculis massa iaculis. Nulla facilisi. Vestibulum efficitur dolor nulla, at cursus dui accumsan vel. Phasellus vulputate sem non augue fermentum, eleifend egestas leo faucibus. Fusce id hendrerit nibh. Nunc tempor nunc aliquam nisl ornare euismod.',
      name: 'agreementParagraph3',
      desc: '',
      args: [],
    );
  }

  /// `Nullam pharetra, nibh et vehicula eleifend, dui justo ornare odio, vitae varius turpis diam at urna. Ut at dolor vulputate, molestie lectus nec, aliquam sapien. In vestibulum tristique augue non placerat. Pellentesque a libero magna. Cras sed tortor in leo commodo maximus id sagittis nulla.`
  String get agreementParagraph4 {
    return Intl.message(
      'Nullam pharetra, nibh et vehicula eleifend, dui justo ornare odio, vitae varius turpis diam at urna. Ut at dolor vulputate, molestie lectus nec, aliquam sapien. In vestibulum tristique augue non placerat. Pellentesque a libero magna. Cras sed tortor in leo commodo maximus id sagittis nulla.',
      name: 'agreementParagraph4',
      desc: '',
      args: [],
    );
  }

  /// `Maecenas metus sapien, pretium at libero ac, porttitor tincidunt elit. Vestibulum elementum eros et enim pharetra gravida. Donec pulvinar nisi massa, sit amet feugiat dui egestas dictum.`
  String get agreementParagraph5 {
    return Intl.message(
      'Maecenas metus sapien, pretium at libero ac, porttitor tincidunt elit. Vestibulum elementum eros et enim pharetra gravida. Donec pulvinar nisi massa, sit amet feugiat dui egestas dictum.',
      name: 'agreementParagraph5',
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

  /// `Download Now`
  String get downloadNow {
    return Intl.message(
      'Download Now',
      name: 'downloadNow',
      desc: '',
      args: [],
    );
  }

  /// `FAQ`
  String get faq {
    return Intl.message(
      'FAQ',
      name: 'faq',
      desc: '',
      args: [],
    );
  }

  /// `Help`
  String get help {
    return Intl.message(
      'Help',
      name: 'help',
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

  /// `Loading...`
  String get loading {
    return Intl.message(
      'Loading...',
      name: 'loading',
      desc: '',
      args: [],
    );
  }

  /// `My Items`
  String get myItems {
    return Intl.message(
      'My Items',
      name: 'myItems',
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

  /// `Network Error`
  String get networkError {
    return Intl.message(
      'Network Error',
      name: 'networkError',
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

  /// `Decline`
  String get notAgree {
    return Intl.message(
      'Decline',
      name: 'notAgree',
      desc: '',
      args: [],
    );
  }

  /// `Notice to User`
  String get noticeToUser {
    return Intl.message(
      'Notice to User',
      name: 'noticeToUser',
      desc: '',
      args: [],
    );
  }

  /// `Privacy Policy`
  String get privacyPolicy {
    return Intl.message(
      'Privacy Policy',
      name: 'privacyPolicy',
      desc: '',
      args: [],
    );
  }

  /// `Proin ex metus, sodales eu cursus in, iaculis sit amet diam. Donec posuere pharetra erat et congue. Phasellus cursus rhoncus mi at luctus. Praesent et augue vitae dolor semper luctus. Pellentesque vitae neque cursus, posuere velit vestibulum, blandit nisi. Vestibulum vel erat vehicula, ullamcorper ipsum porta, lobortis est. Nam hendrerit tristique augue vel accumsan. Curabitur vitae iaculis purus, at euismod risus.`
  String get privacyPolicyParagraph1 {
    return Intl.message(
      'Proin ex metus, sodales eu cursus in, iaculis sit amet diam. Donec posuere pharetra erat et congue. Phasellus cursus rhoncus mi at luctus. Praesent et augue vitae dolor semper luctus. Pellentesque vitae neque cursus, posuere velit vestibulum, blandit nisi. Vestibulum vel erat vehicula, ullamcorper ipsum porta, lobortis est. Nam hendrerit tristique augue vel accumsan. Curabitur vitae iaculis purus, at euismod risus.',
      name: 'privacyPolicyParagraph1',
      desc: '',
      args: [],
    );
  }

  /// `Proin purus neque, pharetra eu congue eu, vulputate a turpis. Aenean interdum at orci vitae consequat. In sed iaculis lectus. Aenean aliquam placerat turpis vel tincidunt. Phasellus venenatis sagittis risus, id varius enim faucibus sed. Proin augue ligula, varius id arcu et, fermentum aliquet felis.`
  String get privacyPolicyParagraph2 {
    return Intl.message(
      'Proin purus neque, pharetra eu congue eu, vulputate a turpis. Aenean interdum at orci vitae consequat. In sed iaculis lectus. Aenean aliquam placerat turpis vel tincidunt. Phasellus venenatis sagittis risus, id varius enim faucibus sed. Proin augue ligula, varius id arcu et, fermentum aliquet felis.',
      name: 'privacyPolicyParagraph2',
      desc: '',
      args: [],
    );
  }

  /// `Morbi maximus nibh blandit augue posuere porttitor. Proin malesuada hendrerit ex et iaculis. Fusce eget euismod turpis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec mollis volutpat enim eu malesuada. Donec sit amet odio nisl. Suspendisse sit amet lorem faucibus, pretium quam nec, commodo mauris. Quisque placerat lorem in odio pellentesque auctor. Mauris at elementum felis. Nulla facilisi.`
  String get privacyPolicyParagraph3 {
    return Intl.message(
      'Morbi maximus nibh blandit augue posuere porttitor. Proin malesuada hendrerit ex et iaculis. Fusce eget euismod turpis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec mollis volutpat enim eu malesuada. Donec sit amet odio nisl. Suspendisse sit amet lorem faucibus, pretium quam nec, commodo mauris. Quisque placerat lorem in odio pellentesque auctor. Mauris at elementum felis. Nulla facilisi.',
      name: 'privacyPolicyParagraph3',
      desc: '',
      args: [],
    );
  }

  /// ` In nec sem nec ex dictum facilisis sit amet ac enim. Nunc commodo aliquam blandit. Proin quis ornare ipsum. Phasellus condimentum pulvinar dolor vel tristique. Aenean congue consectetur lorem non vehicula.`
  String get privacyPolicyParagraph4 {
    return Intl.message(
      ' In nec sem nec ex dictum facilisis sit amet ac enim. Nunc commodo aliquam blandit. Proin quis ornare ipsum. Phasellus condimentum pulvinar dolor vel tristique. Aenean congue consectetur lorem non vehicula.',
      name: 'privacyPolicyParagraph4',
      desc: '',
      args: [],
    );
  }

  /// `Nullam fringilla suscipit tellus, nec dapibus eros porttitor at. Quisque rutrum cursus ipsum nec dignissim. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Mauris eros lorem, vulputate sit amet turpis sed, condimentum volutpat leo. Aenean sed libero id risus volutpat rutrum eget sit amet est. Sed non neque mollis, lacinia lacus id, scelerisque elit. Aliquam lacus diam, pellentesque eget diam vitae, cursus pulvinar justo.`
  String get privacyPolicyParagraph5 {
    return Intl.message(
      'Nullam fringilla suscipit tellus, nec dapibus eros porttitor at. Quisque rutrum cursus ipsum nec dignissim. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Mauris eros lorem, vulputate sit amet turpis sed, condimentum volutpat leo. Aenean sed libero id risus volutpat rutrum eget sit amet est. Sed non neque mollis, lacinia lacus id, scelerisque elit. Aliquam lacus diam, pellentesque eget diam vitae, cursus pulvinar justo.',
      name: 'privacyPolicyParagraph5',
      desc: '',
      args: [],
    );
  }

  /// `Related Apps`
  String get relatedApps {
    return Intl.message(
      'Related Apps',
      name: 'relatedApps',
      desc: '',
      args: [],
    );
  }

  /// `Reload`
  String get reload {
    return Intl.message(
      'Reload',
      name: 'reload',
      desc: '',
      args: [],
    );
  }

  /// `Reminder`
  String get reminder {
    return Intl.message(
      'Reminder',
      name: 'reminder',
      desc: '',
      args: [],
    );
  }

  /// `Saved`
  String get saved {
    return Intl.message(
      'Saved',
      name: 'saved',
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

  /// `Go back`
  String get semanticsGoBack {
    return Intl.message(
      'Go back',
      name: 'semanticsGoBack',
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

  /// `Tour`
  String get tour {
    return Intl.message(
      'Tour',
      name: 'tour',
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
