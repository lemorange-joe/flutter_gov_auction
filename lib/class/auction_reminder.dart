import 'package:hive/hive.dart';

part 'auction_reminder.g.dart';

@HiveType(typeId: 2)
class AuctionReminder implements Comparable<AuctionReminder> {
  AuctionReminder(this.lotId, this.remindTime, this.auctionId, this.auctionDate, this.lotNum, this.lotIcon, this.photoUrl, this.descriptionEn, this.descriptionTc, this.descriptionSc);

  @HiveField(0)
  int lotId;  // as the unique ID

  @HiveField(1)
  DateTime remindTime;

  @HiveField(2)
  int auctionId;

  @HiveField(3)
  DateTime auctionDate;

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
}
