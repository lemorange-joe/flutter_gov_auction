import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:hive_flutter/hive_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../class/auction_reminder.dart';
import '../class/saved_auction.dart';
import '../generated/l10n.dart';
import '../helpers/api_helper.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../helpers/hive_helper.dart';
import '../includes/config.dart' as config;
import '../includes/utilities.dart' as utilities;
import '../widgets/reminder_button.dart';
import '../widgets/tel_group.dart';
import '../widgets/ui/animated_loading.dart';

class AuctionLotPage extends StatefulWidget {
  const AuctionLotPage(this.title, this.heroTagPrefix, this.auctionId, this.auctionStartTime, this.auctionLot, {super.key});

  final String title;
  final String heroTagPrefix;
  final int auctionId;
  final DateTime auctionStartTime;
  final AuctionLot auctionLot;

  @override
  State<AuctionLotPage> createState() => _AuctionLotPageState();
}

class _AuctionLotPageState extends State<AuctionLotPage> {
  late ScrollController _scrollController;
  List<RelatedAuctionLot> relatedLots = <RelatedAuctionLot>[];
  int relatedPageNum = 0;
  bool noMoreRelatedLots = false;
  String moreText = 'xxx';

  @override
  void initState() {
    super.initState();

    _scrollController = ScrollController();
    _scrollController.addListener(() {
      if (_scrollController.position.maxScrollExtent == _scrollController.offset && !noMoreRelatedLots) {
        loadRelatedLots(relatedPageNum + 1);
      }
    });
  }

  void loadRelatedLots(int page) {
    ApiHelper()
        .get(S.of(context).lang, 'auction', 'relatedLots', urlParameters: <String>[widget.auctionLot.id.toString(), page.toString()]).then((dynamic result) {
      if (!mounted) {
        return;
      }
      final List<dynamic> resultList = result as List<dynamic>;
      final List<RelatedAuctionLot> newRelatedAuctionLot =
          resultList.map((dynamic jsonData) => RelatedAuctionLot.fromjson(jsonData as Map<String, dynamic>)).toList();
      setState(() {
        relatedPageNum = page;
        if (newRelatedAuctionLot.isEmpty) {
          noMoreRelatedLots = true;
        } else {
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
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Text(
          S.of(context).relatedLots,
          style: Theme.of(context).textTheme.bodyText1!.copyWith(
                fontSize: 16.0,
                fontWeight: FontWeight.bold,
              ),
        ),
        ...relatedLots.map((RelatedAuctionLot auctionLot) => SizedBox(height: 50.0, child: Text('${auctionLot.lotNum}: ${auctionLot.description}'))).toList(),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    const double titleFieldWidth = 100.0;
    final double titleImageHeight = MediaQuery.of(context).size.height / 2 * MediaQuery.of(context).textScaleFactor;
    final bool isLotPhoto = widget.auctionLot.photoUrl.isNotEmpty && Uri.parse(widget.auctionLot.photoUrl).isAbsolute;
    final AuctionReminder reminder = AuctionReminder.fromAuctionLot(widget.auctionId, widget.auctionStartTime, widget.auctionLot); 
    // final AuctionReminder reminder = AuctionReminder.fromAuctionLot(widget.auctionId, DateTime.now().add(const Duration(days: 3)), widget.auctionLot); // for testing reminder

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
        title: Text(
          '${S.of(context).fieldLotNum} ${widget.auctionLot.lotNum}',
          style: const TextStyle(color: Colors.white),
        ),
        centerTitle: true,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
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
                          tag: '${widget.heroTagPrefix}_${widget.auctionLot.id}',
                          child: isLotPhoto
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
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: <Widget>[
                            ValueListenableBuilder<Box<SavedAuction>>(
                              valueListenable: Hive.box<SavedAuction>('saved_auction').listenable(),
                              builder: (BuildContext context, _, __) {
                                final SavedAuction curAuction = SavedAuction(
                                  widget.auctionId,
                                  widget.auctionLot.id,
                                  widget.auctionStartTime,
                                  widget.auctionLot.lotNum,
                                  widget.auctionLot.icon,
                                  widget.auctionLot.photoUrl,
                                  widget.auctionLot.descriptionEn,
                                  widget.auctionLot.descriptionTc,
                                  widget.auctionLot.descriptionSc,
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
                            ValueListenableBuilder<Box<AuctionReminder>>(
                              valueListenable: Hive.box<AuctionReminder>('reminder').listenable(),
                              builder: (BuildContext context, _, __) {
                                return ReminderButton(reminder, HiveHelper().getAuctionReminderIdList().contains(widget.auctionLot.id));
                              }
                            ),
                            const SizedBox(width: 20.0),
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
                          child: Text(widget.auctionLot.gldFileRef),
                        ),
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
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: <Widget>[
                        SizedBox(width: titleFieldWidth, child: Text(S.of(context).fieldInspectionArrangement)),
                        Expanded(
                          child: widget.auctionLot.inspectionDateList.isEmpty
                              ? const Text('-')
                              : Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: widget.auctionLot.inspectionDateList
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
