import 'package:hive/hive.dart';

part 'saved_auction.g.dart';

@HiveType(typeId: 1)
class SavedAuction implements Comparable<SavedAuction> {
  SavedAuction(this.auctionId, this.auctionNum, this.lotId, this.auctionStartTime, this.lotNum, this.lotIcon, this.photoUrl, this.descriptionEn, this.descriptionTc, this.descriptionSc, [this.savedDate]);

  factory SavedAuction.empty() {
    return SavedAuction(0, '', 0, DateTime(1900), '', 'fontawesome.box', '', '', '', '', DateTime(1900));
  }

  @HiveField(0)
  DateTime? savedDate;

  @HiveField(1)
  int auctionId;

  @HiveField(2)
  String auctionNum;

  @HiveField(3)
  int lotId;

  @HiveField(4)
  DateTime auctionStartTime;

  @HiveField(5)
  String lotNum;

  @HiveField(6)
  String lotIcon;

  @HiveField(7)
  String photoUrl;

  @HiveField(8)
  String descriptionEn;

  @HiveField(9)
  String descriptionTc;

  @HiveField(10)
  String descriptionSc;

  String get hiveKey {
    return '${auctionId}_$lotNum';
  }

  String getDescription(String lang) {
    if (lang == 'tc') {
      return descriptionTc;
    }

    if (lang == 'sc') {
      return descriptionSc;
    }

    return descriptionEn;
  }

  // TBC how to use comparator, e.g.
  // Comparator<ComparatorEmployee> employeeSalary = (x, y) => x.salary.compareTo(y.salary);
  // employeesList.sort(employeeSalary);
  static Comparator<SavedAuction> savedDateComparator = (SavedAuction a, SavedAuction b) {
    final DateTime thisSavedDate = a.savedDate ?? DateTime(1900);
    final DateTime otherSavedDate = b.savedDate ?? DateTime(1900);

    return otherSavedDate.compareTo(thisSavedDate); // sort newest first
  };

  @override
  int compareTo(SavedAuction otherAuction) {
    if (auctionStartTime == otherAuction.auctionStartTime) {
      return lotNum.compareTo(otherAuction.lotNum);
    }

    return otherAuction.auctionStartTime.compareTo(auctionStartTime);
  }
}
