// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'auction_reminder.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class AuctionReminderAdapter extends TypeAdapter<AuctionReminder> {
  @override
  final int typeId = 2;

  @override
  AuctionReminder read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return AuctionReminder(
      fields[0] as int,
      fields[1] as DateTime,
      fields[2] as int,
      fields[3] as DateTime,
      fields[4] as String,
      fields[5] as String,
      fields[6] as String,
      fields[7] as String,
      fields[8] as String,
      fields[9] as String,
    );
  }

  @override
  void write(BinaryWriter writer, AuctionReminder obj) {
    writer
      ..writeByte(10)
      ..writeByte(0)
      ..write(obj.lotId)
      ..writeByte(1)
      ..write(obj.remindTime)
      ..writeByte(2)
      ..write(obj.auctionId)
      ..writeByte(3)
      ..write(obj.auctionStartTime)
      ..writeByte(4)
      ..write(obj.lotNum)
      ..writeByte(5)
      ..write(obj.lotIcon)
      ..writeByte(6)
      ..write(obj.photoUrl)
      ..writeByte(7)
      ..write(obj.descriptionEn)
      ..writeByte(8)
      ..write(obj.descriptionTc)
      ..writeByte(9)
      ..write(obj.descriptionSc);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is AuctionReminderAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
