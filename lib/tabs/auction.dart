import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:hive_flutter/hive_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import 'package:sliding_up_panel2/sliding_up_panel2.dart';
import 'package:url_launcher/url_launcher.dart';
import '../class/auction.dart';
import '../class/auction_reminder.dart';
import '../class/saved_auction.dart';
import '../generated/l10n.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../helpers/hive_helper.dart';
import '../includes/config.dart' as config;
// import '../includes/enums.dart';
import '../includes/utilities.dart' as utilities;
import '../providers/app_info_provider.dart';
import '../providers/auction_provider.dart';
import '../widgets/reminder_button.dart';
// import '../widgets/tel_group.dart';
import '../widgets/ui/animated_loading.dart';
import '../widgets/ui/image_loading_skeleton.dart';
import '../widgets/ui/open_external_icon.dart';

class AuctionTab extends StatefulWidget {
  const AuctionTab(this.auction, this.showHome, {Key? key}) : super(key: key);

  final Auction auction;
  final void Function() showHome;

  @override
  State<AuctionTab> createState() => _AuctionTabState();
}

class _AuctionTabState extends State<AuctionTab> with TickerProviderStateMixin {
  late TabController _tabController;
  late PanelController _panelController;
  double _denseHeaderHeight = 56.0;
  final double _auctionInfoPanelHeightRatio = 0.7;

