import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;
import '../includes/enums.dart';
import '../providers/auction_provider.dart';
import '../widgets/featured_card.dart';
import '../widgets/ui/animated_loading.dart';

class FeaturedListView extends StatelessWidget {
  const FeaturedListView(this.auction, this.homeTabShowAuction, {super.key});

  final Auction auction;
  final Function() homeTabShowAuction;

  void showFeaturedLot(BuildContext context) {
    Provider.of<AuctionProvider>(context, listen: false).setLatestAuctionAsCurrent();
    homeTabShowAuction();
  }

  @override
  Widget build(BuildContext context) {
    Color titleColor = auction.status == AuctionStatus.Finished ? Theme.of(context).textTheme.bodyText1!.color! : config.blue;
    if (Theme.of(context).brightness == Brightness.dark) {
      titleColor = auction.status == AuctionStatus.Finished ? Colors.grey[100]! : Colors.white;
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: <Widget>[
            Row(
              children: <Widget>[
                Icon(
                  MdiIcons.checkDecagram,
                  color: titleColor,
                  size: 22.0 * MediaQuery.of(context).textScaleFactor,
                ),
                const SizedBox(width: config.iconTextSpacing),
                Text(
                  '${auction.status == AuctionStatus.Finished ? S.of(context).previousAuction : S.of(context).nextAuction} - ${S.of(context).featuredItems}',
                  style: TextStyle(
                    color: titleColor,
                    fontSize: 16.0,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
            TextButton(
              onPressed: () {
                Provider.of<AuctionProvider>(context, listen: false).setLatestAuctionAsCurrent();
                homeTabShowAuction();
              },
              style: TextButton.styleFrom(
                padding: EdgeInsets.zero,
                tapTargetSize: MaterialTapTargetSize.shrinkWrap,
              ),
              child: Text(S.of(context).viewAll),
            ),
          ],
        ),
        SizedBox(
          height: 250.0 * (1 + (MediaQuery.of(context).textScaleFactor - 1) * 0.5),
          child: Consumer<AuctionProvider>(builder: (BuildContext context, AuctionProvider auctionProvider, Widget? _) {
            return auctionProvider.loaded
                ? SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    child: Row(
                      children: auction.lotList
                          .where((AuctionLot auctionLot) => auctionLot.featured)
                          .map((AuctionLot auctionLot) => FeaturedCard(auctionLot, showFeaturedLot))
                          .toList(),
                    ),
                  )
                : Center(
                    child: LemorangeLoading(size: 60.0),
                  );
          }),
        ),
      ],
    );
  }
}
