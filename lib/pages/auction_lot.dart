import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter_config/flutter_config.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:hive_flutter/hive_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../class/app_info.dart';
import '../class/auction.dart';
import '../class/saved_auction.dart';
import '../generated/l10n.dart';
import '../helpers/api_helper.dart';
import '../helpers/dynamic_icon_helper.dart' as dynamic_icon_helper;
import '../helpers/hive_helper.dart';
import '../includes/config.dart' as config;
import '../includes/enums.dart';
import '../includes/utilities.dart' as utilities;
import '../providers/app_info_provider.dart';
import '../providers/auction_provider.dart';
import '../widgets/auction_lot_card.dart';
import '../widgets/common/dialog.dart';
import '../widgets/info_button.dart';
import '../widgets/tel_group.dart';
import '../widgets/ui/animated_loading.dart';
import '../widgets/ui/open_external_icon.dart';

class Spacer extends StatelessWidget {
  const Spacer({super.key, this.height = 10.0});

  final double height;

  @override
  Widget build(BuildContext context) {
    return SizedBox(height: height * MediaQuery.of(context).textScaleFactor);
  }
}

class AuctionLotPage extends StatefulWidget {
  const AuctionLotPage(this.title, this.heroTagPrefix, this.auctionId, this.auctionStartTime, this.auctionLot, this.loadAuctionLotId, {super.key});

  final String title;
  final String heroTagPrefix;
  final int auctionId;
  final DateTime auctionStartTime;
  final AuctionLot auctionLot;
  final int loadAuctionLotId;

  @override
  State<AuctionLotPage> createState() => _AuctionLotPageState();
}

class _AuctionLotPageState extends State<AuctionLotPage> {
  late ScrollController _scrollController;
  List<RelatedAuctionLot> relatedLots = <RelatedAuctionLot>[];
  int relatedPageNum = 0;
  bool noMoreRelatedLots = false;
  late AuctionLot _auctionLot;

  @override
  void initState() {
    super.initState();

    _auctionLot = widget.auctionLot;
    if (_auctionLot.id == 0 && widget.loadAuctionLotId > 0) {
      Future<void>.delayed(Duration.zero, () {
        AuctionProvider().getAuctionLot(widget.loadAuctionLotId, S.of(context).lang).then((AuctionLot result) {
          setState(() {
            _auctionLot = result;
          });
        });
      });
    }

    _scrollController = ScrollController();
    _scrollController.addListener(() {
      if (_scrollController.position.maxScrollExtent == _scrollController.offset && !noMoreRelatedLots) {
        loadRelatedLots(relatedPageNum + 1);
      }
    });
  }

  void loadRelatedLots(int page) {
    ApiHelper().get(S.of(context).lang, 'auction', 'relatedLots',
        urlParameters: <String>[_auctionLot.id.toString(), page.toString(), config.relatedLotPageSize.toString()]).then((dynamic result) {
      if (!mounted) {
        return;
      }
      final List<dynamic> resultList = result as List<dynamic>;
      final List<RelatedAuctionLot> newRelatedAuctionLot =
          resultList.map((dynamic jsonData) => RelatedAuctionLot.fromJson(jsonData as Map<String, dynamic>)).toList();
      setState(() {
        relatedPageNum = page;
        if (newRelatedAuctionLot.isEmpty) {
          noMoreRelatedLots = true;
        } else {
          noMoreRelatedLots = newRelatedAuctionLot.length < config.relatedLotPageSize;
          relatedLots.addAll(newRelatedAuctionLot);
        }
      });
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  Widget _buildItemList(BuildContext context, TextStyle headerStyle) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Text(
          S.of(context).fieldItemList,
          style: headerStyle,
        ),
        ..._auctionLot.itemList
            .asMap()
            .entries
            .map((MapEntry<int, AuctionItem> entry) => _buildAuctionItem(entry.key + 1, entry.value.description, entry.value.quantity, entry.value.unit))
            .toList(),
      ],
    );
  }

