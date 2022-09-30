import 'package:auto_size_text/auto_size_text.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;

class FeaturedCard extends StatefulWidget {
  const FeaturedCard(this.auctionLot, {super.key});

  final AuctionLot auctionLot;

  @override
  State<FeaturedCard> createState() => _FeaturedCardState();
}

class _FeaturedCardState extends State<FeaturedCard> {
  bool _showDetails = false;

  @override
  Widget build(BuildContext context) {
    Logger().d('${widget.auctionLot.photoUrl}: ${Uri.parse(widget.auctionLot.photoUrl).isAbsolute}');
    final Color remarksColor = Theme.of(context).brightness == Brightness.dark ? Colors.grey[100]! : Colors.grey[700]!;

    return SizedBox(
      width: 160.0 * (1 + (MediaQuery.of(context).textScaleFactor - 1) * 0.5),
      child: GestureDetector(
        onTap: () {
          setState(() {
            _showDetails = !_showDetails;
          });
        },
        onLongPress: () {
          setState(() {
            _showDetails = false;
          });
          Logger().w('Long pressed!');
        },
        child: Card(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(config.mdBorderRadius),
          ),
          child: Column(
            children: <Widget>[
              Expanded(
                child: Stack(
                  children: <Widget>[
                    Padding(
                      padding: const EdgeInsets.all(18.0),
                      child: Container(
                        decoration: BoxDecoration(
                          image: const DecorationImage(
                            image: AssetImage('assets/images/app_logo.png'),
                            fit: BoxFit.contain,
                          ),
                          borderRadius: BorderRadius.circular(config.mdBorderRadius),
                        ),
                      ),
                    ),
                    if (widget.auctionLot.photoUrl.isNotEmpty && Uri.parse(widget.auctionLot.photoUrl).isAbsolute)
                      Container(
                        decoration: BoxDecoration(
                          image: DecorationImage(
                            image: CachedNetworkImageProvider(widget.auctionLot.photoUrl),
                            fit: BoxFit.cover,
                          ),
                          borderRadius: BorderRadius.circular(config.mdBorderRadius),
                        ),
                      ),
                    Align(
                      alignment: Alignment.bottomCenter,
                      child: LayoutBuilder(
                        builder: (BuildContext context, BoxConstraints constraints) {
                          return AnimatedContainer(
                            duration: const Duration(milliseconds: config.transitionAnimationDuration),
                            width: double.infinity,
                            height: _showDetails ? constraints.maxHeight : 52.0 * MediaQuery.of(context).textScaleFactor,
                            color: Theme.of(context).backgroundColor.withAlpha(_showDetails ? 212 : 128),
                            child: Padding(
                              padding: const EdgeInsets.all(4.0),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: <Widget>[
                                  Text(
                                    widget.auctionLot.title,
                                    overflow: TextOverflow.ellipsis,
                                    style: TextStyle(
                                      color: _showDetails ? (Theme.of(context).brightness == Brightness.dark ? Colors.white : config.blue) : Theme.of(context).textTheme.bodyText1!.color,
                                      fontSize: 14.0,
                                      fontWeight: _showDetails ? FontWeight.bold : FontWeight.normal,
                                    ),
                                    maxLines: 2,
                                  ),
                                  if (_showDetails)
                                    Expanded(
                                      child: SingleChildScrollView(
                                        child: Text(
                                          widget.auctionLot.department,
                                          style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 14.0),
                                        ),
                                      ),
                                    ),
                                  if (_showDetails)
                                    Semantics(
                                      label: S.of(context).semanticsPressHoldViewDetails,
                                      excludeSemantics: true,
                                      child: Row(
                                        children: <Widget>[
                                          Icon(
                                            MdiIcons.gestureDoubleTap,
                                            size: 15.0,
                                            color: remarksColor,
                                          ),
                                          const SizedBox(width: config.iconTextSpacing),
                                          AutoSizeText(
                                            S.of(context).pressHoldViewDetails,
                                            style: TextStyle(
                                              color: remarksColor,
                                              fontSize: 12.0,
                                            ),
                                            minFontSize: 8.0,
                                            maxLines: 1,
                                          ),
                                        ],
                                      ),
                                    ),
                                  if (_showDetails)
                                    AutoSizeText(
                                      S.of(context).photoDisclaimer,
                                      style: TextStyle(
                                        color: remarksColor,
                                        fontSize: 12.0,
                                      ),
                                      minFontSize: 8.0,
                                      maxLines: 1,
                                    ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
