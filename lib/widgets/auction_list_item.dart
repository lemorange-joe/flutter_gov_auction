import 'package:flutter/material.dart';
import '../class/auction.dart';

class AuctionListItem extends StatelessWidget {
  const AuctionListItem(this.auction, {super.key});

  final Auction auction;

  @override
  Widget build(BuildContext context) {
    return Text('${auction.id} ${auction.startTime}');
  }
}
