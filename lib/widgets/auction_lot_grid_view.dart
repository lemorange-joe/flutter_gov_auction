import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import './auction_lot_card.dart';
import '../class/auction.dart';

class AuctionLotGridView extends StatelessWidget {
  const AuctionLotGridView(this.title, this.auctionLotList, this.titleStyle, this.auctionLotPageTitlePrefix, {super.key, this.showSoldIcon = false});

  final String title;
  final List<AuctionLotGridItem> auctionLotList;
  final TextStyle titleStyle;
  final String auctionLotPageTitlePrefix;
  final bool showSoldIcon;

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Text(title, style: titleStyle),
        ...getListItem(context, auctionLotList),
      ],
    );
  }

  List<Widget> getListItem(BuildContext context, List<AuctionLotGridItem> auctionLotList) {
    const double minItemWidth = 200.0;
    const double itemHeight = 220.0;
    final int crossAxisCount = MediaQuery.of(context).size.width >= (minItemWidth + 12.0) * 3 ? 3 : 2;
    final List<Widget> rowList = <Widget>[];
    final int totalLot = auctionLotList.length;

    for (int i = 0; i < totalLot / crossAxisCount; ++i) {
      rowList.add(
        SizedBox(
          height: itemHeight,
          width: double.infinity,
          child: Row(
            children: List<int>.generate(crossAxisCount, (int j) => j)
                .map(
                  (int j) => Expanded(
                    child: i * crossAxisCount + j < totalLot ? AuctionLotCard(auctionLotList[i * crossAxisCount + j], auctionLotPageTitlePrefix, showSoldIcon) : Container(),
                  ),
                )
                .toList(),
          ),
        ),
      );
    }

    return rowList;
  }
}
