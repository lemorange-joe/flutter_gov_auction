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
      height: 160.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
      width: 110.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
      child: Card(
        color: Colors.blue,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(8.0, 8.0, 8.0, 6.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              // upper half of the auction card
              GestureDetector(
                onTap: () {
                  Provider.of<AuctionProvider>(context, listen: false).setCurAuction(auction.id, S.of(context).lang);
                  showAuction();
                },
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: <Widget>[
                    Calendar(auction.startTime, size: 60.0, color: HSLColor.fromAHSL(1, (index * 2) % 360, 1, 0.333).toColor()),
                    Text(auction.auctionNum),
                  ],
                ),
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
                              size: 17.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                            ),
                            const SizedBox(width: 8.0),
                            Text(
                              '\$${utilities.formatDigits(auction.transactionTotal)}',
                              style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 13.0),
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
                          size: 16.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                        ),
                        const SizedBox(width: 8.0),
                        Text(
                          utilities.formatDigits(auction.lotCount),
                          style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 13.0),
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
    );
  }
}
