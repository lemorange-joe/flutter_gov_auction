import 'package:flutter/material.dart';
import '../class/auction.dart';

class AuctionDetailsPage extends StatelessWidget {
  const AuctionDetailsPage(this.auction, {Key? key}) : super(key: key);

  final Auction auction;

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Text('Auction Details'),
    );
  }
}
