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
