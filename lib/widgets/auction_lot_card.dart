import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../class/saved_auction.dart';
import '../generated/l10n.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../includes/config.dart' as config;
import '../includes/enums.dart';
import '../includes/utilities.dart' as utilities;
import '../widgets/ui/calendar.dart';

class AuctionLotCardData {
  AuctionLotCardData(
      this.auctionId, this.lotId, this.auctionNum, this.lotNum, this.startTime, this.icon, this.photoUrl, this.description, this.transactionStatus);

  factory AuctionLotCardData.fromRelatedAuctionLot(RelatedAuctionLot lot) {
    return AuctionLotCardData(lot.auctionId, lot.lotId, lot.auctionNum, lot.lotNum, lot.startTime, lot.icon, lot.photoUrl, lot.description, lot.transactionStatus);
  }

  factory AuctionLotCardData.fromSavedAuctionLot(SavedAuction lot, String lang) {
    return AuctionLotCardData(lot.auctionId, lot.lotId, lot.auctionNum, lot.lotNum, lot.auctionStartTime, lot.lotIcon, lot.photoUrl, lot.getDescription(lang), '');
  }

  final int auctionId;
  final int lotId;
  final String auctionNum;
  final String lotNum;
  final DateTime startTime;
  final String icon;
  final String photoUrl;
  final String description;
  final String transactionStatus;
}

class AuctionLotCard extends StatelessWidget {
  const AuctionLotCard(this.auctionLotData, this.auctionLotPageTitlePrefix, {super.key, this.showSoldIcon = true});

  final AuctionLotCardData auctionLotData;
  final String auctionLotPageTitlePrefix;
  final bool showSoldIcon;

  @override
  Widget build(BuildContext context) {
    final bool isLotPhoto = auctionLotData.photoUrl.isNotEmpty && Uri.parse(auctionLotData.photoUrl).isAbsolute;
    final String heroTagPrefix = '${S.of(context).recentlySold}_${auctionLotData.lotId}';

    return GestureDetector(
      onTap: () {
        Navigator.pushNamed(context, 'auction_lot', arguments: <String, dynamic>{
          'title': auctionLotPageTitlePrefix,
          'heroTagPrefix': heroTagPrefix,
          'auctionId': auctionLotData.auctionId,
          'auctionNum': auctionLotData.auctionNum,
          'auctionStartTime': auctionLotData.startTime,
          'loadAuctionLotId': auctionLotData.lotId,
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
                                image: CachedNetworkImageProvider(auctionLotData.photoUrl),
                                fit: BoxFit.cover,
                              ),
                            ),
                          )
                        : FractionallySizedBox(
                            widthFactor: 0.618,
                            heightFactor: 0.618,
                            child: FittedBox(
                              child: FaIcon(dynamic_icon_helper.getIcon(auctionLotData.icon.toLowerCase()) ?? FontAwesomeIcons.box),
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
                      child: Calendar(auctionLotData.startTime, showBorder: true),
                    ),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(8.0, 0.0, 8.0, 4.0),
              child: Column(
                children: <Widget>[
                  const SizedBox(height: 2.0),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: <Widget>[
                      Text(
                        auctionLotData.lotNum,
                        style: Theme.of(context).textTheme.bodyText1,
                      ),
                      if (showSoldIcon && auctionLotData.transactionStatus == TransactionStatus.Sold)
                        FaIcon(
                          FontAwesomeIcons.sackDollar,
                          color: config.blue,
                          size: 20.0 * MediaQuery.of(context).textScaleFactor,
                        ),
                    ],
                  ),
                  const SizedBox(height: 4.0),
                  SizedBox(
                    height: 40.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                    child: Row(
                      children: <Widget>[
                        Flexible(
                          child: Text(
                            auctionLotData.description,
                            overflow: TextOverflow.ellipsis,
                            maxLines: 2,
                            style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 12.0),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
