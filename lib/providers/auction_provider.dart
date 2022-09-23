import 'dart:async';
import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import '../class/auction.dart';
import '../helpers/api_helper.dart';
import '../helpers/hive_helper.dart';

class AuctionProvider with ChangeNotifier {
  AuctionProvider();

  bool loaded = false;
  late List<Auction> auctionList;

  Future<void> refresh({String lang = 'en'}) async {
    final ApiHelper apiHelper = ApiHelper();

    try {
      final List<dynamic> result = await apiHelper.get(lang, 'auction', 'list', useDemoData: true) as List<dynamic>;
      auctionList = <Auction>[];
      for(final dynamic item in result) {
        auctionList.add(Auction.fromJson(item as Map<String, dynamic>));
      }
      
      loaded = true;
      
    } catch (e) {
      // Logger().e(e.toString());
      HiveHelper().writeLog('[Auction] ${e.toString()}');
    }

    notifyListeners();
  }
}
