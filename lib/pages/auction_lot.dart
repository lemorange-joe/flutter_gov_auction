import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:hive_flutter/hive_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../class/saved_auction.dart';
import '../generated/l10n.dart';
import '../helpers/api_helper.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../helpers/hive_helper.dart';
import '../includes/config.dart' as config;
import '../includes/utilities.dart' as utilities;
import '../providers/auction_provider.dart';
import '../widgets/auction_lot_card.dart';
import '../widgets/tel_group.dart';
import '../widgets/ui/animated_loading.dart';

class AuctionLotPage extends StatefulWidget {
  const AuctionLotPage(this.title, this.heroTagPrefix, this.auctionId, this.auctionStartTime, this.auctionLot, this.loadAuctionLotId, {super.key});

  final String title;
  final String heroTagPrefix;
  final int auctionId;
  final DateTime auctionStartTime;
  final AuctionLot auctionLot;
  final int loadAuctionLotId;

  @override
  State<AuctionLotPage> createState() => _AuctionLotPageState();
}

class _AuctionLotPageState extends State<AuctionLotPage> {
  late ScrollController _scrollController;
  List<RelatedAuctionLot> relatedLots = <RelatedAuctionLot>[];
  int relatedPageNum = 0;
  bool noMoreRelatedLots = false;
  String moreText = 'xxx';
  late AuctionLot _auctionLot;

  @override
  void initState() {
    super.initState();

    _auctionLot = widget.auctionLot;
    if (_auctionLot.id == 0 && widget.loadAuctionLotId > 0) {
      Future<void>.delayed(Duration.zero, () {
        AuctionProvider().getAuctionLot(widget.loadAuctionLotId, S.of(context).lang).then((AuctionLot result) {
          setState(() {
            _auctionLot = result;
          });
        });
      });
    }

    _scrollController = ScrollController();
    _scrollController.addListener(() {
      if (_scrollController.position.maxScrollExtent == _scrollController.offset && !noMoreRelatedLots) {
        loadRelatedLots(relatedPageNum + 1);
      }
    });
  }

  void loadRelatedLots(int page) {
    ApiHelper().get(S.of(context).lang, 'auction', 'relatedLots',
        urlParameters: <String>[_auctionLot.id.toString(), page.toString(), config.relatedLotPageSize.toString()]).then((dynamic result) {
      if (!mounted) {
        return;
      }
      final List<dynamic> resultList = result as List<dynamic>;
      final List<RelatedAuctionLot> newRelatedAuctionLot =
          resultList.map((dynamic jsonData) => RelatedAuctionLot.fromJson(jsonData as Map<String, dynamic>)).toList();
      setState(() {
        relatedPageNum = page;
        if (newRelatedAuctionLot.isEmpty) {
          noMoreRelatedLots = true;
        } else {
          noMoreRelatedLots = newRelatedAuctionLot.length < config.relatedLotPageSize;
          relatedLots.addAll(newRelatedAuctionLot);
        }
      });
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
        ..._auctionLot.itemList
            .asMap()
            .entries
            .map((MapEntry<int, AuctionItem> entry) => Text('${entry.key + 1}. ${entry.value.description} ${entry.value.quantity} ${entry.value.unit}'))
            .toList(),
      ],
    );
  }

