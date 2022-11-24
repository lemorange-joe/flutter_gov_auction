import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../includes/config.dart' as config;
import '../includes/enums.dart';
import '../widgets/ui/calendar.dart';

class AuctionLotCard extends StatelessWidget {
  const AuctionLotCard(this.auctionLot, this.showSoldIcon, {super.key});

  final AuctionLotGridItem auctionLot;
  final bool showSoldIcon;

  @override
  Widget build(BuildContext context) {
    final bool isLotPhoto = auctionLot.photoUrl.isNotEmpty && Uri.parse(auctionLot.photoUrl).isAbsolute;

    return Card(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          Stack(
            children: <Widget>[
              Align(
                alignment: Alignment.topLeft,
                child: Padding(
                  padding: const EdgeInsets.only(left: 8.0, top: 8.0),
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
                  padding: const EdgeInsets.only(left: 8.0, right: 8.0),
                  child: Calendar(auctionLot.startTime, showBorder: true),
                ),
              ),
              Center(
                child: AspectRatio(
                  aspectRatio: 1.0,
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
              ),
            ],
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8.0),
            child: Text(
              auctionLot.description,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
    );
  }
}
