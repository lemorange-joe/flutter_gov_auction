import 'package:flutter/material.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../includes/utilities.dart' as utilities;

class AuctionListItem extends StatelessWidget {
  const AuctionListItem(this.auction, {super.key});

  final Auction auction;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 20.0),
      decoration: BoxDecoration(
        border: Border(
          bottom: BorderSide(color: Colors.grey[200]!),
        ),
      ),
      child: Row(
        children: <Widget>[
          SizedBox(
            width: 80.0,
            child: Text(auction.auctionNum),
          ),
          Text(utilities.formatDate(auction.startTime, S.of(context).lang)),
        ],
      ),
    );
  }
}
