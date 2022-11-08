import 'package:hive/hive.dart';
import 'auction.dart';

part 'auction_reminder.g.dart';

@HiveType(typeId: 2)
class AuctionReminder implements Comparable<AuctionReminder> {
  AuctionReminder(this.lotId, this.remindTime, this.auctionId, this.auctionStartTime, this.lotNum, this.lotIcon, this.photoUrl, this.descriptionEn,
      this.descriptionTc, this.descriptionSc);

  factory AuctionReminder.fromAuctionLot(int auctionId, DateTime auctionStartTime, AuctionLot lot) {
    return AuctionReminder(
      lot.id,
      DateTime(1900),
      auctionId,
      auctionStartTime,
      lot.lotNum,
      lot.icon,
      lot.photoUrl,
      lot.descriptionEn,
      lot.descriptionTc,
      lot.descriptionSc,
    );
  }

  @HiveField(0)
  int lotId; // as the unique ID

  @HiveField(1)
  DateTime remindTime;

  @HiveField(2)
  int auctionId;

  @HiveField(3)
  DateTime auctionStartTime;

  @HiveField(4)
  String lotNum;

  @HiveField(5)
  String lotIcon;

  @HiveField(6)
  String photoUrl;

  @HiveField(7)
  String descriptionEn;

  @HiveField(8)
  String descriptionTc;

  @HiveField(9)
  String descriptionSc;

  String getDescription(String lang) {
    if (lang == 'tc') {
      return descriptionTc;
    }

    if (lang == 'sc') {
      return descriptionSc;
    }

    return descriptionEn;
  }

  @override
  int compareTo(AuctionReminder otherReminder) {
    return otherReminder.remindTime.compareTo(remindTime);
  }

  AuctionReminder copyWith({
    int? lotId,
    DateTime? remindTime,
    int? auctionId,
    DateTime? auctionStartTime,
    String? lotNum,
    String? lotIcon,
    String? photoUrl,
    String? descriptionEn,
    String? descriptionTc,
    String? descriptionSc,
  }) {
    return AuctionReminder(
      lotId ?? this.lotId,
      remindTime ?? this.remindTime,
      auctionId ?? this.auctionId,
      auctionStartTime ?? this.auctionStartTime,
      lotNum ?? this.lotNum,
      lotIcon ?? this.lotIcon,
      photoUrl ?? this.photoUrl,
      descriptionEn ?? this.descriptionEn,
      descriptionTc ?? this.descriptionTc,
      descriptionSc ?? this.descriptionSc,
    );
  }
}
