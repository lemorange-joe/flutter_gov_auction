<?php
// counterpart with lib/includes/revelation.dart
class Obfuscation {
    static function changeAuctionLotCount($count, $auctionId) {
        return $count * $auctionId - $auctionId * $auctionId;
    }

    static function changeAuctionTransactionTotal($total, $auctionId) {
        return $total * ($auctionId - 5) + $auctionId * 50000;
    }
}
?>