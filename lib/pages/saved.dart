import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/saved_auction.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';
import '../includes/config.dart' as config;
import '../includes/utilities.dart' as utilities;

class SavedPage extends StatelessWidget {
  const SavedPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: config.green,
        leading: IconButton(
          icon: Semantics(
            label: S.of(context).semanticsGoBack,
            button: true,
            enabled: true,
            child: const Icon(MdiIcons.arrowLeft, color: Colors.white),
          ),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: Text(S.of(context).saved, style: const TextStyle(color: Colors.white, fontSize: 20.0)),
        centerTitle: true,
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
          child: Column(
            children: <Widget>[
              Text(S.of(context).saved),
              Expanded(
                child: ValueListenableBuilder<Box<SavedAuction>>(
                  valueListenable: Hive.box<SavedAuction>('saved_auction').listenable(),
                  builder: (BuildContext context, _, __) {
                    final List<SavedAuction> savedAuctionList = HiveHelper().getSavedAuctionList();
                    return ListView.builder(
                      itemCount: savedAuctionList.length,
                      itemBuilder: (BuildContext context, int i) {
                        return buildSavedAuctionListItem(context, savedAuctionList[i]);
                      },
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget buildSavedAuctionListItem(BuildContext context, SavedAuction savedAuction) {
    // TODO(joe): verify getDescription(lang) is working, cannot check using demo data
    return ListTile(
      onTap: () {
        Logger().d('tapped: ${savedAuction.hiveKey}');
      },
      title: Padding(
        padding: const EdgeInsets.only(top: 8.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: <Widget>[
            Text(savedAuction.lotNum),
            Text(utilities.formatDate(savedAuction.savedDate!, S.of(context).lang)),
            Text(savedAuction.getDescription(S.of(context).lang)),
          ],
        ),
      ),
    );
  }
}
