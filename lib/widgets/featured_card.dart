import 'package:auto_size_text/auto_size_text.dart';
import 'package:flutter/material.dart';
import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;

class FeaturedCard extends StatefulWidget {
  const FeaturedCard(this.title, this.content, this.image, {super.key});

  final String title;
  final String content;
  final String image;

  @override
  State<FeaturedCard> createState() => _FeaturedCardState();
}

class _FeaturedCardState extends State<FeaturedCard> {
  bool _showDetails = false;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: 150.0,
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
                    Container(
                      decoration: BoxDecoration(
                        image: DecorationImage(
                          image: AssetImage('assets/images/${widget.image}'),
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
                                    widget.title,
                                    overflow: TextOverflow.ellipsis,
                                    style: TextStyle(
                                      color: _showDetails ? config.blue : Theme.of(context).textTheme.bodyText1!.color,
                                      fontSize: 14.0,
                                      fontWeight: _showDetails ? FontWeight.bold : FontWeight.normal,
                                    ),
                                    maxLines: 2,
                                  ),
                                  if (_showDetails)
                                    Expanded(
                                      child: SingleChildScrollView(
                                        child: Text(
                                          widget.content,
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
                                            color: Colors.grey[700],
                                          ),
                                          const SizedBox(width: config.iconTextSpacing),
                                          AutoSizeText(
                                            S.of(context).pressHoldViewDetails,
                                            style: TextStyle(
                                              color: Colors.grey[700],
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
                                        color: Colors.grey[700],
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
