import 'package:cached_network_image/cached_network_image.dart';
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
  late TabController _tabController;

  @override
  void initState() {
    super.initState();

    _tabController = TabController(length: 7, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Widget _buildLotList(List<AuctionLot> lotList) {
    // remove SingleChildScrollView because does not support auto expand/collapse sliver appbar
    return lotList.isEmpty ? const Center(child: Text('Empty')) : GetListView(lotList);
  }

  @override
  Widget build(BuildContext context) {
    _tabController.index = Provider.of<AuctionProvider>(context, listen: false).initialShowFeatured ? 1 : 0;

    return Scaffold(
      body: DefaultTabController(
        length: 7,
        child: NestedScrollView(
          headerSliverBuilder: (BuildContext context, bool innerBoxIsScrolled) {
            return <Widget>[
              SliverAppBar(
                expandedHeight: 200.0,
                floating: true,
                pinned: true,
                snap: true,
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
            key: Key(widget.auction.id.toString()),
            controller: _tabController,
            children: <Widget>[
              _buildLotList(widget.auction.lotList),
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
  const GetListView(this.lotList, {Key? key}) : super(key: key);
  @override
  State<StatefulWidget> createState() => _GetListViewState();

  final List<AuctionLot> lotList;
}

class _GetListViewState extends State<GetListView> with AutomaticKeepAliveClientMixin<GetListView> {
  @override
  Widget build(BuildContext context) {
    super.build(context);

    return ListView.builder(
      itemCount: widget.lotList.length,
      itemBuilder: (BuildContext context, int i) {
        final AuctionLot curLot = widget.lotList[i];

        return ListTile(
          onTap: () {
            Navigator.pushNamed(context, 'auction_lot', arguments: <String, dynamic>{'title': S.of(context).itemDetails, 'auctionLot': curLot});
          },
          leading: Hero(
            tag: 'lot_photo_${curLot.id}',
            child: SizedBox(
              width: 40.0,
              height: 40.0,
              child: (curLot.photoUrl.isNotEmpty && Uri.parse(curLot.photoUrl).isAbsolute)
                  ? Container(
                      decoration: BoxDecoration(
                        image: DecorationImage(
                          image: CachedNetworkImageProvider(curLot.photoUrl),
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
          title: Column(
            children: <Widget>[
              Text(curLot.gldFileRef),
              Text(curLot.department),
              Row(
                children: <Widget>[
                  Text(curLot.contact),
                  const SizedBox(width: 20.0),
                  Text(curLot.contactNumber),
                ],
              ),
              Text(curLot.contactLocation),
              const Divider(
                endIndent: 50.0,
              ),
              Text(curLot.title),
            ],
          ),
        );
      },
    );
  }

  @override
  bool get wantKeepAlive => true;
}
