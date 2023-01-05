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
  const AuctionSummaryCard(this.index, this.auction, this.showAuction, {super.key});

  final int index;
  final Auction auction;
  final void Function() showAuction;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 170.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
      width: 115.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
      child: GestureDetector(
        onTap: () {
          Provider.of<AuctionProvider>(context, listen: false).setCurAuction(auction.id, S.of(context).lang);
          showAuction();
        },
        child: Card(
          color: Theme.of(context).backgroundColor,
          child: Padding(
            padding: const EdgeInsets.fromLTRB(8.0, 12.0, 8.0, 10.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: <Widget>[
                // upper half of the auction card
                Column(
                  mainAxisSize: MainAxisSize.min,
                  children: <Widget>[
                    // gradient color change, for reference
                    // Calendar(auction.startTime, showBorder: true, size: 60.0, color: HSLColor.fromAHSL(1, (index * 5) % 360, 1, 0.333).toColor()),
                    Calendar(auction.startTime, showBorder: true, size: 60.0, color: auction.startTime.month.isOdd ? config.green : config.blue),
                    const SizedBox(height: 5.0),
                    Text(auction.auctionNum, style: Theme.of(context).textTheme.bodyText1),
                  ],
                ),
                // lower half of the auction card
                Column(
                  mainAxisSize: MainAxisSize.min,
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: <Widget>[
                    if (auction.status == AuctionStatus.Finished)
                      Padding(
                        padding: const EdgeInsets.only(bottom: 5.0),
                        child: Tooltip(
                          message: S.of(context).tooltipTotalTrasaction(utilities.formatDigits(auction.transactionTotal)),
                          showDuration: const Duration(milliseconds: config.tooltipDuration),
                          child: Row(
                            children: <Widget>[
                              FaIcon(
                                FontAwesomeIcons.moneyCheckDollar,
                                size: 16.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                                color: Theme.of(context).textTheme.bodyText2!.color,
                              ),
                              const SizedBox(width: 4.0),
                              Text(
                                '\$${utilities.formatDigits(auction.transactionTotal)}',
                                maxLines: 1,
                                style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 12.0),
                                overflow: TextOverflow.fade,
                              ),
                            ],
                          ),
                        ),
                      ),
                    Tooltip(
                      message: S.of(context).tooltipLotCount(utilities.formatDigits(auction.lotCount)),
                      showDuration: const Duration(milliseconds: config.tooltipDuration),
                      child: Row(
                        children: <Widget>[
                          FaIcon(
                            FontAwesomeIcons.boxesStacked,
                            color: Theme.of(context).textTheme.bodyText2!.color,
                            size: 15.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                          ),
                          const SizedBox(width: 4.0),
                          Text(
                            utilities.formatDigits(auction.lotCount),
                            style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 12.0),
                          ),
                        ],
                      ),
                    ),
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
