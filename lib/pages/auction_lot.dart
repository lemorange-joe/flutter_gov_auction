import 'package:flutter/material.dart';
import '../class/auction.dart';

class AuctionLotPage extends StatelessWidget {
  const AuctionLotPage(this.auctionLot, {Key? key}) : super(key: key);

  final AuctionLot auctionLot;

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Text('Auction Lot'),
    );
  }
}
