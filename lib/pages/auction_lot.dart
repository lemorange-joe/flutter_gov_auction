import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;

class AuctionLotPage extends StatelessWidget {
  const AuctionLotPage(this.title, this.auctionLot, {super.key});

  final String title;
  final AuctionLot auctionLot;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: config.blue,
        leading: IconButton(
          icon: Semantics(
            label: S.of(context).semanticsGoBack,
            button: true,
            enabled: true,
            child: const Icon(MdiIcons.arrowLeft, color: Colors.white),
          ),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: Text(title, style: const TextStyle(color: Colors.white)),
        centerTitle: true,
      ),
      body: SafeArea(
        child: Center(
          child: Column(
            children: <Widget>[
              const SizedBox(height: 50.0),
              Hero(
                tag: 'lot_photo_${auctionLot.id}',
                child: SizedBox(
                  width: 40.0,
                  height: 40.0,
                  child: (auctionLot.photoUrl.isNotEmpty && Uri.parse(auctionLot.photoUrl).isAbsolute)
                      ? Container(
                          decoration: BoxDecoration(
                            image: DecorationImage(
                              image: CachedNetworkImageProvider(auctionLot.photoUrl),
                              fit: BoxFit.cover,
                            ),
                            borderRadius: BorderRadius.circular(config.mdBorderRadius),
                          ),
                        )
                      : Container(
                          decoration: BoxDecoration(
                            image: const DecorationImage(
                              image: AssetImage('assets/images/app_logo.png'),
                              fit: BoxFit.cover,
                            ),
                            borderRadius: BorderRadius.circular(config.mdBorderRadius),
                          ),
                        ),
                ),
              ),
              Text(auctionLot.title),
            ],
          ),
        ),
      ),
    );
  }
}
