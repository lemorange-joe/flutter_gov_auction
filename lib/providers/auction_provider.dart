import 'dart:async';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:logger/logger.dart';
import '../class/auction.dart';
import '../helpers/api_helper.dart';
import '../helpers/hive_helper.dart';
import '../includes/enums.dart';

class AuctionProvider with ChangeNotifier {
  AuctionProvider();

  bool loaded = false;
  bool loadedDetails = false;
  late List<Auction> auctionList;
  Auction curAuction = Auction.empty();

  Future<void> refresh({String lang = 'en'}) async {
    final ApiHelper apiHelper = ApiHelper();

    try {
      final List<dynamic> result = await apiHelper.get(lang, 'auction', 'list', useDemoData: true) as List<dynamic>;
      auctionList = <Auction>[];
      for (final dynamic item in result) {
        auctionList.add(Auction.fromJson(item as Map<String, dynamic>));
      }

      loaded = true;
    } catch (e) {
      // Logger().e(e.toString());
      HiveHelper().writeLog('[Auction] ${e.toString()}');
    }

    notifyListeners();
  }

  Future<void> getAuctionDetails(int auctionId, String lang) async {
    loadedDetails = false;
    notifyListeners();

    final ApiHelper apiHelper = ApiHelper();
    try {
      final Map<String, dynamic> json =
          await apiHelper.get(lang, 'auction', 'details', urlParameters: <String>[auctionId.toString()], useDemoData: true) as Map<String, dynamic>;

      curAuction = Auction(
        json['id'] as int,
        json['n'] as String,
        DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['st'] as String),
        '${json['l'] as String} XXX',
        json['ap'] as String,
        json['rp'] as String,
        <AuctionItemPdf>[],
        json['r'] as String,
        <AuctionLot>[],
        getAuctionStatus(json['as'] as String),
        DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['lu'] as String),
      );
    } catch (e) {
      Logger().e(e.toString());
      HiveHelper().writeLog('[Auction] ${e.toString()}');
    }

    loadedDetails = true;
    notifyListeners();
  }

  Future<void> refreshLang(String lang) async {
    await refresh(lang: lang);
    if (curAuction.id > 0) {
      getAuctionDetails(curAuction.id, lang);
    }
  }
}
