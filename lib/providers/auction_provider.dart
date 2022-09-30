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
  Auction latestAuction = Auction.empty();

  Future<void> refresh({String lang = 'en'}) async {
    loaded = false;
    latestAuction = Auction.empty();
    notifyListeners();

    final ApiHelper apiHelper = ApiHelper();

    try {
      final List<dynamic> result = await apiHelper.get(lang, 'auction', 'list', useDemoData: true) as List<dynamic>;
      auctionList = <Auction>[];
      for (final dynamic item in result) {
        auctionList.add(Auction.fromJson(item as Map<String, dynamic>));
      }

      await refreshLatestAuction(lang);

      loaded = true;
    } catch (e) {
      Logger().e(e.toString());
      HiveHelper().writeLog('[Auction] ${e.toString()}');
    }

    notifyListeners();
  }

  Future<void> refreshLatestAuction(String lang) async {
    if (auctionList.isEmpty) {
      latestAuction = Auction.empty();
      return;
    }

    latestAuction = await getAuctionDetails(auctionList[0].id, lang);
  }

  Future<void> setCurAuction(int auctionId, String lang) async {
    loadedDetails = false;
    notifyListeners();

    curAuction = await getAuctionDetails(auctionId, lang);

    loadedDetails = true;
    notifyListeners();
  }

  void setLatestAuctionAsCurrent() {
    curAuction = latestAuction;
    notifyListeners();
  }

  Future<Auction> getAuctionDetails(int auctionId, String lang) async {
    final ApiHelper apiHelper = ApiHelper();
    try {
      final Map<String, dynamic> json =
          await apiHelper.get(lang, 'auction', 'details', urlParameters: <String>[auctionId.toString()], useDemoData: true) as Map<String, dynamic>;

      final List<dynamic> jsonItemPdfList = json['ipl'] as List<dynamic>;
      final List<dynamic> jsonLotList = json['ll'] as List<dynamic>;

      final List<AuctionItemPdf> itemPdfList = <AuctionItemPdf>[];
      final List<AuctionLot> lotList = <AuctionLot>[];

      for (final dynamic jsonItemPdf in jsonItemPdfList) {
        itemPdfList.add(AuctionItemPdf(getAuctionItemType((jsonItemPdf as Map<String, dynamic>)['t']! as String), jsonItemPdf['url']! as String));
      }

      for (final dynamic jsonLot in jsonLotList) {
        lotList.add(AuctionLot.fromJson(jsonLot as Map<String, dynamic>));
      }

      return Auction(
        json['id'] as int,
        json['n'] as String,
        DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['st'] as String),
        '${json['l'] as String} XXX',
        json['ap'] as String,
        json['rp'] as String,
        itemPdfList,
        json['r'] as String,
        lotList,
        getAuctionStatus(json['as'] as String),
        DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['lu'] as String),
      );
    } catch (e) {
      Logger().e(e.toString());
      HiveHelper().writeLog('[Auction] ${e.toString()}');
    }

    return Auction.empty();
  }

  Future<void> refreshLang(String lang) async {
    await refresh(lang: lang);
    if (curAuction.id > 0) {
      getAuctionDetails(curAuction.id, lang);
    }
  }
}
