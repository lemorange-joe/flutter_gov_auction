import 'package:intl/intl.dart';
// import 'package:logger/logger.dart';
import '../includes/enums.dart';
import '../includes/revelation.dart' as revelation;

class Auction {
  Auction(this.id, this.auctionNum, this.startTime, this.collectionDeadline, this.location, this.auctionPdfUrl, this.resultPdfUrl, this.itemPdfList,
      this.remarks, this.lotCount, this.transactionTotal, this.lotList, this.status, this.lastUpdate);

  factory Auction.empty() {
    return Auction(0, '', DateTime(1900), DateTime(1900), '', '', '', <AuctionItemPdf>[], '', 0, 0, <AuctionLot>[], AuctionStatus.None, DateTime(1900));
  }

  factory Auction.fromJson(Map<String, dynamic> json) {
    final List<dynamic> ipl = json['ipl'] as List<dynamic>;

    return Auction(
      json['id'] as int,
      json['n'] as String,
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['st'] as String),
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['cd'] as String),
      json['l'] as String,
      json['ap'] as String,
      json['rp'] as String,
      ipl.map((dynamic ip) {
        final Map<String, String> itemPdf = ip as Map<String, String>;

        if (itemPdf['t'] != null && itemPdf['url'] != null) {
          return AuctionItemPdf(itemPdf['t']!, itemPdf['url']!);
        } else {
          return AuctionItemPdf('', '');
        }
      }).toList()
        ..removeWhere((AuctionItemPdf a) => a.itemType == ''),
      json['r'] as String,
      revelation.revealAuctionLotCount(json['lc'] as int, json['id'] as int),
      revelation.revealAuctionTransactionTotal(json['tt'] as int, json['id'] as int),
      <AuctionLot>[],
      getAuctionStatus(json['as'] as String),
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['lu'] as String),
    );
  }

  final int id;
  final String auctionNum;
  final DateTime startTime;
  final DateTime collectionDeadline;
  final String location;
  final String auctionPdfUrl;
  final String resultPdfUrl;
  final String remarks;
  final int lotCount;
  final int transactionTotal;
  final AuctionStatus status;
  final DateTime lastUpdate;
  final List<AuctionItemPdf> itemPdfList;
  final List<AuctionLot> lotList;
}

class AuctionLot {
  AuctionLot(
      this.id,
      this.auctionDate,
      this.itemType,
      this.lotNum,
      this.gldFileRef,
      this.reference,
      this.department,
      this.contact,
      this.contactNumber,
      this.contactLocation,
      this.remarks,
      this.itemCondition,
      this.description,
      this.descriptionEn,
      this.descriptionTc,
      this.descriptionSc,
      this.featured,
      this.icon,
      this.photoUrl,
      this.photoReal,
      this.itemList,
      this.specialInspection,
      this.inspectionDateList,
      this.transactionCurrency,
      this.transactionPrice,
      this.transactionStatus,
      this.lastUpdate);

