import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/auction.dart';
import '../class/home_controller.dart';
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
  const HomeTab(this.showAuction, this.homeController, {Key? key}) : super(key: key);

  final void Function() showAuction;
  final HomeController homeController;

  @override
  State<HomeTab> createState() => _HomeTabState();
}

class _HomeTabState extends State<HomeTab> {
  late ScrollController _scrollController;
  final List<Widget> _hotCategoryGridList = <Widget>[]; // store the list of hot category auction grid, append items during onScroll event
  int _hotCategoryIndex = -1; // TODO(joe): always load the "sold" category first after appInfo is loaded, then randomize others
  bool _loadingHotCategory = false;
  bool _noMoreHotCategory = false;

  @override
  void initState() {
    super.initState();
    _scrollController = ScrollController()..addListener(onScroll);
    widget.homeController.clearHotCategoryList = clearHotCategoryList;
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void clearHotCategoryList() {
    setState(() {
      _hotCategoryIndex = -1;
      _hotCategoryGridList.clear();
    });
  }

  void onScroll() {
    if (!_noMoreHotCategory && _scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200.0 && !_loadingHotCategory) {
      setState(() {
        _loadingHotCategory = true;
      });
      _loadHotCategoryGrid(_hotCategoryIndex + 1);
    }
  }

  void _loadHotCategoryGrid(int i) {
    final TextStyle titleStyle = TextStyle(
      color: Theme.of(context).brightness == Brightness.dark ? Colors.white : config.blue,
      fontSize: 16.0,
      fontWeight: FontWeight.bold,
    );

    final Map<String, String> gridCategoryMap = Provider.of<AppInfoProvider>(context, listen: false).gridCategoryList;
    if (i >= gridCategoryMap.keys.length) {
      setState(() {
        _noMoreHotCategory = true;
        _loadingHotCategory = false;
      });
      return;
    }

    final String gridCategoryKey = gridCategoryMap.keys.toList()[i];
    final String gridCategoryTitle = gridCategoryMap[gridCategoryKey]!;
    final String gridViewTitle =
        gridCategoryTitle.isEmpty ? S.of(context).recentlySold : '${S.of(context).searchGridBefore}$gridCategoryTitle${S.of(context).searchGridAfter}';
    final String auctionLotPageTitlePrefix = gridCategoryTitle.isEmpty ? '${S.of(context).recentlySold}: ' : '$gridCategoryTitle: ';

    ApiHelper()
        .get(S.of(context).lang, 'auction', 'searchGrid', urlParameters: <String>[gridCategoryKey, config.gridItemCount.toString()]).then((dynamic result) {
      if (!mounted) {
        return;
      }

      final List<RelatedAuctionLot> itemList = <RelatedAuctionLot>[];
      for (final dynamic item in result as List<dynamic>) {
        itemList.add(RelatedAuctionLot.fromJson(item as Map<String, dynamic>));
      }

      setState(() {
        _hotCategoryGridList.add(
          Padding(
            padding: const EdgeInsets.only(bottom: 20.0),
            child: AuctionLotGridView(gridViewTitle, itemList, titleStyle, auctionLotPageTitlePrefix, showSoldIcon: true),
          ),
        );
        _hotCategoryIndex = i;
        _loadingHotCategory = false;
      });
    });
  }

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
        controller: _scrollController,
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
            const SizedBox(height: 200.0),  // to be removed after auto loading the sold "category" to ensure scrolling
            ..._hotCategoryGridList,
            if (_loadingHotCategory) Center(child: LemorangeLoading()),
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
