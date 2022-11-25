import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../helpers/api_helper.dart';
import '../includes/config.dart' as config;
import '../providers/app_info_provider.dart';
import '../providers/auction_provider.dart';
import '../widgets/auction_list_item.dart';
import '../widgets/auction_lot_grid_view.dart';
import '../widgets/featured_list_view.dart';
import '../widgets/ui/animated_loading.dart';

class HomeTab extends StatefulWidget {
  const HomeTab(this.showAuction, {Key? key}) : super(key: key);

  final void Function() showAuction;

  @override
  State<HomeTab> createState() => _HomeTabState();
}

class _HomeTabState extends State<HomeTab> {
  @override
  Widget build(BuildContext context) {
    final TextStyle titleStyle = TextStyle(
      color: Theme.of(context).brightness == Brightness.dark ? Colors.white : config.blue,
      fontSize: 16.0,
      fontWeight: FontWeight.bold,
    );

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12.0),
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: <Widget>[
            const SizedBox(height: 10.0),
            SizedBox(
              height: 40.0 * MediaQuery.of(context).textScaleFactor,
              child: OutlinedButton(
                onPressed: () {
                  Navigator.pushNamed(context, 'search');
                },
                style: OutlinedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(horizontal: 8.0),
                  shape: const RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(50.0))),
                  foregroundColor: Theme.of(context).textTheme.bodyText1!.color,
                ),
                child: Row(
                  children: <Widget>[
                    Icon(
                      MdiIcons.magnify,
                      color: Theme.of(context).textTheme.bodyText1!.color,
                      size: 24.0 * MediaQuery.of(context).textScaleFactor,
                    ),
                    const SizedBox(width: 8.0),
                    Text(S.of(context).searchAuction, style: Theme.of(context).textTheme.bodyText2),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 10.0),
            Row(
              children: <Widget>[
                Expanded(
                  child: Consumer<AuctionProvider>(
                    builder: (BuildContext context, AuctionProvider auctionProvider, Widget? _) {
                      return FeaturedListView(auctionProvider.latestAuction, widget.showAuction);
                    },
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10.0),
            _buildAuctionList(titleStyle),
            Consumer<AppInfoProvider>(
              builder: (BuildContext context, AppInfoProvider appInfoProvider, Widget? _) {
                // TODO(joe): add lazy load all categories
                return appInfoProvider.loaded ? 
                  Column(
                    children: <Widget>[
                      Padding(
                        padding: const EdgeInsets.only(bottom: 20.0),
                        child: _buildAuctionLotGrid(0, titleStyle),
                      ),
                      Padding(
                        padding: const EdgeInsets.only(bottom: 20.0),
                        child: _buildAuctionLotGrid(1, titleStyle),
                      ),
                      Padding(
                        padding: const EdgeInsets.only(bottom: 20.0),
                        child: _buildAuctionLotGrid(2, titleStyle),
                      ),
                    ],
                  )
                : Container();
              },
            ),
            ..._buildOtherSection1(),
          ],
        ),
      ),
    );
  }

  Widget _buildAuctionList(TextStyle titleStyle) {
    return Consumer<AuctionProvider>(
      builder: (BuildContext context, AuctionProvider auctionProvider, Widget? _) {
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: <Widget>[
            Text(S.of(context).pastAuctionList, style: titleStyle),
            if (auctionProvider.loaded)
              ...auctionProvider.auctionList
                  .map(
                    (Auction auction) => InkWell(
                      onTap: () {
                        Provider.of<AuctionProvider>(context, listen: false).setCurAuction(auction.id, S.of(context).lang);
                        widget.showAuction();
                      },
                      child: AuctionListItem(auction),
                    ),
                  )
                  .toList()
            else
              LemorangeLoading(),
          ],
        );
      },
    );
  }

  Widget _buildAuctionLotGrid(int gridCategoryIndex, TextStyle titleStyle) {
    final Map<String, String> gridCategoryMap = Provider.of<AppInfoProvider>(context, listen: false).gridCategoryList;
    final String gridCategoryKey = gridCategoryMap.keys.toList()[gridCategoryIndex];
    final String gridCategoryTitle = gridCategoryMap[gridCategoryKey]!;
    final String gridViewTitle = gridCategoryTitle.isEmpty ? S.of(context).recentlySold : gridCategoryTitle;

    return FutureBuilder<dynamic>(
      future: ApiHelper().get(S.of(context).lang, 'auction', 'searchGrid', urlParameters: <String>[gridCategoryKey, config.gridItemCount.toString()]),
      builder: (BuildContext context, AsyncSnapshot<dynamic> snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return LemorangeLoading();
        }

        final List<dynamic> result = snapshot.data as List<dynamic>;
        final List<AuctionLotGridItem> itemList = <AuctionLotGridItem>[];
        for (final dynamic item in result) {
          itemList.add(AuctionLotGridItem.fromJson(item as Map<String, dynamic>));
        }

        return AuctionLotGridView(gridViewTitle, itemList, titleStyle, showSoldIcon: true);
      },
    );
  }

  List<Widget> _buildOtherSection1() {
    return <Widget>[
      const Text(
        'Remarks',
        style: TextStyle(fontSize: 20.0),
      ),
      const Text('Item 1'),
      const Text('Item 2'),
      const Text('Item 3'),
      const Text('Item 4'),
      const Text('Item 5'),
    ];
  }
}
