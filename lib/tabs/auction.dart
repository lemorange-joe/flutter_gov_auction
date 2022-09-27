import 'package:flutter/material.dart';
import 'package:logger/logger.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';

class AuctionController {
  late void Function(Auction newAuction) setAuction;
}

class AuctionTab extends StatefulWidget {
  const AuctionTab(this.controller, this.showHome, {Key? key}) : super(key: key);

  final AuctionController controller;
  final void Function() showHome;

  @override
  // State<AuctionTab> createState() => _AuctionTabState(controller);
  State<AuctionTab> createState() => _AuctionTabState();
}

class _AuctionTabState extends State<AuctionTab> {
  // _AuctionTabState(AuctionController controller) {
  //   controller.setAuction = setAuction;
  // }
  

  Auction auction = Auction.empty();

  @override
  void initState() {
    super.initState();

    Logger().d('init auction, id: ${auction.id}');
    widget.controller.setAuction = setAuction;
  }

  void setAuction(Auction newAuction) {
    Logger().d('widget setAuction: ${newAuction.id}');
    setState(() {
      auction = newAuction;
    });
  }

  @override
  Widget build(BuildContext context) {
    Logger().w('--- build ---');
    return Center(
      child: Column(
        children: <Widget>[
          Text('id: ${auction.id}'),
          ElevatedButton(
            onPressed: () {
              widget.showHome();
            },
            child: Text(S.of(context).home),
          ),
        ],
      ),
    );
  }
}
