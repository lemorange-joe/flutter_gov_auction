import 'package:flutter/material.dart';
import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../helpers/api_helper.dart';
// import '../providers/app_info_provider.dart';
import '../providers/auction_provider.dart';
import '../widgets/featured_list_view.dart';

class HomeTab extends StatefulWidget {
  const HomeTab(this.showAuction, {Key? key}) : super(key: key);

  final void Function() showAuction;

  @override
  State<HomeTab> createState() => _HomeTabState();
}

class _HomeTabState extends State<HomeTab> {
  int _counter = 0;

  Future<void> _incrementCounter() async {
    setState(() {
      _counter++;
    });

    final dynamic result = await ApiHelper().get(S.of(context).lang, 'data', 'appinfo', useDemoData: true);
    Logger().d(result);
  }

  @override
  Widget build(BuildContext context) {
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
            const Text(
              'You have pushed the button this many times:',
            ),
            Text(
              '$_counter',
              style: Theme.of(context).textTheme.headline4,
            ),
            TextButton(
              onPressed: _incrementCounter,
              child: const Icon(Icons.add),
            ),
            _buildAuctionList(context),
            ..._buildOtherSection1(),
          ],
        ),
      ),
    );
  }

  Widget _buildAuctionList(BuildContext context) {
    return Consumer<AuctionProvider>(
      builder: (BuildContext context, AuctionProvider auctionProvider, Widget? _) {
        return !auctionProvider.loaded
            ? const SizedBox(width: 20.0, height: 20.0, child: CircularProgressIndicator())
            : Column(
                children: auctionProvider.auctionList.map((Auction auction) {
                  return SizedBox(
                    height: 40.0,
                    child: GestureDetector(
                      onTap: () {
                        Provider.of<AuctionProvider>(context, listen: false).setCurAuction(auction.id, S.of(context).lang);
                        widget.showAuction();
                      },
                      child: Text('${auction.id} ${auction.startTime}'),
                    ),
                  );
                }).toList(),
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
