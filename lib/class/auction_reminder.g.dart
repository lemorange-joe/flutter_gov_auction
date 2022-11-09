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
      fields[2] as DateTime,
    );
  }

  @override
  void write(BinaryWriter writer, AuctionReminder obj) {
    writer
      ..writeByte(3)
      ..writeByte(0)
      ..write(obj.auctionId)
      ..writeByte(1)
      ..write(obj.remindTime)
      ..writeByte(2)
      ..write(obj.auctionStartTime);
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
