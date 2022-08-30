import '../include/enums.dart';

class Auction {
  Auction (this.id, this.auctionNum, this.startTime, this.lotList);

  final int id;
  final String auctionNum;
  final DateTime startTime;
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