  Widget _buildAuctionItem(int i, String description, String quantity, String unit) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 2.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          SizedBox(
            width: 32.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
            child: Text(
              '$i.',
              textAlign: TextAlign.end,
            ),
          ),
          const SizedBox(width: 8.0),
          Flexible(
            child: RichText(
              text: TextSpan(
                style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 16.0),
                children: <InlineSpan>[
                  TextSpan(
                    text: '$description ',
                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                          fontSize: 14.0 * MediaQuery.of(context).textScaleFactor,
                        ),
                  ),
                  WidgetSpan(
                    child: ColoredBox(
                      color: Theme.of(context).brightness == Brightness.dark ? Colors.grey[600]! : Colors.grey[300]!,
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 4.0),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: <Widget>[
                            Text(
                              '$quantity ',
                              style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                    fontWeight: FontWeight.bold,
                                  ),
                            ),
                            Text(
                              unit,
                              style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                    backgroundColor: Theme.of(context).brightness == Brightness.dark ? Colors.grey[600]! : Colors.grey[300]!,
                                  ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRelatedLotList() {
    final int totalLot = relatedLots.length;
    const double minItemWidth = 200.0;
    final int crossAxisCount = MediaQuery.of(context).size.width >= (minItemWidth + 12.0) * 3 ? 3 : 2;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Text(
          S.of(context).relatedLotsOfThisAuction,
          style: Theme.of(context).textTheme.bodyText1!.copyWith(
                fontSize: config.titleFontSize,
                color: config.blue,
                fontWeight: FontWeight.bold,
              ),
        ),
        ...List<int>.generate((totalLot / crossAxisCount).ceil(), (int i) => i) // count how many number of rows needed first
            .map((int i) => SizedBox(
                  height: config.auctionLotCardHeight,
                  width: double.infinity,
                  child: Row(
                    children: List<int>.generate(crossAxisCount, (int j) => j) // for each row, get the required number of items from relatedLots
                        .map((int j) => Expanded(
                              child: i * crossAxisCount + j < totalLot
                                  ? AuctionLotCard(relatedLots[i * crossAxisCount + j], S.of(context).relatedLotsPrefix)
                                  : Container(),
                            ))
                        .toList(),
                  ),
                ))
            .toList()
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    final AppInfoProvider appInfoProvider = Provider.of<AppInfoProvider>(context, listen: false);
    final double titleImageHeight = MediaQuery.of(context).size.height / 2 * MediaQuery.of(context).textScaleFactor;
    final bool isLotPhoto = _auctionLot.photoUrl.isNotEmpty && Uri.parse(_auctionLot.photoUrl).isAbsolute;
    final TextStyle headerStyle = Theme.of(context).textTheme.bodyText1!.copyWith(
          fontWeight: FontWeight.bold,
        );
    final TextStyle fieldStyle = Theme.of(context).textTheme.bodyText1!;

    return Scaffold(
      appBar: AppBar(
        toolbarHeight: 56.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
        backgroundColor: config.blue,
        leading: IconButton(
          icon: Semantics(
            label: S.of(context).semanticsGoBack,
            button: true,
            enabled: true,
            child: const Icon(MdiIcons.arrowLeft, color: Colors.white),
          ),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: _auctionLot.id == 0
            ? Text(S.of(context).loading)
            : Text(
                '${widget.title}${S.of(context).fieldLotNum} ${_auctionLot.lotNum}',
                style: const TextStyle(color: Colors.white),
              ),
        centerTitle: true,
      ),
      body: SafeArea(
        child: _auctionLot.id == 0
            ? Center(child: LemorangeLoading(size: 60.0))
            : SingleChildScrollView(
                physics: const BouncingScrollPhysics(),
                controller: _scrollController,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    SizedBox(
                      width: double.infinity,
                      height: titleImageHeight,
                      child: ColoredBox(
                        color: Theme.of(context).backgroundColor,
                        child: Stack(
                          children: <Widget>[
                            Center(
                              child: Hero(
                                tag: '${widget.heroTagPrefix}_${_auctionLot.id}',
                                child: isLotPhoto
                                    ? Container(
                                        decoration: BoxDecoration(
                                          image: DecorationImage(
                                            image: CachedNetworkImageProvider(_auctionLot.photoUrl),
                                            fit: BoxFit.cover,
                                          ),
                                        ),
                                      )
                                    : FractionallySizedBox(
                                        widthFactor: 0.618,
                                        heightFactor: 0.618,
                                        child: FittedBox(
                                          child: FaIcon(dynamic_icon_helper.getIcon(_auctionLot.icon.toLowerCase()) ?? FontAwesomeIcons.box),
                                        ),
                                      ),
                              ),
                            ),
                            Positioned(
                              right: 0.0,
                              bottom: 8.0,
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                mainAxisAlignment: MainAxisAlignment.end,
                                children: <Widget>[
                                  ValueListenableBuilder<Box<SavedAuction>>(
                                    valueListenable: Hive.box<SavedAuction>('saved_auction').listenable(),
                                    builder: (BuildContext context, _, __) {
                                      final SavedAuction curAuction = SavedAuction(
                                        widget.auctionId,
                                        _auctionLot.id,
                                        widget.auctionStartTime,
                                        _auctionLot.lotNum,
                                        _auctionLot.icon,
                                        _auctionLot.photoUrl,
                                        _auctionLot.descriptionEn,
                                        _auctionLot.descriptionTc,
                                        _auctionLot.descriptionSc,
                                      );
                                      final HiveHelper hiveHelper = HiveHelper();
                                      final bool isSaved = hiveHelper.getSavedAuctionKeyList().contains(curAuction.hiveKey);

                                      return ElevatedButton(
                                        onPressed: () async {
                                          if (isSaved) {
                                            await hiveHelper.deleteSavedAuction(curAuction);
                                          } else {
                                            const String tipsKey = 'check_auction_details_on_gld_website';
                                            if (!hiveHelper.getTips(tipsKey)) {
                                              CommonDialog.showWithTips(
                                                context,
                                                S.of(context).tipsCheckAuctionOnGldWebsiteTitle,
                                                S.of(context).tipsCheckAuctionOnGldWebsiteContent,
                                                tipsKey,
                                                S.of(context).tipsCheckAuctionOnGldWebsiteCheckbox,
                                                S.of(context).ok,
                                                () {
                                                  Navigator.pop(context);
                                                },
                                              );
                                            }

                                            curAuction.savedDate = DateTime.now();
                                            await hiveHelper.writeSavedAuction(curAuction);
                                          }
                                        },
                                        style: ElevatedButton.styleFrom(
                                          fixedSize: const Size(50.0, 50.0),
                                          shape: const CircleBorder(),
                                          // backgroundColor: isSaved ? const Color.fromARGB(190, 255, 255, 255) : const Color.fromARGB(64, 0, 0, 0), // color TBC
                                          backgroundColor: Colors.white,
                                          elevation: 2.0,
                                        ),
                                        child: Semantics(
                                          label: isSaved ? S.of(context).semanticsSavedItems : S.of(context).semanticsAddToSavedItems,
                                          child: Icon(
                                            isSaved ? MdiIcons.heart : MdiIcons.heartOutline,
                                            color: config.green,
                                            size: 28.0,
                                          ),
                                        ),
                                      );
                                    },
                                  ),
                                  const Spacer(),
                                ],
                              ),
                            )
                          ],
                        ),
                      ),
                    ),
                    Card(
                      elevation: 1.0,
                      child: Padding(
                        padding: const EdgeInsets.all(12.0),
                        child: SelectableRegion(
                          selectionControls: materialTextSelectionControls,
                          focusNode: FocusNode(),
                          child: Table(
                            columnWidths: const <int, TableColumnWidth>{
                              0: FractionColumnWidth(0.28),
                              1: FractionColumnWidth(0.72),
                            },
                            children: <TableRow>[
                              _buildAuctionLotRow(
                                  S.of(context).fieldAuctionDate, Text(utilities.formatDate(_auctionLot.auctionDate, S.of(context).lang), style: fieldStyle)),
                              _buildAuctionLotRow(S.of(context).fieldLotNum, Text(_auctionLot.lotNum, style: fieldStyle)),
                              _buildAuctionLotRow(S.of(context).fieldItemType, Text(appInfoProvider.getItemTypeName(_auctionLot.itemType), style: fieldStyle)),
                              _buildAuctionLotRow(S.of(context).fieldGldFileRef, Text(_auctionLot.gldFileRef, style: fieldStyle)),
                              _buildAuctionLotRow(S.of(context).fieldDeapartment, Text(_auctionLot.department, style: fieldStyle)),
                              _buildAuctionLotRow(S.of(context).fieldReference, Text(_auctionLot.reference, style: fieldStyle)),
                              _buildAuctionLotRow(S.of(context).fieldContactLocation, _buildContactLocationItem(fieldStyle)),
                              _buildAuctionLotRow(S.of(context).fieldContact, _buildContactPersonItem(fieldStyle)),
                              _buildAuctionLotRow(S.of(context).fieldContactNumber, TelGroup(_auctionLot.contactNumber)),
                              _buildAuctionLotRow(S.of(context).fieldItemConditions,
                                  Text(_auctionLot.itemCondition.isEmpty ? config.emptyCharacter : _auctionLot.itemCondition, style: fieldStyle)),
                              _buildAuctionLotRow(
                                _auctionLot.specialInspection ? S.of(context).fieldSpecialInspectionArrangement : S.of(context).fieldInspectionArrangement,
                                _auctionLot.inspectionDateList.isEmpty
                                    ? const Text(config.emptyCharacter)
                                    : Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: _auctionLot.inspectionDateList
                                            .map(
                                              (InspectionDate inspect) => _buildInspectionDateItem(context, inspect, fieldStyle),
                                            )
                                            .toList(),
                                      ),
                              ),
                              _buildAuctionLotRow(S.of(context).fieldAuctionStatus, _buildAuctionStatus()),
                            ],
                          ),
                        ),
                      ),
                    ),
                    const Spacer(height: 5.0),
                    Card(
                      elevation: 1.0,
                      child: Padding(
                        padding: const EdgeInsets.all(12.0),
                        child: _buildItemList(context, headerStyle),
                      ),
                    ),
                    const Spacer(height: 5.0),
                    _buildRemarks(headerStyle),
                    const Spacer(height: 20.0),
                    if (relatedPageNum == 0)
                      SizedBox(height: MediaQuery.of(context).size.height / 2)
                    else
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 12.0),
                        child: _buildRelatedLotList(),
                      ),
                    Center(
                      child: Padding(
                        padding: const EdgeInsets.symmetric(vertical: 50.0),
                        child: noMoreRelatedLots
                            ? Text(
                                S.of(context).relatedLotsEmpty,
                                style: Theme.of(context).textTheme.bodyText2,
                              )
                            : LemorangeLoading(),
                      ),
                    ),
                  ],
                ),
              ),
      ),
    );
  }

  TableRow _buildAuctionLotRow(String fieldName, Widget childWidget) {
    return TableRow(
      children: <Widget>[
        Padding(
          padding: const EdgeInsets.only(top: 4.0, right: 8.0),
          child: Text(fieldName, style: Theme.of(context).textTheme.bodyText2),
        ),
        Padding(
          padding: const EdgeInsets.only(top: 4.0),
          child: childWidget,
        ),
      ],
    );
  }

  Widget _buildPdfSource() {
    return RichText(
      text: TextSpan(
        style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 14.0 * MediaQuery.of(context).textScaleFactor),
        children: <InlineSpan>[
          TextSpan(text: S.of(context).pdfSource),
          TextSpan(
            text: S.of(context).gldAuctionLists,
            style: const TextStyle(color: config.blue),
            semanticsLabel: '${S.of(context).semanticsOpen}${S.of(context).gldAuctionLists}',
            recognizer: TapGestureRecognizer()
              ..onTap = () {
                launchUrl(
                  Uri.parse(FlutterConfig.get('GLD_WEBSITE').toString().replaceAll('{lang}', S.of(context).gldWebsiteLang)),
                  mode: LaunchMode.externalApplication,
                );
              },
          ),
          const WidgetSpan(
            alignment: PlaceholderAlignment.top,
            child: OpenExternalIcon(size: 13.0),
          ),
        ],
      ),
    );
  }

  Widget _buildRemarks(TextStyle headerStyle) {
    return SizedBox(
      width: double.infinity,
      child: Card(
        elevation: 1.0,
        child: Padding(
          padding: const EdgeInsets.all(12.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: <Widget>[
              Text(S.of(context).fieldRemarks, style: headerStyle),
              if (_auctionLot.remarks.isNotEmpty)
                Text(
                  _auctionLot.remarks,
                  style: Theme.of(context).textTheme.bodyText1,
                ),
              if (_auctionLot.remarks.isNotEmpty) const Spacer(),
              Consumer<AppInfoProvider>(builder: (BuildContext context, AppInfoProvider appInfo, Widget? _) {
                final NoticeLink standardTandCLink = appInfo.getAuctionStandardTandCLink(S.of(context).auctionStandardTandC, S.of(context).gldWebsiteLang);
                return RichText(
                  text: TextSpan(
                    style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 14.0 * MediaQuery.of(context).textScaleFactor),
                    children: <InlineSpan>[
                      TextSpan(
                        text: S.of(context).licenseRemarks1,
                      ),
                      TextSpan(
                        text: standardTandCLink.title,
                        style: const TextStyle(color: config.blue),
                        semanticsLabel: '${S.of(context).semanticsOpenFile}${standardTandCLink.title}',
                        recognizer: TapGestureRecognizer()
                          ..onTap = () {
                            launchUrl(
                              Uri.parse(standardTandCLink.url),
                              mode: LaunchMode.externalApplication,
                            );
                          },
                      ),
                      const WidgetSpan(
                        alignment: PlaceholderAlignment.top,
                        child: OpenExternalIcon(size: 13.0),
                      ),
                      TextSpan(text: S.of(context).licenseRemarks2),
                    ],
                  ),
                );
              }),
              const Spacer(),
              RichText(
                text: TextSpan(
                  style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 14.0 * MediaQuery.of(context).textScaleFactor),
                  children: <InlineSpan>[
                    TextSpan(
                      text: S.of(context).viewNoticeToParticipants1,
                    ),
                    TextSpan(
                      text: S.of(context).noticeToParticipants,
                      style: const TextStyle(color: config.blue),
                      semanticsLabel: '${S.of(context).read}${S.of(context).noticeToParticipants}',
                      recognizer: TapGestureRecognizer()
                        ..onTap = () {
                          Navigator.pushNamed(context, 'noticeToParticipants');
                        },
                    ),
                    TextSpan(text: S.of(context).viewNoticeToParticipants2),
                  ],
                ),
              ),
              const Spacer(),
              _buildPdfSource(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildContactLocationItem(TextStyle fieldStyle) {
    // for testing
    // '項目1-2: 香港灣仔軍器廠街1號警察總部警政大樓26樓 項目3-5: 香港灣仔軍器廠街1號警察總部警政大樓西翼21樓 項目6: 香港灣仔軍器廠街1號警察總部警政大樓西翼9樓'
    // 'Item(s) 1-2: 26/F, Arsenal House, Police Headquarters, 1 Arsenal Street, Wan Chai, Hong Kong Item(s) 3-5: 21/F, Arsenal House West Wing, Police Headquarters, 1 Arsenal Street, Wan Chai, Hong KongItem(s) 6: 9/F, Arsenal House West Wing, Police Headquarters'
    final String itemSeparator = S.of(context).splitContactLocation;
    return Text(_auctionLot.contactLocation.trim().replaceAll(itemSeparator, '\n$itemSeparator').replaceFirst('\n', ''), style: fieldStyle);
  }

  Widget _buildContactPersonItem(TextStyle fieldStyle) {
    // for testing
    // '項目1-2: 黃女士 / 蘇先生 項目3-5: 方女士 / 吳先生 項目6: 何先生 / 尹小姐'
    // 'Item(s) 1-2: Ms WONG / Mr SO, Item(s) 3-5: Ms FONG / Mr WU, Mr HO / Miss WAN'
    final String itemSeparator = S.of(context).splitContactPerson;
    return Text(_auctionLot.contact.trim().replaceAll(itemSeparator, '\n$itemSeparator').replaceFirst('\n', ''), style: fieldStyle);
  }

  Widget _buildInspectionDateItem(BuildContext context, InspectionDate inspect, TextStyle fieldStyle) {
    final String content = S.of(context).lang == 'en'
        ? '${inspect.startTime} - ${inspect.endTime} ${utilities.formatDayOfWeek(inspect.dayOfWeek, S.of(context).lang)}'
        : '${utilities.formatDayOfWeek(inspect.dayOfWeek, S.of(context).lang)}  ${inspect.startTime} - ${inspect.endTime}';

    String moreInfoContent = '';
    if (_auctionLot.specialInspection) {
      final String moreInfoContent1 =
          S.of(context).insepctionArrangementDetails1(inspect.startTime, inspect.endTime, utilities.formatDayOfWeek(inspect.dayOfWeek, S.of(context).lang));
      final String moreInfoContent2 = S.of(context).insepctionArrangementDetails2(inspect.typhoonStartTime, inspect.typhoonEndTime);
      moreInfoContent = '$moreInfoContent1\n\n$moreInfoContent2';
    } else {
      moreInfoContent = '''
${S.of(context).insepctionArrangementDefaultDetails}\n
${S.of(context).insepctionArrangementDefaultDetailsA(inspect.startTime, inspect.endTime, utilities.formatDayOfWeek(inspect.dayOfWeek, S.of(context).lang))}\n
${S.of(context).insepctionArrangementDefaultDetailsB(utilities.formatDayOfWeek(inspect.dayOfWeek, S.of(context).lang), inspect.startTime, inspect.endTime)}\n
${S.of(context).insepctionArrangementDefaultDetailsC(inspect.startTime, inspect.endTime)}
      ''';
    }

    return Row(
      children: <Widget>[
        Text(content, style: fieldStyle),
        InfoButton(_auctionLot.specialInspection ? S.of(context).fieldSpecialInspectionArrangement : S.of(context).fieldInspectionArrangement, moreInfoContent),
      ],
    );
  }

  Widget _buildAuctionStatus() {
    if (_auctionLot.transactionStatus == TransactionStatus.NotSold) {
      return Text(
        S.of(context).notSold,
        style: Theme.of(context).textTheme.bodyText1!.copyWith(
              color: Colors.red[700],
            ),
      );
    }

    return Text(
      '\$${utilities.formatDigits(_auctionLot.transactionPrice.toInt())} (${S.of(context).sold})',
      style: Theme.of(context).textTheme.bodyText1!.copyWith(
            color: Colors.green[700],
          ),
    );
  }
}
