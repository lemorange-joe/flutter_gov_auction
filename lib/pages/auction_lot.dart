import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../includes/config.dart' as config;

class AuctionLotPage extends StatelessWidget {
  const AuctionLotPage(this.title, this.heroTag, this.auctionLot, {super.key});

  final String title;
  final String heroTag;
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
        child: SingleChildScrollView(
          physics: const BouncingScrollPhysics(),
          child: Column(
            children: <Widget>[
              Hero(
                tag: heroTag,
                child: SizedBox(
                  width: double.infinity,
                  height: MediaQuery.of(context).size.height / 2,
                  child: (auctionLot.photoUrl.isNotEmpty && Uri.parse(auctionLot.photoUrl).isAbsolute)
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
                            fit: BoxFit.fill,
                            child: FaIcon(dynamic_icon_helper.getIcon(auctionLot.icon.toLowerCase()) ?? FontAwesomeIcons.box),
                          ),
                        ),
                ),
              ),
              const SizedBox(height: 100.0),
              const Text('ABC'),
              const SizedBox(height: 100.0),
              const Text('ABC'),
              const SizedBox(height: 100.0),
              const Text('ABC'),
              const SizedBox(height: 100.0),
              Text(auctionLot.title),
            ],
          ),
        ),
      ),
    );
  }
}
