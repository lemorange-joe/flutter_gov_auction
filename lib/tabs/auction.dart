import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;

class AuctionTab extends StatefulWidget {
  const AuctionTab(this.auction, this.showHome, {Key? key}) : super(key: key);

  final Auction auction;
  final void Function() showHome;

  @override
  State<AuctionTab> createState() => _AuctionTabState();
}

Widget _buildLotList(List<AuctionLot> lotList) {
  
  return ListView.builder(
    itemCount: lotList.length,
    itemBuilder: (BuildContext context, int i) {
      return Text('${i + 1} ${lotList[i].reference}');
    },
  );
}

class _AuctionTabState extends State<AuctionTab> {
  @override
  Widget build(BuildContext context) {
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
              _buildLotList(widget.auction.lotList),
              _buildLotList(widget.auction.lotList.where((AuctionLot auctionLot) => auctionLot.featured).toList()),
              const Text('3'),
              const Text('4'),
              const Text('5'),
              const Text('6'),
              const Text('7'),
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
