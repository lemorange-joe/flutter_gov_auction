import 'package:intl/intl.dart';
import '../includes/enums.dart';

class Auction {
  Auction(this.id, this.auctionNum, this.startTime, this.location, this.auctionPdfUrl, this.resultPdfUrl, this.itemPdfList, this.remarks, this.lotList,
      this.status, this.lastUpdate);

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
      }).toList()..removeWhere((AuctionItemPdf a) => a.itemType == AuctionItemType.None),
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
  AuctionLot(this.itemType, this.gldFileRef, this.reference, this.itemList);

  final AuctionItemType itemType;
  final String gldFileRef;
  final String reference;
  final List<AuctionItem> itemList;
}

class AuctionItem {
  AuctionItem(this.description, this.quantity, this.unit);

  final String description;
  final double quantity;
  final String unit;
}

class AuctionItemPdf {
  AuctionItemPdf(this.itemType, this.pdfUrl);

  final AuctionItemType itemType;
  final String pdfUrl;
}
