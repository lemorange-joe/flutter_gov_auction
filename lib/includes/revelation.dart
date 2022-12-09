// counterpart with php/class/obfuscation.php
int revealAuctionLotCount(int obfuscatedCount, int auctionId) {
  return ((obfuscatedCount + auctionId * auctionId) / auctionId).floor();
}

int revealAuctionTransactionTotal(int obfuscatedTotal, int auctionId) {
  return ((obfuscatedTotal - auctionId * 50000) / (auctionId - 5)).floor();
}
