import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
// import 'package:logger/logger.dart';
import '../class/saved_auction.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';
import '../includes/config.dart' as config;
import '../widgets/auction_lot_card.dart';

class SavedAuctionLotListView extends StatelessWidget {
  const SavedAuctionLotListView({super.key});

  @override
  Widget build(BuildContext context) {
    return ValueListenableBuilder<Box<SavedAuction>>(
        valueListenable: Hive.box<SavedAuction>('saved_auction').listenable(),
        builder: (BuildContext context, _, __) {
          final HiveHelper hiveHelper = HiveHelper();
          final List<SavedAuction> savedAuctionList = hiveHelper.getSavedAuctionList();

          return savedAuctionList.isEmpty
              ? Container()
              : Row(
                  children: <Widget>[
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: <Widget>[
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: <Widget>[
                              Padding(
                                padding: const EdgeInsets.only(left: 4.0),
                                child: Text(
                                  S.of(context).saved,
                                  style: const TextStyle(
                                    color: config.blue,
                                    fontSize: config.titleFontSize,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                              TextButton(
                                onPressed: () {
                                  Navigator.pushNamed(context, 'saved');
                                },
                                style: TextButton.styleFrom(
                                  padding: EdgeInsets.zero,
                                  tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                                ),
                                child: Text(S.of(context).viewAll),
                              ),
                            ],
                          ),
                          Padding(
                            padding: const EdgeInsets.only(right: 4.0),
                            child: SingleChildScrollView(
                              scrollDirection: Axis.horizontal,
                              child: Row(
                                children: savedAuctionList
                                    .map((SavedAuction savedAuction) => SizedBox(
                                          width: MediaQuery.of(context).size.width / 3,
                                          child: AuctionLotCard(AuctionLotCardData.fromSavedAuctionLot(savedAuction, S.of(context).lang), S.of(context).savedPrefix),
                                        ))
                                    .toList(),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                );
        });
  }
}
