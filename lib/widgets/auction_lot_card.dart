import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../class/auction.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;

class AuctionLotCard extends StatelessWidget {
  const AuctionLotCard(this.auctionLot, {super.key});

  final AuctionLotGridItem auctionLot;

  @override
  Widget build(BuildContext context) {
    final bool isLotPhoto = auctionLot.photoUrl.isNotEmpty && Uri.parse(auctionLot.photoUrl).isAbsolute;

    return Card(
      child: Column(
        children: <Widget>[
          AspectRatio(
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
          Text(auctionLot.description),
        ],
      ),
    );
  }
}
