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

  /// `Accessibility Design`
  String get accessibilityDesign {
    return Intl.message(
      'Accessibility Design',
      name: 'accessibilityDesign',
      desc: '',
      args: [],
    );
  }

  /// `(TBC)This mobile app has adopted accessibility design. This mobile app has adopted accessibility design. This mobile app has adopted accessibility design.`
  String get accessibilityParagraph1 {
    return Intl.message(
      '(TBC)This mobile app has adopted accessibility design. This mobile app has adopted accessibility design. This mobile app has adopted accessibility design.',
      name: 'accessibilityParagraph1',
      desc: '',
      args: [],
    );
  }

  /// `(TBC)You can use the "TalkBack" function of Android to... You can use the "TalkBack" function of Android to... You can use the "TalkBack" function of Android to...`
  String get accessibilityParagraph2Android {
    return Intl.message(
      '(TBC)You can use the "TalkBack" function of Android to... You can use the "TalkBack" function of Android to... You can use the "TalkBack" function of Android to...',
      name: 'accessibilityParagraph2Android',
      desc: '',
      args: [],
    );
  }

  /// `(TBC)You can use the "VoiceOver" function of iOS to... You can use the "VoiceOver" function of iOS to... You can use the "VoiceOver" function of iOS to...`
  String get accessibilityParagraph2Ios {
    return Intl.message(
      '(TBC)You can use the "VoiceOver" function of iOS to... You can use the "VoiceOver" function of iOS to... You can use the "VoiceOver" function of iOS to...',
      name: 'accessibilityParagraph2Ios',
      desc: '',
      args: [],
    );
  }

  /// `.`
  String get accessibilityParagraph3After {
    return Intl.message(
      '.',
      name: 'accessibilityParagraph3After',
      desc: '',
      args: [],
    );
  }

  /// `Should you have any enquiries or comments regarding the accessibility issues, please contact us at `
  String get accessibilityParagraph3Before {
    return Intl.message(
      'Should you have any enquiries or comments regarding the accessibility issues, please contact us at ',
      name: 'accessibilityParagraph3Before',
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

  /// `Terms of Use and Disclaimer`
  String get agreement {
    return Intl.message(
      'Terms of Use and Disclaimer',
      name: 'agreement',
      desc: '',
      args: [],
    );
  }

  /// `(TBC) This is not an official app. All auction information in this app, unless otherwise stated, is acquired from the open data provided by DATA.GOV.HK and Government Logistics Department. Any differences of data, the website of the Information and Logistics Services Department shall prevail. The Government of the Hong Kong Special Administrative Region is the intellectual property owner of the data.`
  String get agreementParagraph1 {
    return Intl.message(
      '(TBC) This is not an official app. All auction information in this app, unless otherwise stated, is acquired from the open data provided by DATA.GOV.HK and Government Logistics Department. Any differences of data, the website of the Information and Logistics Services Department shall prevail. The Government of the Hong Kong Special Administrative Region is the intellectual property owner of the data.',
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

  /// `(TBC) Allow data analytics`
  String get allowAnalytics {
    return Intl.message(
      '(TBC) Allow data analytics',
      name: 'allowAnalytics',
      desc: '',
      args: [],
    );
  }

  /// `(TBC) Allow data analytics description: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elementum.`
  String get allowAnalyticsDescription {
    return Intl.message(
      '(TBC) Allow data analytics description: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elementum.',
      name: 'allowAnalyticsDescription',
      desc: '',
      args: [],
    );
  }

  /// `Auction Details`
  String get auctionDetails {
    return Intl.message(
      'Auction Details',
      name: 'auctionDetails',
      desc: '',
      args: [],
    );
  }

  /// `Auction List`
  String get auctionList {
    return Intl.message(
      'Auction List',
      name: 'auctionList',
      desc: '',
      args: [],
    );
  }

  /// `Auction Number`
  String get auctionNumber {
    return Intl.message(
      'Auction Number',
      name: 'auctionNumber',
      desc: '',
      args: [],
    );
  }

  /// `Auction Reminder`
  String get auctionReminder {
    return Intl.message(
      'Auction Reminder',
      name: 'auctionReminder',
      desc: '',
      args: [],
    );
  }

  /// `No auction reminders`
  String get auctionReminderEmpty {
    return Intl.message(
      'No auction reminders',
      name: 'auctionReminderEmpty',
      desc: '',
      args: [],
    );
  }

  /// `Auction Result`
  String get auctionResult {
    return Intl.message(
      'Auction Result',
      name: 'auctionResult',
      desc: '',
      args: [],
    );
  }

  /// `Date`
  String get auctionStartDate {
    return Intl.message(
      'Date',
      name: 'auctionStartDate',
      desc: '',
      args: [],
    );
  }

  /// `Time`
  String get auctionStartTime {
    return Intl.message(
      'Time',
      name: 'auctionStartTime',
      desc: '',
      args: [],
    );
  }

  /// `Cancel`
  String get cancel {
    return Intl.message(
      'Cancel',
      name: 'cancel',
      desc: '',
      args: [],
    );
  }

  /// `Clear reminder`
  String get clearReminder {
    return Intl.message(
      'Clear reminder',
      name: 'clearReminder',
      desc: '',
      args: [],
    );
  }

  /// `Clear search history`
  String get clearSearchHistory {
    return Intl.message(
      'Clear search history',
      name: 'clearSearchHistory',
      desc: '',
      args: [],
    );
  }

  /// `Close`
  String get close {
    return Intl.message(
      'Close',
      name: 'close',
      desc: '',
      args: [],
    );
  }

  /// `Collection Deadline`
  String get collectionDeadline {
    return Intl.message(
      'Collection Deadline',
      name: 'collectionDeadline',
      desc: '',
      args: [],
    );
  }

  /// `A Buyer shall collect and remove the Lot purchased from the Location on or before {collection_deadline} or as otherwise specified in the Special Conditions of Auction Sale.`
  String collectionDeadlineStatement(Object collection_deadline) {
    return Intl.message(
      'A Buyer shall collect and remove the Lot purchased from the Location on or before $collection_deadline or as otherwise specified in the Special Conditions of Auction Sale.',
      name: 'collectionDeadlineStatement',
      desc: '',
      args: [collection_deadline],
    );
  }

  /// `Coming Auction`
  String get comingAuction {
    return Intl.message(
      'Coming Auction',
      name: 'comingAuction',
      desc: '',
      args: [],
    );
  }

  /// `Are you sure to delete all auction reminders`
  String get confirmDeleteAllReminders {
    return Intl.message(
      'Are you sure to delete all auction reminders',
      name: 'confirmDeleteAllReminders',
      desc: '',
      args: [],
    );
  }

  /// `Are you sure to delete all saved items under comming and past auctions?`
  String get confirmDeleteAllSavedItems {
    return Intl.message(
      'Are you sure to delete all saved items under comming and past auctions?',
      name: 'confirmDeleteAllSavedItems',
      desc: '',
      args: [],
    );
  }

  /// ` day before`
  String get dayBefore {
    return Intl.message(
      ' day before',
      name: 'dayBefore',
      desc: '',
      args: [],
    );
  }

  /// ` days before`
  String get daysBefore {
    return Intl.message(
      ' days before',
      name: 'daysBefore',
      desc: '',
      args: [],
    );
  }

  /// `Delete reminders`
  String get deleteReminders {
    return Intl.message(
      'Delete reminders',
      name: 'deleteReminders',
      desc: '',
      args: [],
    );
  }

  /// `Delete saved items`
  String get deleteSavedItems {
    return Intl.message(
      'Delete saved items',
      name: 'deleteSavedItems',
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

  /// `View details`
  String get doubleTapViewDetails {
    return Intl.message(
      'View details',
      name: 'doubleTapViewDetails',
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

  /// `featured items`
  String get featuredItems {
    return Intl.message(
      'featured items',
      name: 'featuredItems',
      desc: '',
      args: [],
    );
  }

  /// `Auction Status`
  String get fieldAuctionStatus {
    return Intl.message(
      'Auction Status',
      name: 'fieldAuctionStatus',
      desc: '',
      args: [],
    );
  }

  /// `Contact Person`
  String get fieldContact {
    return Intl.message(
      'Contact Person',
      name: 'fieldContact',
      desc: '',
      args: [],
    );
  }

  /// `Location`
  String get fieldContactLocation {
    return Intl.message(
      'Location',
      name: 'fieldContactLocation',
      desc: '',
      args: [],
    );
  }

  /// `Tel`
  String get fieldContactNumber {
    return Intl.message(
      'Tel',
      name: 'fieldContactNumber',
      desc: '',
      args: [],
    );
  }

  /// `Department`
  String get fieldDeapartment {
    return Intl.message(
      'Department',
      name: 'fieldDeapartment',
      desc: '',
      args: [],
    );
  }

  /// `GLD File Ref`
  String get fieldGldFileRef {
    return Intl.message(
      'GLD File Ref',
      name: 'fieldGldFileRef',
      desc: '',
      args: [],
    );
  }

  /// `Special Inspection Arrangement`
  String get fieldInspectionArrangement {
    return Intl.message(
      'Special Inspection Arrangement',
      name: 'fieldInspectionArrangement',
      desc: '',
      args: [],
    );
  }

  /// `Item Conditions`
  String get fieldItemConditions {
    return Intl.message(
      'Item Conditions',
      name: 'fieldItemConditions',
      desc: '',
      args: [],
    );
  }

  /// `Item List`
  String get fieldItemList {
    return Intl.message(
      'Item List',
      name: 'fieldItemList',
      desc: '',
      args: [],
    );
  }

  /// `Item Type`
  String get fieldItemType {
    return Intl.message(
      'Item Type',
      name: 'fieldItemType',
      desc: '',
      args: [],
    );
  }

  /// `Lot Num`
  String get fieldLotNum {
    return Intl.message(
      'Lot Num',
      name: 'fieldLotNum',
      desc: '',
      args: [],
    );
  }

  /// `Reference`
  String get fieldReference {
    return Intl.message(
      'Reference',
      name: 'fieldReference',
      desc: '',
      args: [],
    );
  }

  /// `Remarks`
  String get fieldRemarks {
    return Intl.message(
      'Remarks',
      name: 'fieldRemarks',
      desc: '',
      args: [],
    );
  }

  /// `Please upgrade to latest version.`
  String get forceUpgradeContent {
    return Intl.message(
      'Please upgrade to latest version.',
      name: 'forceUpgradeContent',
      desc: '',
      args: [],
    );
  }

  /// `Latest app available`
  String get forceUpgradeTitle {
    return Intl.message(
      'Latest app available',
      name: 'forceUpgradeTitle',
      desc: '',
      args: [],
    );
  }

  /// `Government Logistics Department webpage`
  String get gldWebsite {
    return Intl.message(
      'Government Logistics Department webpage',
      name: 'gldWebsite',
      desc: '',
      args: [],
    );
  }

  /// `en`
  String get gldWebsiteLang {
    return Intl.message(
      'en',
      name: 'gldWebsiteLang',
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

  /// `Hot Search`
  String get hotSearch {
    return Intl.message(
      'Hot Search',
      name: 'hotSearch',
      desc: '',
      args: [],
    );
  }

  /// ` hour before`
  String get hourBefore {
    return Intl.message(
      ' hour before',
      name: 'hourBefore',
      desc: '',
      args: [],
    );
  }

  /// ` hours before`
  String get hoursBefore {
    return Intl.message(
      ' hours before',
      name: 'hoursBefore',
      desc: '',
      args: [],
    );
  }

  /// `Inspection may only be made between {start_time} hours and {end_time} hours on {day_of_week} of the week of the public auction. Should {day_of_week} be a public holiday, the inspection day shall be the first working day immediately before the public holiday between {start_time} hours and {end_time} hours.`
  String insepctionArrangementDetails1(
      Object start_time, Object end_time, Object day_of_week) {
    return Intl.message(
      'Inspection may only be made between $start_time hours and $end_time hours on $day_of_week of the week of the public auction. Should $day_of_week be a public holiday, the inspection day shall be the first working day immediately before the public holiday between $start_time hours and $end_time hours.',
      name: 'insepctionArrangementDetails1',
      desc: '',
      args: [start_time, end_time, day_of_week],
    );
  }

  /// `In case Tropical Cyclone Warning Signal No.8 or above is hoisted or Black Rainstorm Warning Signal or "extreme conditions after super typhoons" announced by the Government is/are in force for any duration between {start_time} hours and {end_time} hours on the scheduled inspection day, the inspection arrangement on that day and the auction of the relevant lots will be cancelled.`
  String insepctionArrangementDetails2(Object start_time, Object end_time) {
    return Intl.message(
      'In case Tropical Cyclone Warning Signal No.8 or above is hoisted or Black Rainstorm Warning Signal or "extreme conditions after super typhoons" announced by the Government is/are in force for any duration between $start_time hours and $end_time hours on the scheduled inspection day, the inspection arrangement on that day and the auction of the relevant lots will be cancelled.',
      name: 'insepctionArrangementDetails2',
      desc: '',
      args: [start_time, end_time],
    );
  }

  /// `Item Details`
  String get itemDetails {
    return Intl.message(
      'Item Details',
      name: 'itemDetails',
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

  /// `Location: `
  String get location {
    return Intl.message(
      'Location: ',
      name: 'location',
      desc: '',
      args: [],
    );
  }

  /// ` minutes before`
  String get minutesBefore {
    return Intl.message(
      ' minutes before',
      name: 'minutesBefore',
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

  /// `Next auction`
  String get nextAuction {
    return Intl.message(
      'Next auction',
      name: 'nextAuction',
      desc: '',
      args: [],
    );
  }

  /// `No auction items`
  String get noAuctionItem {
    return Intl.message(
      'No auction items',
      name: 'noAuctionItem',
      desc: '',
      args: [],
    );
  }

  /// `No search history`
  String get noSearchHistory {
    return Intl.message(
      'No search history',
      name: 'noSearchHistory',
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

  /// `Notes for Bidders`
  String get notesForBidders {
    return Intl.message(
      'Notes for Bidders',
      name: 'notesForBidders',
      desc: '',
      args: [],
    );
  }

  /// `Notice links:`
  String get noticeLinks {
    return Intl.message(
      'Notice links:',
      name: 'noticeLinks',
      desc: '',
      args: [],
    );
  }

  /// `Nullam vitae scelerisque est. Aenean placerat erat sed augue eleifend, vel faucibus elit auctor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Vestibulum in interdum diam.`
  String get noticeParagraph1 {
    return Intl.message(
      'Nullam vitae scelerisque est. Aenean placerat erat sed augue eleifend, vel faucibus elit auctor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Vestibulum in interdum diam.',
      name: 'noticeParagraph1',
      desc: '',
      args: [],
    );
  }

  /// `List of the goods available for sale and other information are obtainable from [gld_website] and the following places one week prior to the Auction:`
  String get noticeParagraph2 {
    return Intl.message(
      'List of the goods available for sale and other information are obtainable from [gld_website] and the following places one week prior to the Auction:',
      name: 'noticeParagraph2',
      desc: '',
      args: [],
    );
  }

  /// `hasellus quis nisi faucibus, molestie est non, tempor felis. Duis quam nibh, tristique gravida risus sit amet, malesuada condimentum risus. Vivamus id dapibus nibh. Praesent porttitor ultrices tellus, sed varius lorem viverra et. Pellentesque orci elit, malesuada ut erat ac, rutrum feugiat ex. Cras vehicula turpis magna. Nullam feugiat orci vitae urna molestie, id iaculis massa iaculis. Nulla facilisi. Vestibulum efficitur dolor nulla, at cursus dui accumsan vel. Phasellus vulputate sem non augue fermentum, eleifend egestas leo faucibus. Fusce id hendrerit nibh. Nunc tempor nunc aliquam nisl ornare euismod.`
  String get noticeParagraph3 {
    return Intl.message(
      'hasellus quis nisi faucibus, molestie est non, tempor felis. Duis quam nibh, tristique gravida risus sit amet, malesuada condimentum risus. Vivamus id dapibus nibh. Praesent porttitor ultrices tellus, sed varius lorem viverra et. Pellentesque orci elit, malesuada ut erat ac, rutrum feugiat ex. Cras vehicula turpis magna. Nullam feugiat orci vitae urna molestie, id iaculis massa iaculis. Nulla facilisi. Vestibulum efficitur dolor nulla, at cursus dui accumsan vel. Phasellus vulputate sem non augue fermentum, eleifend egestas leo faucibus. Fusce id hendrerit nibh. Nunc tempor nunc aliquam nisl ornare euismod.',
      name: 'noticeParagraph3',
      desc: '',
      args: [],
    );
  }

  /// `Nullam pharetra, nibh et vehicula eleifend, dui justo ornare odio, vitae varius turpis diam at urna. Ut at dolor vulputate, molestie lectus nec, aliquam sapien. In vestibulum tristique augue non placerat. Pellentesque a libero magna. Cras sed tortor in leo commodo maximus id sagittis nulla.`
  String get noticeParagraph4 {
    return Intl.message(
      'Nullam pharetra, nibh et vehicula eleifend, dui justo ornare odio, vitae varius turpis diam at urna. Ut at dolor vulputate, molestie lectus nec, aliquam sapien. In vestibulum tristique augue non placerat. Pellentesque a libero magna. Cras sed tortor in leo commodo maximus id sagittis nulla.',
      name: 'noticeParagraph4',
      desc: '',
      args: [],
    );
  }

  /// `Maecenas metus sapien, pretium at libero ac, porttitor tincidunt elit. Vestibulum elementum eros et enim pharetra gravida. Donec pulvinar nisi massa, sit amet feugiat dui egestas dictum.`
  String get noticeParagraph5 {
    return Intl.message(
      'Maecenas metus sapien, pretium at libero ac, porttitor tincidunt elit. Vestibulum elementum eros et enim pharetra gravida. Donec pulvinar nisi massa, sit amet feugiat dui egestas dictum.',
      name: 'noticeParagraph5',
      desc: '',
      args: [],
    );
  }

  /// `Notice to Participants`
  String get noticeToParticipants {
    return Intl.message(
      'Notice to Participants',
      name: 'noticeToParticipants',
      desc: '',
      args: [],
    );
  }

  /// `Should you have any questions about participating in the auction, please refer to `
  String get viewNoticeToParticipants1 {
    return Intl.message(
      'Should you have any questions about participating in the auction, please refer to ',
      name: 'viewNoticeToParticipants1',
      desc: '',
      args: [],
    );
  }

  /// `.`
  String get viewNoticeToParticipants2 {
    return Intl.message(
      '.',
      name: 'viewNoticeToParticipants2',
      desc: '',
      args: [],
    );
  }

  /// `Not Sold`
  String get notSold {
    return Intl.message(
      'Not Sold',
      name: 'notSold',
      desc: '',
      args: [],
    );
  }

  /// `OK`
  String get ok {
    return Intl.message(
      'OK',
      name: 'ok',
      desc: '',
      args: [],
    );
  }

  /// `Past Auction`
  String get pastAuction {
    return Intl.message(
      'Past Auction',
      name: 'pastAuction',
      desc: '',
      args: [],
    );
  }

  /// `Past Auction Lists`
  String get pastAuctionList {
    return Intl.message(
      'Past Auction Lists',
      name: 'pastAuctionList',
      desc: '',
      args: [],
    );
  }

  /// `Photo Copyright`
  String get photoCopyright {
    return Intl.message(
      'Photo Copyright',
      name: 'photoCopyright',
      desc: '',
      args: [],
    );
  }

  /// `not the actual product, the photo is for reference only`
  String get photoDisclaimer {
    return Intl.message(
      'not the actual product, the photo is for reference only',
      name: 'photoDisclaimer',
      desc: '',
      args: [],
    );
  }

  /// `Last auction`
  String get previousAuction {
    return Intl.message(
      'Last auction',
      name: 'previousAuction',
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

  /// `In nec sem nec ex dictum facilisis sit amet ac enim. Nunc commodo aliquam blandit. Proin quis ornare ipsum. Phasellus condimentum pulvinar dolor vel tristique. Aenean congue consectetur lorem non vehicula.`
  String get privacyPolicyParagraph4 {
    return Intl.message(
      'In nec sem nec ex dictum facilisis sit amet ac enim. Nunc commodo aliquam blandit. Proin quis ornare ipsum. Phasellus condimentum pulvinar dolor vel tristique. Aenean congue consectetur lorem non vehicula.',
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

  /// `read`
  String get read {
    return Intl.message(
      'read',
      name: 'read',
      desc: '',
      args: [],
    );
  }

  /// `Receive notification`
  String get receiveNotification {
    return Intl.message(
      'Receive notification',
      name: 'receiveNotification',
      desc: '',
      args: [],
    );
  }

  /// `Recently Sold`
  String get recentlySold {
    return Intl.message(
      'Recently Sold',
      name: 'recentlySold',
      desc: '',
      args: [],
    );
  }

  /// `Recent Search`
  String get recentSearch {
    return Intl.message(
      'Recent Search',
      name: 'recentSearch',
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

  /// `No more related items`
  String get relatedLotsEmpty {
    return Intl.message(
      'No more related items',
      name: 'relatedLotsEmpty',
      desc: '',
      args: [],
    );
  }

  /// `Related items of this auction`
  String get relatedLotsOfThisAuction {
    return Intl.message(
      'Related items of this auction',
      name: 'relatedLotsOfThisAuction',
      desc: '',
      args: [],
    );
  }

  /// `Related items: `
  String get relatedLotsPrefix {
    return Intl.message(
      'Related items: ',
      name: 'relatedLotsPrefix',
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

  /// `No saved items`
  String get savedAuctionEmpty {
    return Intl.message(
      'No saved items',
      name: 'savedAuctionEmpty',
      desc: '',
      args: [],
    );
  }

  /// `(TBC)Search items`
  String get searchAuction {
    return Intl.message(
      '(TBC)Search items',
      name: 'searchAuction',
      desc: '',
      args: [],
    );
  }

  /// `"`
  String get searchGridAfter {
    return Intl.message(
      '"',
      name: 'searchGridAfter',
      desc: '',
      args: [],
    );
  }

  /// `Items related to "`
  String get searchGridBefore {
    return Intl.message(
      'Items related to "',
      name: 'searchGridBefore',
      desc: '',
      args: [],
    );
  }

  /// `Search History`
  String get searchHistory {
    return Intl.message(
      'Search History',
      name: 'searchHistory',
      desc: '',
      args: [],
    );
  }

  /// `add to saved items`
  String get semanticsAddToSavedItems {
    return Intl.message(
      'add to saved items',
      name: 'semanticsAddToSavedItems',
      desc: '',
      args: [],
    );
  }

  /// `close`
  String get semanticsClose {
    return Intl.message(
      'close',
      name: 'semanticsClose',
      desc: '',
      args: [],
    );
  }

  /// `delete all reminders`
  String get semanticsDeleteAllReminders {
    return Intl.message(
      'delete all reminders',
      name: 'semanticsDeleteAllReminders',
      desc: '',
      args: [],
    );
  }

  /// `delete all saved items`
  String get semanticsDeleteAllSaved {
    return Intl.message(
      'delete all saved items',
      name: 'semanticsDeleteAllSaved',
      desc: '',
      args: [],
    );
  }

  /// `press and hold to view details`
  String get semanticsDoubleTapViewDetails {
    return Intl.message(
      'press and hold to view details',
      name: 'semanticsDoubleTapViewDetails',
      desc: '',
      args: [],
    );
  }

  /// `email to `
  String get semanticsEmailTo {
    return Intl.message(
      'email to ',
      name: 'semanticsEmailTo',
      desc: '',
      args: [],
    );
  }

  /// `featured items`
  String get semanticsFeaturedItems {
    return Intl.message(
      'featured items',
      name: 'semanticsFeaturedItems',
      desc: '',
      args: [],
    );
  }

  /// `go back`
  String get semanticsGoBack {
    return Intl.message(
      'go back',
      name: 'semanticsGoBack',
      desc: '',
      args: [],
    );
  }

  /// `more information`
  String get semanticsMoreInfo {
    return Intl.message(
      'more information',
      name: 'semanticsMoreInfo',
      desc: '',
      args: [],
    );
  }

  /// `open`
  String get semanticsOpen {
    return Intl.message(
      'open',
      name: 'semanticsOpen',
      desc: '',
      args: [],
    );
  }

  /// `, click to open in map `
  String get semanticsOpenInMap {
    return Intl.message(
      ', click to open in map ',
      name: 'semanticsOpenInMap',
      desc: '',
      args: [],
    );
  }

  /// `read news`
  String get semanticsOpenNews {
    return Intl.message(
      'read news',
      name: 'semanticsOpenNews',
      desc: '',
      args: [],
    );
  }

  /// `red`
  String get semanticsRead {
    return Intl.message(
      'red',
      name: 'semanticsRead',
      desc: '',
      args: [],
    );
  }

  /// `saved items`
  String get semanticsSavedItems {
    return Intl.message(
      'saved items',
      name: 'semanticsSavedItems',
      desc: '',
      args: [],
    );
  }

  /// `semantics TBC`
  String get semanticsTbc {
    return Intl.message(
      'semantics TBC',
      name: 'semanticsTbc',
      desc: '',
      args: [],
    );
  }

  /// `unread`
  String get semanticsUnread {
    return Intl.message(
      'unread',
      name: 'semanticsUnread',
      desc: '',
      args: [],
    );
  }

  /// `Font Size`
  String get settingFontSize {
    return Intl.message(
      'Font Size',
      name: 'settingFontSize',
      desc: '',
      args: [],
    );
  }

  /// `Large`
  String get settingFontSizeLarge {
    return Intl.message(
      'Large',
      name: 'settingFontSizeLarge',
      desc: '',
      args: [],
    );
  }

  /// `Medium`
  String get settingFontSizeMid {
    return Intl.message(
      'Medium',
      name: 'settingFontSizeMid',
      desc: '',
      args: [],
    );
  }

  /// `Small`
  String get settingFontSizeSmall {
    return Intl.message(
      'Small',
      name: 'settingFontSizeSmall',
      desc: '',
      args: [],
    );
  }

  /// `Language`
  String get settingLanguage {
    return Intl.message(
      'Language',
      name: 'settingLanguage',
      desc: '',
      args: [],
    );
  }

  /// `Receive notification`
  String get settingReceiveNotification {
    return Intl.message(
      'Receive notification',
      name: 'settingReceiveNotification',
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

  /// `Theme`
  String get settingTheme {
    return Intl.message(
      'Theme',
      name: 'settingTheme',
      desc: '',
      args: [],
    );
  }

  /// `Dark`
  String get settingThemeDark {
    return Intl.message(
      'Dark',
      name: 'settingThemeDark',
      desc: '',
      args: [],
    );
  }

  /// `Light`
  String get settingThemeLight {
    return Intl.message(
      'Light',
      name: 'settingThemeLight',
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

  /// `Sold`
  String get sold {
    return Intl.message(
      'Sold',
      name: 'sold',
      desc: '',
      args: [],
    );
  }

  /// `Item(s)`
  String get splitContactLocation {
    return Intl.message(
      'Item(s)',
      name: 'splitContactLocation',
      desc: '',
      args: [],
    );
  }

  /// `Item`
  String get splitContactPerson {
    return Intl.message(
      'Item',
      name: 'splitContactPerson',
      desc: '',
      args: [],
    );
  }

  /// `All`
  String get tabAll {
    return Intl.message(
      'All',
      name: 'tabAll',
      desc: '',
      args: [],
    );
  }

  /// `Confiscated Goods`
  String get tabItemTypeC {
    return Intl.message(
      'Confiscated Goods',
      name: 'tabItemTypeC',
      desc: '',
      args: [],
    );
  }

  /// `Unserviceable Stores`
  String get tabItemTypeM {
    return Intl.message(
      'Unserviceable Stores',
      name: 'tabItemTypeM',
      desc: '',
      args: [],
    );
  }

  /// `Surplus Serviceable Stores`
  String get tabItemTypeMS {
    return Intl.message(
      'Surplus Serviceable Stores',
      name: 'tabItemTypeMS',
      desc: '',
      args: [],
    );
  }

  /// `Unclaimed Properties`
  String get tabItemTypeUP {
    return Intl.message(
      'Unclaimed Properties',
      name: 'tabItemTypeUP',
      desc: '',
      args: [],
    );
  }

  /// `Time: `
  String get time {
    return Intl.message(
      'Time: ',
      name: 'time',
      desc: '',
      args: [],
    );
  }

  /// `{lot_count} auction lots`
  String tooltipLotCount(Object lot_count) {
    return Intl.message(
      '$lot_count auction lots',
      name: 'tooltipLotCount',
      desc: '',
      args: [lot_count],
    );
  }

  /// `Total auction amount: ${total}`
  String tooltipTotalTrasaction(Object total) {
    return Intl.message(
      'Total auction amount: \$$total',
      name: 'tooltipTotalTrasaction',
      desc: '',
      args: [total],
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

  /// `View all`
  String get viewAll {
    return Intl.message(
      'View all',
      name: 'viewAll',
      desc: '',
      args: [],
    );
  }

  /// `View Details`
  String get viewAuctionDetails {
    return Intl.message(
      'View Details',
      name: 'viewAuctionDetails',
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
