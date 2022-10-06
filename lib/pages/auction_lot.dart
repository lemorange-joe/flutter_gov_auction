import 'package:flutter/material.dart';
import '../class/auction.dart';

class AuctionLotPage extends StatelessWidget {
  const AuctionLotPage(this.auctionLot, {super.key});

  final AuctionLot auctionLot;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: Column(
            children: <Widget>[
              const SizedBox(height: 50.0),
              Text(auctionLot.id.toString()),
              Text(auctionLot.title),
            ],
          ),
        ),
      ),
    );
  }
}
