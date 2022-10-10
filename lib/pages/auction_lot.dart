import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../includes/config.dart' as config;
import '../includes/utilities.dart' as utilities;
import '../widgets/tel_group.dart';

class AuctionLotPage extends StatefulWidget {
  const AuctionLotPage(this.title, this.heroTag, this.auctionLot, {super.key});

  final String title;
  final String heroTag;
  final AuctionLot auctionLot;

  @override
  State<AuctionLotPage> createState() => _AuctionLotPageState();
}

class _AuctionLotPageState extends State<AuctionLotPage> {
  late ScrollController _scrollController;
  List<AuctionLot> relatedLots = <AuctionLot>[];
  int relatedPageNum = 0;
  String moreText = 'xxx';

  @override
  void initState() {
    super.initState();

    _scrollController = ScrollController();
    _scrollController.addListener(() {
      if (_scrollController.position.maxScrollExtent == _scrollController.offset) {
        setState(() {
          moreText = '${_scrollController.offset}: ${DateTime.now()}';
          ++relatedPageNum;
          relatedLots.add(AuctionLot.empty());
          relatedLots.add(AuctionLot.empty());
          relatedLots.add(AuctionLot.empty());
          relatedLots.add(AuctionLot.empty());
          relatedLots.add(AuctionLot.empty());
        });
      }
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  Widget _buildItemList(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Text(
          S.of(context).fieldItemList,
          style: Theme.of(context).textTheme.bodyText1!.copyWith(
                fontWeight: FontWeight.bold,
              ),
        ),
        ...widget.auctionLot.itemList
            .asMap()
            .entries
            .map((MapEntry<int, AuctionItem> entry) => Text('${entry.key + 1}. ${entry.value.description} ${entry.value.quantity} ${entry.value.unit}'))
            .toList(),
      ],
    );
  }

  Widget _buildRelatedLotList() {
    return Column(
      children: <Widget>[
        const Text('Related Lots'),
        ...relatedLots.map((AuctionLot auctionLot) => SizedBox(height: 50.0, child: Text(auctionLot.id.toString()))).toList(),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    const double titleFieldWidth = 100.0;
    final double titleImageHeight = MediaQuery.of(context).size.height / 2 * MediaQuery.of(context).textScaleFactor;

    return Scaffold(
      appBar: AppBar(
        toolbarHeight: 56.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
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
        title: Text('${S.of(context).fieldLotNum} ${widget.auctionLot.lotNum}', style: const TextStyle(color: Colors.white)),
        centerTitle: true,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          physics: const BouncingScrollPhysics(),
          controller: _scrollController,
          child: Column(
            children: <Widget>[
              SizedBox(
                width: double.infinity,
                height: titleImageHeight,
                child: ColoredBox(
                  color: Theme.of(context).backgroundColor,
                  child: Stack(
                    children: <Widget>[
                      Center(
                        child: Hero(
                          tag: widget.heroTag,
                          child: (widget.auctionLot.photoUrl.isNotEmpty && Uri.parse(widget.auctionLot.photoUrl).isAbsolute)
                              ? Container(
                                  decoration: BoxDecoration(
                                    image: DecorationImage(
                                      image: CachedNetworkImageProvider(widget.auctionLot.photoUrl),
                                      fit: BoxFit.cover,
                                    ),
                                  ),
                                )
                              : FractionallySizedBox(
                                  widthFactor: 0.618,
                                  heightFactor: 0.618,
                                  child: FittedBox(
                                    child: FaIcon(dynamic_icon_helper.getIcon(widget.auctionLot.icon.toLowerCase()) ?? FontAwesomeIcons.box),
                                  ),
                                ),
                        ),
                      ),
                      Positioned(
                        right: 0.0,
                        bottom: 8.0,
                        child: TextButton(
                          onPressed: () {},
                          style: TextButton.styleFrom(
                            fixedSize: const Size(50.0, 50.0),
                            shape: const CircleBorder(),
                            backgroundColor: const Color.fromARGB(190, 255, 255, 255),
                          ),
                          child: const Icon(
                            MdiIcons.cardsHeartOutline,
                            color: config.green,
                            size: 28.0,
                          ),
                        ),
                      )
                    ],
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(12.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: <Widget>[
                        SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldGldFileRef)),
                        Expanded(child: Text(widget.auctionLot.gldFileRef)),
                      ],
                    ),
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: <Widget>[
                        SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldDeapartment)),
                        Expanded(child: Text(widget.auctionLot.department)),
                      ],
                    ),
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: <Widget>[
                        SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldReference)),
                        Expanded(child: Text(widget.auctionLot.reference)),
                      ],
                    ),
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: <Widget>[
                        SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldContactLocation)),
                        Expanded(child: Text(widget.auctionLot.contactLocation)),
                      ],
                    ),
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: <Widget>[
                        SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldContact)),
                        Expanded(child: Text(widget.auctionLot.contact)),
                      ],
                    ),
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: <Widget>[
                        SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldContactNumber)),
                        Expanded(child: TelGroup(widget.auctionLot.contactNumber)),
                      ],
                    ),
                    Container(
                      color: Colors.yellow[300],
                      child: _buildItemList(context),
                    ),
                  ],
                ),
              ),
              _buildRelatedLotList(),
              Text(moreText),
            ],
          ),
        ),
      ),
    );
  }
}
