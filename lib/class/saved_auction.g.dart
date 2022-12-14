// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'saved_auction.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class SavedAuctionAdapter extends TypeAdapter<SavedAuction> {
  @override
  final int typeId = 1;

  @override
  SavedAuction read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return SavedAuction(
      fields[1] as int,
      fields[2] as String,
      fields[3] as int,
      fields[4] as DateTime,
      fields[5] as String,
      fields[6] as String,
      fields[7] as String,
      fields[8] as String,
      fields[9] as String,
      fields[10] as String,
      fields[0] as DateTime?,
    );
  }

  @override
  void write(BinaryWriter writer, SavedAuction obj) {
    writer
      ..writeByte(11)
      ..writeByte(0)
      ..write(obj.savedDate)
      ..writeByte(1)
      ..write(obj.auctionId)
      ..writeByte(2)
      ..write(obj.auctionNum)
      ..writeByte(3)
      ..write(obj.lotId)
      ..writeByte(4)
      ..write(obj.auctionStartTime)
      ..writeByte(5)
      ..write(obj.lotNum)
      ..writeByte(6)
      ..write(obj.lotIcon)
      ..writeByte(7)
      ..write(obj.photoUrl)
      ..writeByte(8)
      ..write(obj.descriptionEn)
      ..writeByte(9)
      ..write(obj.descriptionTc)
      ..writeByte(10)
      ..write(obj.descriptionSc);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is SavedAuctionAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