  Widget _buildRelatedLotList() {
    final int totalLot = relatedLots.length;
    const double minItemWidth = 200.0;
    final int crossAxisCount = MediaQuery.of(context).size.width >= (minItemWidth + 12.0) * 3 ? 3 : 2;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Text(
          S.of(context).relatedLotsOfThisAuction,
          style: Theme.of(context).textTheme.bodyText1!.copyWith(
                fontSize: 16.0,
                fontWeight: FontWeight.bold,
              ),
        ),
        ...List<int>.generate((totalLot / crossAxisCount).ceil(), (int i) => i) // count how many number of rows needed first
            .map((int i) => SizedBox(
                  height: config.auctionLotCardHeight,
                  width: double.infinity,
                  child: Row(
                    children: List<int>.generate(crossAxisCount, (int j) => j)  // for each row, get the required number of items from relatedLots
                        .map((int j) => Expanded(
                              child: i * crossAxisCount + j < totalLot
                                  ? AuctionLotCard(relatedLots[i * crossAxisCount + j], S.of(context).relatedLotsPrefix, showLotNum: true)
                                  : Container(),
                            ))
                        .toList(),
                  ),
                ))
            .toList()
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    const double titleFieldWidth = 100.0;
    final double titleImageHeight = MediaQuery.of(context).size.height / 2 * MediaQuery.of(context).textScaleFactor;
    final bool isLotPhoto = _auctionLot.photoUrl.isNotEmpty && Uri.parse(_auctionLot.photoUrl).isAbsolute;

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
        title: _auctionLot.id == 0
            ? Text(S.of(context).loading)
            : Text(
                '${widget.title}${S.of(context).fieldLotNum} ${_auctionLot.lotNum}',
                style: const TextStyle(color: Colors.white),
              ),
        centerTitle: true,
      ),
      body: SafeArea(
        child: _auctionLot.id == 0
            ? Center(child: LemorangeLoading(size: 60.0))
            : SingleChildScrollView(
                physics: const BouncingScrollPhysics(),
                controller: _scrollController,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
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
                                tag: '${widget.heroTagPrefix}_${_auctionLot.id}',
                                child: isLotPhoto
                                    ? Container(
                                        decoration: BoxDecoration(
                                          image: DecorationImage(
                                            image: CachedNetworkImageProvider(_auctionLot.photoUrl),
                                            fit: BoxFit.cover,
                                          ),
                                        ),
                                      )
                                    : FractionallySizedBox(
                                        widthFactor: 0.618,
                                        heightFactor: 0.618,
                                        child: FittedBox(
                                          child: FaIcon(dynamic_icon_helper.getIcon(_auctionLot.icon.toLowerCase()) ?? FontAwesomeIcons.box),
                                        ),
                                      ),
                              ),
                            ),
                            Positioned(
                              right: 0.0,
                              bottom: 8.0,
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                mainAxisAlignment: MainAxisAlignment.end,
                                children: <Widget>[
                                  ValueListenableBuilder<Box<SavedAuction>>(
                                    valueListenable: Hive.box<SavedAuction>('saved_auction').listenable(),
                                    builder: (BuildContext context, _, __) {
                                      final SavedAuction curAuction = SavedAuction(
                                        widget.auctionId,
                                        _auctionLot.id,
                                        widget.auctionStartTime,
                                        _auctionLot.lotNum,
                                        _auctionLot.icon,
                                        _auctionLot.photoUrl,
                                        _auctionLot.descriptionEn,
                                        _auctionLot.descriptionTc,
                                        _auctionLot.descriptionSc,
                                      );
                                      final bool isSaved = HiveHelper().getSavedAuctionKeyList().contains(curAuction.hiveKey);

                                      return TextButton(
                                        onPressed: () async {
                                          if (isSaved) {
                                            await HiveHelper().deleteSavedAuction(curAuction);
                                          } else {
                                            curAuction.savedDate = DateTime.now();
                                            await HiveHelper().writeSavedAuction(curAuction);
                                          }
                                        },
                                        style: TextButton.styleFrom(
                                          fixedSize: const Size(50.0, 50.0),
                                          shape: const CircleBorder(),
                                          // backgroundColor: isSaved ? const Color.fromARGB(190, 255, 255, 255) : const Color.fromARGB(64, 0, 0, 0), // color TBC
                                          backgroundColor: isLotPhoto ? const Color.fromARGB(64, 0, 0, 0) : const Color.fromARGB(190, 255, 255, 255),
                                        ),
                                        child: Semantics(
                                          label: isSaved ? S.of(context).semanticsSavedItems : S.of(context).semanticsAddToSavedItems,
                                          child: Icon(
                                            isSaved ? MdiIcons.heart : MdiIcons.heartOutline,
                                            color: config.green,
                                            size: 28.0,
                                          ),
                                        ),
                                      );
                                    },
                                  ),
                                  const SizedBox(width: 10.0),
                                ],
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
                          Text(widget.heroTagPrefix),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: <Widget>[
                              SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldGldFileRef)),
                              Expanded(
                                child: Text(_auctionLot.gldFileRef),
                              ),
                            ],
                          ),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: <Widget>[
                              SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldDeapartment)),
                              Expanded(child: Text(_auctionLot.department)),
                            ],
                          ),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: <Widget>[
                              SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldReference)),
                              Expanded(child: Text(_auctionLot.reference)),
                            ],
                          ),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: <Widget>[
                              SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldContactLocation)),
                              Expanded(child: Text(_auctionLot.contactLocation)),
                            ],
                          ),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: <Widget>[
                              SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldContact)),
                              Expanded(child: Text(_auctionLot.contact)),
                            ],
                          ),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: <Widget>[
                              SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldContactNumber)),
                              Expanded(child: TelGroup(_auctionLot.contactNumber)),
                            ],
                          ),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: <Widget>[
                              SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldInspectionArrangement)),
                              Expanded(
                                child: _auctionLot.inspectionDateList.isEmpty
                                    ? const Text('-')
                                    : Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: _auctionLot.inspectionDateList
                                            .map((InspectionDate inspect) => Text(
                                                '${utilities.formatDayOfWeek(inspect.dayOfWeek, S.of(context).lang)}: ${inspect.startTime} - ${inspect.endTime}'))
                                            .toList(),
                                      ),
                              ),
                            ],
                          ),
                          _buildItemList(context),
                        ],
                      ),
                    ),
                    if (relatedPageNum == 0)
                      SizedBox(height: MediaQuery.of(context).size.height / 2)
                    else
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 12.0, vertical: 4.0),
                        child: _buildRelatedLotList(),
                      ),
                    Center(
                        child: noMoreRelatedLots
                            ? Text(
                                S.of(context).relatedLotsEmpty,
                                style: Theme.of(context).textTheme.bodyText2,
                              )
                            : LemorangeLoading()),
                  ],
                ),
              ),
      ),
    );
  }
}