  factory AuctionLot.fromJson(Map<String, dynamic> json, String lang) {
    final List<dynamic> jsonItemList = json['il'] as List<dynamic>;
    final List<AuctionItem> itemList = <AuctionItem>[];
    final List<dynamic> jsonInspectionDateList = json['idl'] as List<dynamic>;
    final List<InspectionDate> inspectionDateList = <InspectionDate>[];

    for (final dynamic jsonItem in jsonItemList) {
      itemList.add(AuctionItem.fromJson(jsonItem as Map<String, dynamic>));
    }

    for (final dynamic jsonItem in jsonInspectionDateList) {
      inspectionDateList.add(InspectionDate.fromJson(jsonItem as Map<String, dynamic>));
    }

    return AuctionLot(
      json['id'] as int,
      json['ad'] == null ? DateTime(1900) : DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['ad'] as String),
      json['t'] as String,
      json['ln'] as String,
      json['gr'] as String,
      json['rf'] as String,
      json['dp'] as String,
      json['co'] as String,
      json['cn'] as String,
      json['cl'] as String,
      json['r'] as String,
      json['ic'] as String,
      lang == 'tc' ? json['dtc'] as String : (lang == 'sc' ? json['dsc'] as String : json['den'] as String),
      json['den'] as String,
      json['dtc'] as String,
      json['dsc'] as String,
      json['f'] as int == 1,
      json['i'] as String,
      json['pu'] as String,
      json['pr'] as int == 1,
      itemList,
      json['si'] as int == 1,
      inspectionDateList,
      json['tc'] as String,
      jsonToDouble(json['tp']),
      json['ts'] as String,
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['lu'] as String),
    );
  }

  factory AuctionLot.empty() {
    return AuctionLot(0, DateTime(1900), '', '', '', '', '', '', '', '', '', '', '', '', '', '', false, '', '', false, <AuctionItem>[], false, <InspectionDate>[], '', 0.0, '',
        DateTime(1900));
  }

  final int id;
  final DateTime auctionDate;
  final String itemType;
  final String lotNum;
  final String gldFileRef;
  final String reference;
  final String department;
  final String contact;
  final String contactNumber;
  final String contactLocation;
  final String remarks;
  final String itemCondition;
  final String description;
  final String descriptionEn;
  final String descriptionTc;
  final String descriptionSc;
  final bool featured;
  final String icon;
  final String photoUrl;
  final bool photoReal;
  final List<AuctionItem> itemList;
  final bool specialInspection;
  final List<InspectionDate> inspectionDateList;
  final String transactionCurrency;
  final double transactionPrice;
  final String transactionStatus;
  final DateTime lastUpdate;

  // use the description field now
  // String get title {
  //   if (itemList.isEmpty) {
  //     return lotNum;
  //   }

  //   return itemList.map((AuctionItem auctionItem) => auctionItem.description).toList().join(', ');
  // }

  String get itemDescriptionList {
    if (itemList.isEmpty) {
      return lotNum;
    }

    return itemList.map((AuctionItem auctionItem) => '${auctionItem.description} ${auctionItem.quantity} ${auctionItem.unit}').toList().join(', ');
  }
}

class AuctionItem {
  AuctionItem(this.id, this.icon, this.description, this.quantity, this.unit);

  factory AuctionItem.fromJson(Map<String, dynamic> json) {
    return AuctionItem(
      json['id'] as int,
      json['i'] as String,
      json['d'] as String,
      json['q'] as String,
      json['u'] as String,
    );
  }

  final int id;
  final String icon;
  final String description;
  final String quantity;
  final String unit;
}

class AuctionItemPdf {
  AuctionItemPdf(this.itemType, this.pdfUrl);

  final String itemType;
  final String pdfUrl;
}

double jsonToDouble(dynamic val) {
  if (val.runtimeType == int) {
    return (val as int).toDouble();
  }
  return val as double;
}

class RelatedAuctionLot {
  RelatedAuctionLot(
      this.auctionId,
      this.auctionNum,
      this.startTime,
      this.auctionStatus,
      this.lotId,
      this.lotNum,
      this.itemType,
      this.description,
      this.featured,
      this.icon,
      this.photoUrl,
      this.photoReal,
      this.photoAuthor,
      this.photoAuthorUrl,
      this.transactionCurrency,
      this.transactionPrice,
      this.transactionStatus);

  factory RelatedAuctionLot.fromJson(Map<String, dynamic> json) {
    return RelatedAuctionLot(
      json['aid'] as int,
      json['an'] as String,
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['st'] as String),
      getAuctionStatus(json['as'] as String),
      json['lid'] as int,
      json['ln'] as String,
      json['t'] as String,
      json['d'] as String,
      json['f'] as int == 1,
      json['i'] as String,
      json['pu'] as String,
      json['pr'] as int == 1,
      json['pa'] as String,
      json['pau'] as String,
      json['tc'] as String,
      jsonToDouble(json['tp']),
      json['ts'] as String,
    );
  }

  final int auctionId;
  final String auctionNum;
  final DateTime startTime;
  final AuctionStatus auctionStatus;
  final int lotId;
  final String lotNum;
  final String itemType;
  final String description;
  final bool featured;
  final String icon;

  final String photoUrl;
  final bool photoReal;
  final String photoAuthor;
  final String photoAuthorUrl;
  final String transactionCurrency;
  final double transactionPrice;
  final String transactionStatus;
}

class InspectionDate {
  InspectionDate(this.dayOfWeek, this.startTime, this.endTime, this.typhoonStartTime, this.typhoonEndTime);

  factory InspectionDate.fromJson(Map<String, dynamic> json) {
    return InspectionDate(
      json['dow'] as int,
      json['st'] as String,
      json['et'] as String,
      json['tst'] as String,
      json['tet'] as String, 
    );
  }

  final int dayOfWeek;
  final String startTime;
  final String endTime;
  final String typhoonStartTime;
  final String typhoonEndTime;
}
