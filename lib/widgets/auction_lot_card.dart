import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../includes/config.dart' as config;
import '../includes/enums.dart';
import '../widgets/ui/calendar.dart';

class AuctionLotCard extends StatelessWidget {
  const AuctionLotCard(this.auctionLot, this.auctionLotPageTitlePrefix, {super.key, this.showSoldIcon = true});

  final RelatedAuctionLot auctionLot;
  final String auctionLotPageTitlePrefix;
  final bool showSoldIcon;

  @override
  Widget build(BuildContext context) {
    final bool isLotPhoto = auctionLot.photoUrl.isNotEmpty && Uri.parse(auctionLot.photoUrl).isAbsolute;
    final String heroTagPrefix = '${S.of(context).recentlySold}_${auctionLot.lotId}';

    return GestureDetector(
      onTap: () {
        Navigator.pushNamed(context, 'auction_lot', arguments: <String, dynamic>{
          'title': auctionLotPageTitlePrefix,
          'heroTagPrefix': heroTagPrefix,
          'auctionId': auctionLot.auctionId,
          'auctionStartTime': auctionLot.startTime,
          'loadAuctionLotId': auctionLot.lotId,
        });
      },
      child: Card(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: <Widget>[
            AspectRatio(
              aspectRatio: 1.0,
              child: Stack(
                children: <Widget>[
                  Center(
                    child: isLotPhoto
                        ? Container(
                            decoration: BoxDecoration(
                              image: DecorationImage(
                                image: CachedNetworkImageProvider(auctionLot.photoUrl),
                                fit: BoxFit.cover,
                              ),
                            ),
                          )
                        : FractionallySizedBox(
                            widthFactor: 0.618,
                            heightFactor: 0.618,
                            child: FittedBox(
                              child: FaIcon(dynamic_icon_helper.getIcon(auctionLot.icon.toLowerCase()) ?? FontAwesomeIcons.box),
                            ),
                          ),
                  ),
                  Align(
                    alignment: Alignment.topLeft,
                    child: Padding(
                      padding: const EdgeInsets.only(left: 4.0, top: 4.0),
                      child: Icon(
                        MdiIcons.checkDecagram,
                        color: config.blue,
                        size: 22.0 * MediaQuery.of(context).textScaleFactor,
                      ),
                    ),
                  ),
                  Align(
                    alignment: Alignment.topRight,
                    child: Padding(
                      padding: const EdgeInsets.only(top: 4.0, right: 4.0),
                      child: Calendar(auctionLot.startTime, showBorder: true),
                    ),
                  ),
                ],
              ),
            ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 8.0),
                child: Column(
                  children: <Widget>[
                    const SizedBox(height: 2.0),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: <Widget>[
                        Text(
                          auctionLot.lotNum,
                          style: Theme.of(context).textTheme.bodyText1,
                        ),
                        if (showSoldIcon && auctionLot.transactionStatus == TransactionStatus.Sold)
                          FaIcon(
                            FontAwesomeIcons.sackDollar,
                            color: config.blue,
                            size: 20.0 * MediaQuery.of(context).textScaleFactor,
                          ),
                      ],
                    ),
                    const SizedBox(height: 4.0),
                    Row(
                      children: <Widget>[
                        Flexible(
                          child: Text(
                            auctionLot.description,
                            overflow: TextOverflow.ellipsis,
                            maxLines: 2,
                            style: Theme.of(context).textTheme.bodyText2!.copyWith(
                                  fontSize: 12.0,
                                ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
