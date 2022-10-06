import 'package:expansion_tile_card/expansion_tile_card.dart';
import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;
import '../includes/enums.dart';
import '../providers/auction_provider.dart';
import '../widgets/ui/calendar.dart';

class AuctionTab extends StatefulWidget {
  const AuctionTab(this.auction, this.showHome, {Key? key}) : super(key: key);

  final Auction auction;
  final void Function() showHome;

  @override
  State<AuctionTab> createState() => _AuctionTabState();
}

class _AuctionTabState extends State<AuctionTab> with SingleTickerProviderStateMixin {
  int initialLotId = 0;
  int initialLotIndex = -1;
  final GlobalKey _initialLotKey = GlobalKey();

  @override
  void initState() {
    super.initState();

    initialLotId = Provider.of<AuctionProvider>(context, listen: false).initialLot;
  }

  Widget _buildLotList(List<AuctionLot> lotList, {int initialLotId = 0}) {
    return SingleChildScrollView(
      controller: ScrollController(initialScrollOffset: -10),
      child: GetListView(lotList, initialLotId, _initialLotKey),
      // child: ListView.builder(
      //   shrinkWrap: true,
      //   physics: const NeverScrollableScrollPhysics(),
      //   itemCount: lotList.length,
      //   itemBuilder: (BuildContext context, int i) {
      //     return ExpansionTileCard(
      //       title: Text(
      //         '${lotList[i].id}: ${lotList[i].reference}',
      //         key: initialLotId == lotList[i].id ? _initialLotKey : null,
      //       ),
      //       initiallyExpanded: initialLotId == lotList[i].id,
      //       children: <Widget>[
      //         Align(
      //           alignment: Alignment.centerLeft,
      //           child: Padding(
      //             padding: const EdgeInsets.fromLTRB(12.0, 0.0, 12.0, 12.0),
      //             child: Column(
      //               crossAxisAlignment: CrossAxisAlignment.start,
      //               children: <Widget>[
      //                 const Divider(),
      //                 Text(lotList[i].gldFileRef),
      //                 Text(lotList[i].department),
      //                 Row(
      //                   children: <Widget>[
      //                     Text(lotList[i].contact),
      //                     const SizedBox(width: 20.0),
      //                     Text(lotList[i].contactNumber),
      //                   ],
      //                 ),
      //                 Text(lotList[i].contactLocation),
      //                 const Divider(
      //                   endIndent: 50.0,
      //                 ),
      //                 Text(lotList[i].title),
      //               ],
      //             ),
      //           ),
      //         ),
      //       ],
      //     );
      //   },
      // ),
    );
  }

  @override
  Widget build(BuildContext context) {
    // Logger().d('lot length: ${widget.auction.lotList.length}');
    if (widget.auction.lotList.isNotEmpty) {
      Future<void>.delayed(const Duration(milliseconds: 100)).then((_) {
        if (_initialLotKey.currentContext != null) {
          Scrollable.ensureVisible(
            _initialLotKey.currentContext!,
            alignment: 0.25,
            duration: const Duration(milliseconds: config.transitionAnimationDuration),
            curve: Curves.easeInOut,
          );
          // Logger().w('Scrollable.ensureVisible done!');
        } else {
          // Logger().w('_initialLotKey.currentContext is null!!');
        }
      });
    } else {
      // Logger().w('lotList empty');
    }

    return Scaffold(
      body: DefaultTabController(
        length: 7,
        child: NestedScrollView(
          headerSliverBuilder: (BuildContext context, bool innerBoxIsScrolled) {
            return <Widget>[
              SliverAppBar(
                expandedHeight: 200.0,
                pinned: true,
                backgroundColor: config.blue,
                flexibleSpace: FlexibleSpaceBar(
                  centerTitle: true,
                  title: Column(
                    children: <Widget>[
                      const SizedBox(height: 60.0),
                      const SizedBox(height: 10.0),
                      Calendar(widget.auction.startTime),
                      if (widget.auction.id == 0)
                        const SizedBox(width: 30.0, height: 30.0, child: CircularProgressIndicator())
                      else
                        Text(
                          'id: ${widget.auction.id}, ${widget.auction.startTime}',
                          style: const TextStyle(fontSize: 12.0),
                        ),
                      ElevatedButton(
                        onPressed: () {
                          widget.showHome();
                        },
                        child: Text(S.of(context).home),
                      ),
                    ],
                  ),
                ),
              ),
              SliverPersistentHeader(
                delegate: _SliverAppBarDelegate(
                  TabBar(
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
                          child: const Icon(MdiIcons.cardsHeartOutline),
                        ),
                      ),
                      Tab(text: S.of(context).tabItemTypeC),
                      Tab(text: S.of(context).tabItemTypeUP),
                      Tab(text: S.of(context).tabItemTypeM),
                      Tab(text: S.of(context).tabItemTypeMS),
                    ],
                  ),
                ),
                pinned: true,
              ),
            ];
          },
          body: TabBarView(
            children: <Widget>[
              _buildLotList(widget.auction.lotList, initialLotId: initialLotId),
              _buildLotList(widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.featured).toList()),
              const Text('3'),
              _buildLotList(widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.itemType == AuctionItemType.ConfiscatedGoods).toList()),
              _buildLotList(widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.itemType == AuctionItemType.UnclaimedProperties).toList()),
              _buildLotList(widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.itemType == AuctionItemType.UnserviceableStores).toList()),
              _buildLotList(widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.itemType == AuctionItemType.SurplusServiceableStores).toList()),
            ],
          ),
        ),
      ),
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
  const GetListView(this.lotList, this.initialLotId, this.initialLotKey, {Key? key}) : super(key: key);
  @override
  State<StatefulWidget> createState() => _GetListViewState();

  final List<AuctionLot> lotList;
  final int initialLotId;
  final Key initialLotKey;
}

class _GetListViewState extends State<GetListView> with AutomaticKeepAliveClientMixin<GetListView> {
  @override
  Widget build(BuildContext context) {
    super.build(context);

    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: widget.lotList.length,
      itemBuilder: (BuildContext context, int i) {
        return ExpansionTileCard(
          title: Text(
            '${widget.lotList[i].id}: ${widget.lotList[i].reference}',
            key: widget.initialLotId == widget.lotList[i].id ? widget.initialLotKey : null,
          ),
          initiallyExpanded: widget.initialLotId == widget.lotList[i].id,
          children: <Widget>[
            Align(
              alignment: Alignment.centerLeft,
              child: Padding(
                padding: const EdgeInsets.fromLTRB(12.0, 0.0, 12.0, 12.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    const Divider(),
                    Text(widget.lotList[i].gldFileRef),
                    Text(widget.lotList[i].department),
                    Row(
                      children: <Widget>[
                        Text(widget.lotList[i].contact),
                        const SizedBox(width: 20.0),
                        Text(widget.lotList[i].contactNumber),
                      ],
                    ),
                    Text(widget.lotList[i].contactLocation),
                    const Divider(
                      endIndent: 50.0,
                    ),
                    Text(widget.lotList[i].title),
                  ],
                ),
              ),
            ),
          ],
        );
      },
    );
  }

  @override
  bool get wantKeepAlive => true;
}
