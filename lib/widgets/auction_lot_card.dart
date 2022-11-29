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
  const AuctionLotCard(this.auctionLot, this.auctionLotPageTitlePrefix, {super.key, this.showSoldIcon = true, this.showLotNum = false});

  final RelatedAuctionLot auctionLot;
  final String auctionLotPageTitlePrefix;
  final bool showSoldIcon;
  final bool showLotNum;

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
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: <Widget>[
                          Icon(
                            MdiIcons.checkDecagram,
                            color: config.blue,
                            size: 22.0 * MediaQuery.of(context).textScaleFactor,
                          ),
                          const SizedBox(width: 5.0),
                          if (showSoldIcon && auctionLot.transactionStatus == TransactionStatus.Sold)
                            FaIcon(
                              FontAwesomeIcons.sackDollar,
                              color: config.blue,
                              size: 20.0 * MediaQuery.of(context).textScaleFactor,
                            ),
                        ],
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
                  if (showLotNum || true)
                    Align(
                      alignment: Alignment.bottomLeft,
                      child: Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(4.0),
                        color: isLotPhoto ? const Color.fromARGB(192, 255, 255, 255) : const Color.fromARGB(32, 0, 0, 0),
                        child: Text(auctionLot.lotNum),
                      ),
                    ),
                ],
              ),
            ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 8.0),
                child: Row(
                  children: <Widget>[
                    Flexible(
                      child: Text(
                        auctionLot.description,
                        overflow: TextOverflow.ellipsis,
                        maxLines: 2,
                      ),
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
