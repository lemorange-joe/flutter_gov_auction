import 'package:hive/hive.dart';

part 'auction_reminder.g.dart';

@HiveType(typeId: 2)
class AuctionReminder implements Comparable<AuctionReminder> {
  AuctionReminder(this.auctionId, this.remindTime, this.auctionStartTime);

  @HiveField(0)
  int auctionId; // as the unique ID

  @HiveField(1)
  DateTime remindTime;

  @HiveField(2)
  DateTime auctionStartTime;

  // TBC
  // String getLocation(String lang) {
  //   if (lang == 'tc') {
  //     return locationTc;
  //   }

  //   if (lang == 'sc') {
  //     return locationSc;
  //   }

  //   return locationEn;
  // }

  @override
  int compareTo(AuctionReminder otherReminder) {
    return otherReminder.remindTime.compareTo(remindTime);
  }

  AuctionReminder copyWith({
    int? auctionId,
    DateTime? remindTime,
    DateTime? auctionStartTime,
  }) {
    return AuctionReminder(
      auctionId ?? this.auctionId,
      remindTime ?? this.remindTime,
      auctionStartTime ?? this.auctionStartTime,
    );
  }
}
