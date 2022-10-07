import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../includes/config.dart' as config;
import '../includes/enums.dart';
import '../includes/utilities.dart' as utilities;
import '../providers/auction_provider.dart';
// import '../widgets/tel_group.dart';
import '../widgets/ui/calendar.dart';
import '../widgets/ui/image_loading_skeleton.dart';

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

  Widget _buildLotList(int listIndex, List<AuctionLot> lotList) {
    // remove SingleChildScrollView because does not support auto expand/collapse sliver appbar
    return lotList.isEmpty ? const Center(child: Text('Empty')) : GetListView(listIndex, lotList);
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
                expandedHeight: 200.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                floating: true,
                pinned: true,
                snap: true,
                backgroundColor: config.blue,
                flexibleSpace: FlexibleSpaceBar(
                  centerTitle: true,
                  title: Column(
                    children: <Widget>[
                      const SizedBox(height: 60.0),
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
              _buildLotList(1, widget.auction.lotList),
              _buildLotList(2, widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.featured).toList()),
              const Text('3'),
              _buildLotList(4, widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.itemType == AuctionItemType.ConfiscatedGoods).toList()),
              _buildLotList(5, widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.itemType == AuctionItemType.UnclaimedProperties).toList()),
              _buildLotList(6, widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.itemType == AuctionItemType.UnserviceableStores).toList()),
              _buildLotList(7, widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.itemType == AuctionItemType.SurplusServiceableStores).toList()),
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
  const GetListView(this.listIndex, this.lotList, {Key? key}) : super(key: key);
  @override
  State<StatefulWidget> createState() => _GetListViewState();

  final int listIndex;
  final List<AuctionLot> lotList;
}

class _GetListViewState extends State<GetListView> with AutomaticKeepAliveClientMixin<GetListView> {
  @override
  Widget build(BuildContext context) {
    super.build(context);

    return ListView.builder(
      itemCount: widget.lotList.length,
      itemExtent: 150.0 * MediaQuery.of(context).textScaleFactor,
      itemBuilder: (BuildContext context, int i) {
        final AuctionLot curLot = widget.lotList[i];
        final String heroTag = 'lot_photo_${widget.listIndex}_${curLot.id}';

        return SizedBox(
          height: 150.0 * MediaQuery.of(context).textScaleFactor,
          child: ListTile(
            onTap: () {
              Navigator.pushNamed(context, 'auction_lot', arguments: <String, dynamic>{
                'title': S.of(context).itemDetails,
                'heroTag': heroTag,
                'auctionLot': curLot,
              });
            },
            title: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: <Widget>[
                SizedBox(
                  width: 100.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                  height: 100.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
                  child: Hero(
                    tag: heroTag,
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
                      children: <Widget>[
                        Text(curLot.lotNum),
                        Text(curLot.gldFileRef),
                        Text(curLot.department),
                        // Text(curLot.contact),
                        // TelGroup(curLot.contactNumber),
                        // Text(curLot.contactLocation),
                        Flexible(
                          child: Text(
                            curLot.title,
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
        );
      },
    );
  }

  @override
  bool get wantKeepAlive => true;
}
