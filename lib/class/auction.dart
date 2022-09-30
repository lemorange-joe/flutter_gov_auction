import 'package:intl/intl.dart';
// import 'package:logger/logger.dart';
import '../includes/enums.dart';

class Auction {
  Auction(this.id, this.auctionNum, this.startTime, this.location, this.auctionPdfUrl, this.resultPdfUrl, this.itemPdfList, this.remarks, this.lotList,
      this.status, this.lastUpdate);

  factory Auction.empty() {
    return Auction(0, '', DateTime(1900), '', '', '', <AuctionItemPdf>[], '', <AuctionLot>[], AuctionStatus.None, DateTime(1900));
  }

  factory Auction.fromJson(Map<String, dynamic> json) {
    final List<dynamic> ipl = json['ipl'] as List<dynamic>;

    return Auction(
      json['id'] as int,
      json['n'] as String,
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['st'] as String),
      json['l'] as String,
      json['ap'] as String,
      json['rp'] as String,
      ipl.map((dynamic ip) {
        final Map<String, String> itemPdf = ip as Map<String, String>;

        if (itemPdf['t'] != null && itemPdf['url'] != null) {
          return AuctionItemPdf(getAuctionItemType(itemPdf['t']!), itemPdf['url']!);
        } else {
          return AuctionItemPdf(AuctionItemType.None, '');
        }
      }).toList()
        ..removeWhere((AuctionItemPdf a) => a.itemType == AuctionItemType.None),
      json['r'] as String,
      <AuctionLot>[],
      getAuctionStatus(json['as'] as String),
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['lu'] as String),
    );
  }

  final int id;
  final String auctionNum;
  final DateTime startTime;
  final String location;
  final String auctionPdfUrl;
  final String resultPdfUrl;
  final String remarks;
  final AuctionStatus status;
  final DateTime lastUpdate;
  final List<AuctionItemPdf> itemPdfList;
  final List<AuctionLot> lotList;
}

class AuctionLot {
  AuctionLot(
      this.id,
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
      this.featured,
      this.icon,
      this.photoUrl,
      this.photoReal,
      this.itemList,
      this.transactionCurrency,
      this.transactionPrice,
      this.transactionStatus,
      this.lastUpdate);

  factory AuctionLot.fromJson(Map<String, dynamic> json) {
    final List<dynamic> jsonItemList = json['il'] as List<dynamic>;
    final List<AuctionItem> itemList = <AuctionItem>[];

    for (final dynamic jsonItem in jsonItemList) {
      itemList.add(AuctionItem.fromjson(jsonItem as Map<String, dynamic>));
    }

    return AuctionLot(
      json['id'] as int,
      getAuctionItemType(json['t'] as String),
      json['ln'] as String,
      json['gr'] as String,
      json['rf'] as String,
      json['dp'] as String,
      json['co'] as String,
      json['cn'] as String,
      json['cl'] as String,
      json['r'] as String,
      json['ic'] as String,
      json['f'] as int == 1,
      json['i'] as String,
      json['pu'] as String,
      json['pr'] as int == 1,
      itemList,
      json['tc'] as String,
      jsonToDouble(json['tp']),
      json['ts'] as String,
      DateFormat('yyyy-MM-dd HH:mm:ss').parse(json['lu'] as String),
    );
  }

  final int id;
  final AuctionItemType itemType;
  final String lotNum;
  final String gldFileRef;
  final String reference;
  final String department;
  final String contact;
  final String contactNumber;
  final String contactLocation;
  final String remarks;
  final String itemCondition;
  final bool featured;
  final String icon;
  final String photoUrl;
  final bool photoReal;
  final List<AuctionItem> itemList;
  final String transactionCurrency;
  final double transactionPrice;
  final String transactionStatus;
  final DateTime lastUpdate;

  String get title {
    if (itemList.isEmpty) {
      return lotNum;
    }

    return itemList.map((AuctionItem auctionItem) => auctionItem.description).toList().join(', ');
  }
}

class AuctionItem {
  AuctionItem(this.id, this.icon, this.description, this.quantity, this.unit);

  factory AuctionItem.fromjson(Map<String, dynamic> json) {
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

  final AuctionItemType itemType;
  final String pdfUrl;
}

double jsonToDouble(dynamic val) {
  if (val.runtimeType == int) {
    return (val as int).toDouble();
  }
  return val as double;
}
