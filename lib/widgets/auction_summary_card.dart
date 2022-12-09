import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:provider/provider.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;
import '../includes/enums.dart';
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
      child: Card(
        color: Colors.blue,
        child: Padding(
          padding: const EdgeInsets.all(8.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              // upper half of the auction card
              Expanded(
                flex: 3,
                child: GestureDetector(
                  onTap: () {
                    Provider.of<AuctionProvider>(context, listen: false).setCurAuction(auction.id, S.of(context).lang);
                    showAuction();
                  },
                  child: Column(
                    children: <Widget>[
                      Calendar(auction.startTime),
                      Text(auction.auctionNum),
                    ],
                  ),
                ),
              ),
              // lower half of the auction card
              Expanded(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: <Widget>[
                    Row(
                      children: <Widget>[
                        Tooltip(
                          message: S.of(context).tooltipLotCount(utilities.formatDigits(auction.lotCount)),
                          showDuration: const Duration(milliseconds: config.tooltipDuration),
                          child: Row(
                            children: <Widget>[
                              const FaIcon(FontAwesomeIcons.boxesStacked),
                              const SizedBox(width: 8.0),
                              Text(utilities.formatDigits(auction.lotCount)),
                            ],
                          ),
                        ),
                        const SizedBox(width: 20.0),
                        if (auction.status == AuctionStatus.Finished)
                          Tooltip(
                            message: S.of(context).tooltipTotalTrasaction(utilities.formatDigits(auction.transactionTotal)),
                            showDuration: const Duration(milliseconds: config.tooltipDuration),
                            child: Row(
                              children: <Widget>[
                                const FaIcon(FontAwesomeIcons.moneyCheckDollar),
                                const SizedBox(width: 8.0),
                                Text(utilities.formatDigits(auction.transactionTotal)),
                              ],
                            ),
                          )
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
