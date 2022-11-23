import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import './auction_lot_card.dart';
import '../class/auction.dart';

class AuctionLotGridView extends StatelessWidget {
  const AuctionLotGridView(this.title, this.auctionLotList, {super.key});

  final String title;
  final List<AuctionLotGridItem> auctionLotList;

  @override
  Widget build(BuildContext context) {
    return GridView.count(
      crossAxisCount: 2,
      children: auctionLotList.map((AuctionLotGridItem auctionLot) => AuctionLotCard(auctionLot)).toList(),
    );
  }
}
