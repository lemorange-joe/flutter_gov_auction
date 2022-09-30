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
  return Expanded(
    child: ListView.builder(
      itemCount: lotList.length,
      itemBuilder: (BuildContext context, int i) {
        return Text('${i + 1} ${lotList[i].reference}');
      },
    ),
  );
}

class _AuctionTabState extends State<AuctionTab> {
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        children: <Widget>[
          Expanded(
            child: DefaultTabController(
              length: 7,
              child: Scaffold(
                appBar: AppBar(
                  backgroundColor: config.blue,
                  bottom: TabBar(
                    isScrollable: true,
                    tabs: <Widget>[
                      Tab(text: S.of(context).tabAll),
                      const Tab(icon: Icon(MdiIcons.checkDecagramOutline)),
                      const Tab(icon: Icon(MdiIcons.cardsHeartOutline)),
                      Tab(text: S.of(context).tabItemTypeC),
                      Tab(text: S.of(context).tabItemTypeUP),
                      Tab(text: S.of(context).tabItemTypeM),
                      Tab(text: S.of(context).tabItemTypeMS),
                    ],
                  ),
                  flexibleSpace: FlexibleSpaceBar(
                    title: Column(
                      children: <Widget>[
                        if (widget.auction.id == 0)
                          const SizedBox(width: 30.0, height: 30.0, child: CircularProgressIndicator())
                        else
                          Text('id: ${widget.auction.id}, ${widget.auction.startTime}\n ${widget.auction.location}'),
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
          ),
        ],
      ),
    );
  }
}
