enum AuctionItemType {
  None,
  ConfiscatedGoods, // C: 充公物品
  UnclaimedProperties, // UP: 無人認領物品
  UnserviceableStores, // M: 廢棄物品及剩餘物品
  SurplusServiceableStores, // MS: 仍可使用之廢棄物品及剩餘物品
}

String getAuctionItemTypeCode(AuctionItemType itemType) {
  switch (itemType) {
    case AuctionItemType.None:
      return '';
    case AuctionItemType.ConfiscatedGoods:
      return 'C';
    case AuctionItemType.UnclaimedProperties:
      return 'UP';
    case AuctionItemType.UnserviceableStores:
      return 'M';
    case AuctionItemType.SurplusServiceableStores:
      return 'MS';
  }
}

AuctionItemType getAuctionItemType(String code) {
  switch (code) {
    case 'C':
      return AuctionItemType.ConfiscatedGoods;
    case 'UP':
      return AuctionItemType.UnclaimedProperties;
    case 'M':
      return AuctionItemType.UnserviceableStores;
    case 'MS':
      return AuctionItemType.SurplusServiceableStores;
  }

  return AuctionItemType.None;
}

enum AuctionStatus {
  None,
  Pending,
  Confirmed,
  Cancelled,
  Finished,
}

String getAuctionStatusCode(AuctionStatus auctionStatus) {
  switch (auctionStatus) {
    case AuctionStatus.None:
      return '';
    case AuctionStatus.Pending:
      return 'P';
    case AuctionStatus.Confirmed:
      return 'C';
    case AuctionStatus.Cancelled:
      return 'X';
    case AuctionStatus.Finished:
      return 'F';
  }
}

AuctionStatus getAuctionStatus(String code) {
  switch (code) {
    case 'P':
      return AuctionStatus.Pending;
    case 'C':
      return AuctionStatus.Confirmed;
    case 'X':
      return AuctionStatus.Cancelled;
    case 'F':
      return AuctionStatus.Finished;
  }

  return AuctionStatus.None;
}
