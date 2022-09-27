import 'package:flutter/material.dart';
import 'package:logger/logger.dart';
import 'package:provider/provider.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../helpers/api_helper.dart';
// import '../include/utilities.dart' as utilities;
import '../providers/app_info_provider.dart';
import '../providers/auction_provider.dart';

class HomeTab extends StatefulWidget {
  const HomeTab(this.showAuction, {Key? key}) : super(key: key);

  final void Function(Auction) showAuction;

  @override
  State<HomeTab> createState() => _HomeTabState();
}

class _HomeTabState extends State<HomeTab> {
  int _counter = 0;

  @override
  void initState() {
    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<AppInfoProvider>(context, listen: false).refresh(lang: S.of(context).lang);
      Provider.of<AuctionProvider>(context, listen: false).refresh(lang: S.of(context).lang);
    });
  }

  Future<void> _incrementCounter() async {
    setState(() {
      _counter++;
    });

    final dynamic result = await ApiHelper().get(S.of(context).lang, 'data', 'appinfo', useDemoData: true);
    Logger().d(result);
  }

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        children: <Widget>[
          Expanded(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              mainAxisAlignment: MainAxisAlignment.center,
              children: <Widget>[
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
                SizedBox(
                  height: MediaQuery.of(context).size.height / 5,
                  child: _buildAuctionList(context),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAuctionList(BuildContext context) {
    return Consumer<AuctionProvider>(builder: (BuildContext context, AuctionProvider auctionProvider, Widget? _) {
      return !auctionProvider.loaded
          ? const SizedBox(width: 20.0, height: 20.0, child: CircularProgressIndicator())
          : ListView.builder(
              itemCount: auctionProvider.auctionList.length,
              itemBuilder: (BuildContext context, int i) {
                final Auction auction = auctionProvider.auctionList[i];
                return SizedBox(
                  height: 40.0,
                  child: GestureDetector(
                    onTap: () {
                      widget.showAuction(auction);
                    },
                    child: Text('${auction.id} ${auction.startTime}'),
                  ),
                );
              });
    });
  }
}
