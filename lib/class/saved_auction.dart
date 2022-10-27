import 'package:hive/hive.dart';

part 'saved_auction.g.dart';

@HiveType(typeId: 1)
class SavedAuction implements Comparable<SavedAuction> {
  SavedAuction(this.auctionId, this.auctionDate, this.lotNum, this.photoUrl, this.descriptionEn, this.descriptionTc, this.descriptionSc, [this.savedDate]);

  factory SavedAuction.empty() {
    return SavedAuction(0, DateTime(1900), '', '', '', '', '', DateTime(1900));
  }

  @HiveField(0)
  DateTime? savedDate;

  @HiveField(1)
  int auctionId;

  @HiveField(2)
  DateTime auctionDate;

  @HiveField(3)
  String lotNum;

  @HiveField(4)
  String photoUrl;

  @HiveField(5)
  String descriptionEn;

  @HiveField(6)
  String descriptionTc;

  @HiveField(7)
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
    if (auctionDate == otherAuction.auctionDate) {
      return lotNum.compareTo(otherAuction.lotNum);
    }

    return otherAuction.auctionDate.compareTo(auctionDate);
  }
}
