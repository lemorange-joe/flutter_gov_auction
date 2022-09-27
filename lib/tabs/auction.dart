import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';

class AuctionTab extends StatefulWidget {
  const AuctionTab(this.auction, this.showHome, {Key? key}) : super(key: key);

  final Auction auction;
  final void Function() showHome;

  @override
  State<AuctionTab> createState() => _AuctionTabState();
}

class _AuctionTabState extends State<AuctionTab> {
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
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
    );
  }
}
