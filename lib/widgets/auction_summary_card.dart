import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:provider/provider.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
// import '../includes/config.dart' as config;
import '../includes/utilities.dart' as utilities;
import '../providers/auction_provider.dart';
import '../widgets/ui/calendar.dart';

class AuctionSummaryCard extends StatelessWidget {
  const AuctionSummaryCard(this.auction, this.showAuction, {super.key});

  final Auction auction;
  final void Function() showAuction;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 180.0 * utilities.adjustedPhotoScale(MediaQuery.of(context).textScaleFactor),
      width: 250.0,
      child: GestureDetector(
        onTap: () {
          Provider.of<AuctionProvider>(context, listen: false).setCurAuction(auction.id, S.of(context).lang);
          showAuction();
        },
        child: Card(
          color: Colors.blue,
          child: Padding(
            padding: const EdgeInsets.all(6.0),
            child: Column(
              children: <Widget>[
                Calendar(auction.startTime),
                Text(auction.auctionNum),
                Row(
                  children: <Widget>[
                    const FaIcon(FontAwesomeIcons.box),
                    Text(auction.lotCount.toString()),
                  ],
                ),
                Row(
                  children: <Widget>[
                    const FaIcon(FontAwesomeIcons.sackDollar),
                    Text(auction.transactionTotal.toString()),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