  @override
  void initState() {
    super.initState();
    _panelController = PanelController();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Widget _buildLotList(String listIndex, List<AuctionLot> lotList) {
    // remove SingleChildScrollView because does not support auto expand/collapse sliver appbar
    return lotList.isEmpty
        ? Align(
            alignment: Alignment.topCenter,
            child: Padding(
              padding: const EdgeInsets.only(top: 80.0),
              child: LemorangeLoading(),
            ),
          )
        : GetListView(listIndex, widget.auction, lotList);
  }

  Widget _buildHeader() {
    // final double appBarHeight = AppBar().preferredSize.height + 10.0;
    return DefaultTextStyle(
      style: Theme.of(context).textTheme.bodyText1!.copyWith(
            fontSize: 16.0,
          ),
      child: Padding(
        padding: const EdgeInsets.fromLTRB(24.0, 12.0, 24.0, 0.0),
        child: Column(
          // mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: <Widget>[
            Text(utilities.formatDateTime(widget.auction.startTime, S.of(context).lang)),
            Text(widget.auction.location),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: <Widget>[
                ElevatedButton(
                  onPressed: () {
                    widget.showHome();
                  },
                  child: Text(
                    S.of(context).home,
                    style: const TextStyle(fontSize: 12.0),
                  ),
                ),
                ElevatedButton(
                  onPressed: () {
                    _panelController.animatePanelToPosition(1.0);
                  },
                  child: Text(
                    S.of(context).viewAuctionDetails,
                    style: const TextStyle(fontSize: 12.0),
                  ),
                ),
                ValueListenableBuilder<Box<AuctionReminder>>(
                  valueListenable: Hive.box<AuctionReminder>('reminder').listenable(),
                  builder: (BuildContext context, _, __) {
                    // no need to set the reminder time here, even the reminder was set in hive
                    // the PopupMenuButton onSelected event will override the remind time, just set DateTime(1900) is ok
                    final AuctionReminder reminder = AuctionReminder(widget.auction.id, DateTime(1900), widget.auction.startTime);
                    // final AuctionReminder reminder = AuctionReminder(widget.auction.id, DateTime(1900), DateTime.now().add(const Duration(days: 3)));  // for testing reminder

                    return ReminderButton(reminder, HiveHelper().getAuctionReminderIdList().contains(widget.auction.id));
                  },
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDenseHeader() {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        crossAxisAlignment: CrossAxisAlignment.baseline,
        textBaseline: TextBaseline.alphabetic,
        children: <Widget>[
          IconButton(
            onPressed: () {
              widget.showHome();
            },
            iconSize: 24.0 * MediaQuery.of(context).textScaleFactor,
            splashRadius: 24.0 * MediaQuery.of(context).textScaleFactor,
            icon: const Icon(
              MdiIcons.arrowLeft,
              color: Colors.white,
            ),
          ),
          Text(
            utilities.formatShortDateTime(widget.auction.startTime),
            style: const TextStyle(
              fontSize: 18.0,
              color: Colors.white,
            ),
          ),
          SizedBox(width: 36.0 * MediaQuery.of(context).textScaleFactor),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    _denseHeaderHeight = 56.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor);

    return Scaffold(
      body: Consumer<AppInfoProvider>(
        builder: (BuildContext context, AppInfoProvider appInfoProvider, Widget? _) {
          final Map<String, String> itemTypes = appInfoProvider.appInfo.itemTypeList;

          _tabController = TabController(length: itemTypes.entries.length + 3, vsync: this);
          _tabController.index = Provider.of<AuctionProvider>(context, listen: false).initialShowFeatured ? 1 : 0;

          return !appInfoProvider.loaded
              ? LemorangeLoading()
              : SlidingUpPanel(
                  panelBuilder: () => _buildAuctionInfoPanel(),
                  controller: _panelController,
                  minHeight: 0.0,
                  maxHeight: MediaQuery.of(context).size.height * _auctionInfoPanelHeightRatio,
                  isDraggable: false,
                  backdropEnabled: true,
                  body: DefaultTabController(
                    length: itemTypes.length + 3,
                    child: NestedScrollView(
                      headerSliverBuilder: (BuildContext context, bool innerBoxIsScrolled) {
                        return <Widget>[
                          SliverAppBar(
                            expandedHeight: 180.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                            floating: true,
                            pinned: true,
                            snap: true,
                            titleSpacing: 0.0,
                            flexibleSpace: LayoutBuilder(builder: (BuildContext context, BoxConstraints constraints) {
                              final double top = constraints.biggest.height * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor);
                              final bool showNormalHeader = top > _denseHeaderHeight + MediaQuery.of(context).padding.top;

                              return FlexibleSpaceBar(
                                background: ColoredBox(color: showNormalHeader ? Theme.of(context).scaffoldBackgroundColor : config.blue),
                                centerTitle: true,
                                expandedTitleScale: 1.0,
                                title: widget.auction.id == 0
                                    ? const SizedBox(width: 30.0, height: 30.0, child: CircularProgressIndicator())
                                    : (showNormalHeader ? _buildHeader() : _buildDenseHeader()),
                              );
                            }),
                          ),
                          SliverPersistentHeader(
                            delegate: _SliverAppBarDelegate(
                              TabBar(
                                controller: _tabController,
                                isScrollable: true,
                                overlayColor: MaterialStateProperty.all(Colors.red),
                                labelColor: Theme.of(context).textTheme.bodyText2!.color,
                                indicatorColor: config.green,
                                tabs: <Widget>[
                                  Tab(text: S.of(context).tabAll),
                                  Tab(
                                    icon: Semantics(
                                      label: S.of(context).semanticsFeaturedItems,
                                      child: const Icon(MdiIcons.checkDecagramOutline),
                                    ),
                                  ),
                                  Tab(
                                    icon: Semantics(
                                      label: S.of(context).semanticsSavedItems,
                                      child: ValueListenableBuilder<Box<SavedAuction>>(
                                        valueListenable: Hive.box<SavedAuction>('saved_auction').listenable(),
                                        builder: (BuildContext context, _, __) {
                                          return HiveHelper()
                                                  .getSavedAuctionList()
                                                  .where((SavedAuction auction) => auction.auctionId == widget.auction.id)
                                                  .isEmpty
                                              ? const Icon(MdiIcons.heartOutline)
                                              : const Icon(MdiIcons.heart);
                                        },
                                      ),
                                    ),
                                  ),
                                  ...itemTypes.entries.map((MapEntry<String, String> entry) => Tab(text: entry.value)).toList(),
                                ],
                              ),
                            ),
                            pinned: true,
                          ),
                        ];
                      },
                      body: TabBarView(
                        key: Key(widget.auction.id.toString()),
                        controller: _tabController,
                        children: <Widget>[
                          _buildLotList('1', widget.auction.lotList),
                          _buildLotList('2', widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.featured).toList()),
                          ValueListenableBuilder<Box<SavedAuction>>(
                            valueListenable: Hive.box<SavedAuction>('saved_auction').listenable(),
                            builder: (BuildContext context, _, __) {
                              final List<String> savedLotNums = HiveHelper()
                                  .getSavedAuctionList()
                                  .where((SavedAuction auction) => auction.auctionId == widget.auction.id)
                                  .map((SavedAuction auction) => auction.lotNum)
                                  .toList();

                              final List<AuctionLot> savedAuctionLotList = widget.auction.lotList.where((AuctionLot auctionLot) {
                                return savedLotNums.contains(auctionLot.lotNum);
                              }).toList();

                              return savedAuctionLotList.isEmpty
                                  ? Center(
                                      child: Padding(
                                      padding: const EdgeInsets.only(bottom: 150.0),
                                      child: Text(S.of(context).savedAuctionEmpty, style: Theme.of(context).textTheme.bodyText1),
                                    ))
                                  : _buildLotList('3', savedAuctionLotList);
                            },
                          ),
                          ...itemTypes.entries
                              .map(
                                (MapEntry<String, String> entry) => _buildLotList(
                                    entry.key, widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.itemType == entry.key).toList()),
                              )
                              .toList(),
                        ],
                      ),
                    ),
                  ),
                );
        },
      ),
    );
  }

  Widget _buildAuctionInfoPanel() {
    final Map<String, String> itemTypes = Provider.of<AppInfoProvider>(context, listen: false).appInfo.itemTypeList;

    return SizedBox(
      height: MediaQuery.of(context).size.height * _auctionInfoPanelHeightRatio,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 8.0),
        child: Column(
          children: <Widget>[
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: <Widget>[
                Text(
                  S.of(context).auctionDetails,
                  style: Theme.of(context).textTheme.bodyText1!.copyWith(fontWeight: FontWeight.bold),
                ),
                TextButton(
                  onPressed: () {
                    _panelController.animatePanelToPosition(0.0);
                  },
                  child: const Icon(MdiIcons.close),
                ),
              ],
            ),
            const Divider(height: 2.0, thickness: 1.0),
            Expanded(
              child: SingleChildScrollView(
                child: Column(
                  children: <Widget>[
                    Table(
                      columnWidths: const <int, TableColumnWidth>{
                        0: FractionColumnWidth(0.3),
                        1: FractionColumnWidth(0.7),
                      },
                      children: <TableRow>[
                        _getAuctionInfoPanelRow(
                          S.of(context).auctionNumber,
                          Text(widget.auction.auctionNum, style: Theme.of(context).textTheme.bodyText1),
                        ),
                        _getAuctionInfoPanelRow(
                          S.of(context).auctionStartDate,
                          Text(utilities.formatDate(widget.auction.startTime, S.of(context).lang), style: Theme.of(context).textTheme.bodyText1),
                        ),
                        _getAuctionInfoPanelRow(
                          S.of(context).auctionStartTime,
                          Text(utilities.formatTime(widget.auction.startTime, S.of(context).lang), style: Theme.of(context).textTheme.bodyText1),
                        ),
                        _getAuctionInfoPanelRow(
                          S.of(context).location,
                          Text(widget.auction.location, style: Theme.of(context).textTheme.bodyText1),
                        ),
                        _getAuctionInfoPanelRow(
                          '${S.of(context).collectionDeadline}*',
                          Text(utilities.formatDateTime(widget.auction.collectionDeadline, S.of(context).lang), style: Theme.of(context).textTheme.bodyText1),
                        ),
                        _getAuctionInfoPanelRow(
                          S.of(context).notesForBidders,
                          (widget.auction.auctionPdfUrl.startsWith('http'))
                              ? _getPdfButtonColumn(_getPdfButton('PDF', widget.auction.auctionPdfUrl))
                              : const Text(config.emptyCharacter),
                        ),
                        _getAuctionInfoPanelRow(
                          S.of(context).auctionResult,
                          (widget.auction.resultPdfUrl.startsWith('http'))
                              ? _getPdfButtonColumn(_getPdfButton('PDF', widget.auction.resultPdfUrl))
                              : const Text(config.emptyCharacter),
                        ),
                        _getAuctionInfoPanelRow(
                          S.of(context).auctionList,
                          _getPdfButtonColumn(
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: widget.auction.itemPdfList
                                  .map((AuctionItemPdf itemPdf) => Padding(
                                        padding: const EdgeInsets.only(bottom: 4.0),
                                        child: _getPdfButton(itemTypes[itemPdf.itemType] ?? 'PDF', itemPdf.pdfUrl),
                                      ))
                                  .toList(),
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10.0),
                    Text(
                      '* ${S.of(context).collectionDeadlineStatement(utilities.formatDateTime(widget.auction.collectionDeadline, S.of(context).lang))}',
                      style: Theme.of(context).textTheme.bodyText2!.copyWith(
                            fontSize: 12.0,
                          ),
                    ),
                    const SizedBox(height: 200.0),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  TableRow _getAuctionInfoPanelRow(String fieldName, Widget childWidget) {
    return TableRow(children: <Widget>[
      Padding(
        padding: const EdgeInsets.only(top: 4.0),
        child: Text(fieldName, style: Theme.of(context).textTheme.bodyText2),
      ),
      Padding(
        padding: const EdgeInsets.only(top: 4.0),
        child: childWidget,
      ),
    ]);
  }

  Widget _getPdfButtonColumn(Widget pdfButton) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        pdfButton,
        Text(
          '(${S.of(context).gldSource})',
          style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 12.0),
        ),
      ],
    );
  }

  Widget _getPdfButton(String title, String pdfUrl) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: <Widget>[
        Semantics(
          label: S.of(context).semanticsOpenFileName(title),
          excludeSemantics: true,
          link: true,
          child: GestureDetector(
            onTap: () async {
              await launchUrl(Uri.parse(pdfUrl), mode: LaunchMode.externalApplication);
            },
            child: Text(
              title,
              style: const TextStyle(color: config.blue),
            ),
          ),
        ),
        const OpenExternalIcon(),
      ],
    );
  }
}

class _SliverAppBarDelegate extends SliverPersistentHeaderDelegate {
  _SliverAppBarDelegate(this._tabBar);

  final TabBar _tabBar;

  @override
  double get minExtent => _tabBar.preferredSize.height;
  @override
  double get maxExtent => _tabBar.preferredSize.height;

  @override
  Widget build(BuildContext context, double shrinkOffset, bool overlapsContent) {
    return ColoredBox(
      color: Theme.of(context).backgroundColor,
      child: _tabBar,
    );
  }

  @override
  bool shouldRebuild(_SliverAppBarDelegate oldDelegate) {
    return false;
  }
}

class GetListView extends StatefulWidget {
  const GetListView(this.listIndex, this.auction, this.lotList, {Key? key}) : super(key: key);
  @override
  State<StatefulWidget> createState() => _GetListViewState();

  final String listIndex;
  final Auction auction;
  final List<AuctionLot> lotList;
}

class _GetListViewState extends State<GetListView> with AutomaticKeepAliveClientMixin<GetListView> {
  @override
  Widget build(BuildContext context) {
    super.build(context);

    final int itemCount = widget.lotList.isEmpty ? 0 : widget.lotList.length + 1;

    return ListView.builder(
      itemCount: itemCount,
      itemExtent: 150.0 * MediaQuery.of(context).textScaleFactor,
      itemBuilder: (BuildContext context, int i) {
        if (i == itemCount - 1) {
          // pad the bottom of the list
          return SizedBox(
            height: 120.0 * MediaQuery.of(context).textScaleFactor,
            child: Container(),
          );
        }

        final AuctionLot curLot = widget.lotList[i];
        final String heroTagPrefix = 'lot_photo_${widget.listIndex}';

        return SizedBox(
          height: 120.0 * MediaQuery.of(context).textScaleFactor,
          child: Card(
            elevation: 0.0,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(8.0),
            ),
            child: InkWell(
              onTap: () {
                Navigator.pushNamed(context, 'auction_lot', arguments: <String, dynamic>{
                  'heroTagPrefix': heroTagPrefix,
                  'auctionId': widget.auction.id,
                  'auctionNum': widget.auction.auctionNum,
                  'auctionStartTime': widget.auction.startTime,
                  'auctionLot': curLot,
                });
              },
              borderRadius: BorderRadius.circular(8.0),
              child: Row(
                children: <Widget>[
                  SizedBox(
                    width: 100.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                    height: 100.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                    child: Hero(
                      tag: '${heroTagPrefix}_${curLot.id}',
                      child: (curLot.photoUrl.isNotEmpty && Uri.parse(curLot.photoUrl).isAbsolute)
                          ? ClipRRect(
                              borderRadius: BorderRadius.circular(config.smBorderRadius),
                              child: CachedNetworkImage(
                                width: 100.0,
                                height: 100.0,
                                imageUrl: curLot.photoUrl,
                                placeholder: (_, __) => const ImageLoadingSkeleton(),
                                errorWidget: (_, __, ___) => const Image(image: AssetImage('assets/images/app_logo.png')),
                                fit: BoxFit.cover,
                              ),
                            )
                          : FractionallySizedBox(
                              widthFactor: 0.618,
                              heightFactor: 0.618,
                              child: FittedBox(
                                child: FaIcon(dynamic_icon_helper.getIcon(curLot.icon.toLowerCase()) ?? FontAwesomeIcons.box),
                              ),
                            ),
                    ),
                  ),
                  const SizedBox(width: 10.0),
                  DefaultTextStyle(
                    style: Theme.of(context).textTheme.bodyText2!.copyWith(
                          fontSize: 14.0,
                        ),
                    child: Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: <Widget>[
                          Text(curLot.lotNum),
                          Text(curLot.gldFileRef),
                          Text(curLot.department),
                          // Text(curLot.contact),
                          // TelGroup(curLot.contactNumber),
                          // Text(curLot.contactLocation),
                          Flexible(
                            child: Text(
                              curLot.description,
                              overflow: TextOverflow.ellipsis,
                              maxLines: 2,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  @override
  bool get wantKeepAlive => true;
}
