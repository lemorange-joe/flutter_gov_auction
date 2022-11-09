import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:hive_flutter/hive_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/auction.dart';
import '../class/saved_auction.dart';
import '../generated/l10n.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../helpers/hive_helper.dart';
import '../includes/config.dart' as config;
import '../includes/utilities.dart' as utilities;
import '../providers/auction_provider.dart';
import '../widgets/common/dialog.dart';
import '../widgets/ui/image_loading_skeleton.dart';

class SavedPage extends StatelessWidget {
  const SavedPage({super.key});

  @override
  Widget build(BuildContext context) {
    final TabBar tabBar = TabBar(
      tabs: <Widget>[
        Padding(
          padding: const EdgeInsets.symmetric(vertical: 8.0),
          child: Text(
            S.of(context).comingAuction,
            style: const TextStyle(color: config.blue, fontSize: 16.0),
          ),
        ),
        Padding(
          padding: const EdgeInsets.symmetric(vertical: 8.0),
          child: Text(
            S.of(context).pastAuction,
            style: const TextStyle(color: config.blue, fontSize: 16.0),
          ),
        ),
      ],
    );

    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: PreferredSize(
          preferredSize: Size.fromHeight(80.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor) + 16.0),
          child: AppBar(
            backgroundColor: config.green,
            leading: IconButton(
              onPressed: () => Navigator.of(context).pop(),
              icon: Semantics(
                label: S.of(context).semanticsGoBack,
                button: true,
                enabled: true,
                child: const Icon(MdiIcons.arrowLeft, color: Colors.white),
              ),
            ),
            title: Text(S.of(context).saved, style: const TextStyle(color: Colors.white, fontSize: 20.0)),
            bottom: PreferredSize(
              preferredSize: tabBar.preferredSize,
              child: Material(
                color: Theme.of(context).backgroundColor,
                child: tabBar,
              ),
            ),
            actions: <Widget>[
              ValueListenableBuilder<Box<SavedAuction>>(
                valueListenable: Hive.box<SavedAuction>('saved_auction').listenable(),
                builder: (BuildContext context, _, __) {
                  final bool deleteAllEnabled = HiveHelper().getSavedAuctionList().isNotEmpty;

                  return IconButton(
                    onPressed: deleteAllEnabled
                        ? () async {
                            await CommonDialog.show2(
                              context,
                              S.of(context).deleteSavedItems,
                              S.of(context).confirmDeleteAllSavedItems,
                              S.of(context).ok,
                              () {
                                HiveHelper().clearAllSavedAuction();
                                Navigator.of(context).pop();
                              },
                              S.of(context).cancel,
                              () {
                                Navigator.of(context).pop();
                              },
                            );
                          }
                        : null,
                    icon: Semantics(
                      label: S.of(context).semanticsDeleteAllSaved,
                      button: true,
                      enabled: deleteAllEnabled,
                      child: Icon(
                        MdiIcons.deleteForeverOutline,
                        color: deleteAllEnabled ? Colors.white : Colors.grey[400],
                      ),
                    ),
                  );
                },
              ),
            ],
            centerTitle: true,
          ),
        ),
        body: ValueListenableBuilder<Box<SavedAuction>>(
          valueListenable: Hive.box<SavedAuction>('saved_auction').listenable(),
          builder: (BuildContext context, _, __) {
            final List<SavedAuction> savedAuctionList = HiveHelper().getSavedAuctionList();
            final List<SavedAuction> comingAuctionList = savedAuctionList
                .where((SavedAuction auction) => DateTime.now().add(const Duration(hours: 2)).compareTo(auction.auctionStartTime) <= 0)
                .toList();
            final List<SavedAuction> pastAuctionList =
                savedAuctionList.where((SavedAuction auction) => DateTime.now().add(const Duration(hours: 2)).compareTo(auction.auctionStartTime) > 0).toList();
            comingAuctionList.sort(SavedAuction.savedDateComparator);
            pastAuctionList.sort(SavedAuction.savedDateComparator);

            return TabBarView(
              children: <Widget>[
                buildSavedAuctionTab(context, comingAuctionList),
                buildSavedAuctionTab(context, pastAuctionList),
              ],
            );
          },
        ),
      ),
    );
  }

  Widget buildSavedAuctionTab(BuildContext context, List<SavedAuction> savedAuctionList) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
      child: savedAuctionList.isEmpty
          ? Center(
              child: Padding(
                padding: EdgeInsets.only(bottom: MediaQuery.of(context).size.height * 0.3),
                child: Text(S.of(context).savedAuctionEmpty),
              ),
            )
          : ListView.builder(
              itemCount: savedAuctionList.length,
              itemBuilder: (BuildContext context, int i) {
                final SavedAuction savedAuction = savedAuctionList[i];

                return Dismissible(
                  key: ValueKey<String>(savedAuction.hiveKey),
                  background: buildDismissibleBackground(context, AlignmentDirectional.centerStart),
                  secondaryBackground: buildDismissibleBackground(context, AlignmentDirectional.centerEnd),
                  onDismissed: (DismissDirection direction) {
                    HiveHelper().deleteSavedAuction(savedAuction);
                  },
                  child: buildSavedAuctionListItem(context, savedAuction),
                );
              },
            ),
    );
  }

  Widget buildDismissibleBackground(BuildContext context, AlignmentDirectional alignment) {
    return Container(
      alignment: alignment,
      color: Colors.red,
      child: const Padding(
        padding: EdgeInsets.symmetric(horizontal: 24.0),
        child: Icon(
          MdiIcons.trashCanOutline,
          color: Colors.white,
          size: 32.0,
        ),
      ),
    );
  }

  Widget buildSavedAuctionListItem(BuildContext context, SavedAuction savedAuction) {
    final bool isLotPhoto = savedAuction.photoUrl.isNotEmpty && Uri.parse(savedAuction.photoUrl).isAbsolute;
    const String heroTagPrefix = 'saved_';

    return SizedBox(
      height: 150.0 * MediaQuery.of(context).textScaleFactor,
      child: ListTile(
        onTap: () {
          Provider.of<AuctionProvider>(context, listen: false).getAuctionLot(savedAuction.lotId, S.of(context).lang).then((AuctionLot curLot) {
            Navigator.pushNamed(context, 'auction_lot', arguments: <String, dynamic>{
              'title': S.of(context).itemDetails,
              'heroTagPrefix': heroTagPrefix,
              'auctionId': savedAuction.auctionId,
              'auctionStartTime': savedAuction.auctionStartTime,
              'auctionLot': curLot,
            });
          });
        },
        title: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: <Widget>[
            SizedBox(
              width: 100.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
              height: 100.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
              child: Hero(
                tag: '${heroTagPrefix}_${savedAuction.lotId}',
                child: isLotPhoto
                    ? ClipRRect(
                        borderRadius: BorderRadius.circular(config.smBorderRadius),
                        child: CachedNetworkImage(
                          width: 100.0,
                          height: 100.0,
                          imageUrl: savedAuction.photoUrl,
                          placeholder: (_, __) => const ImageLoadingSkeleton(),
                          errorWidget: (_, __, ___) => const Image(image: AssetImage('assets/images/app_logo.png')),
                          fit: BoxFit.cover,
                        ),
                      )
                    : FractionallySizedBox(
                        widthFactor: 0.618,
                        heightFactor: 0.618,
                        child: FittedBox(
                          child: FaIcon(dynamic_icon_helper.getIcon(savedAuction.lotIcon.toLowerCase()) ?? FontAwesomeIcons.box),
                        ),
                      ),
              ),
            ),
            const SizedBox(width: 10.0),
            DefaultTextStyle(
              style: Theme.of(context).textTheme.bodyText2!.copyWith(
                    fontSize: 14.0,
                  ),
              child: Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    Text(savedAuction.lotNum),
                    Text(utilities.formatDate(savedAuction.auctionStartTime, S.of(context).lang)),
                    Flexible(
                      child: Text(
                        savedAuction.getDescription(S.of(context).lang),
                        overflow: TextOverflow.ellipsis,
                        maxLines: 2,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
