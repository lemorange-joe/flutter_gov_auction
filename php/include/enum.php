<?php
abstract class AuctionStatus
{
    const Pending = "P";
    const Confirmed = "C";
    const Cancelled = "X";
    const Finished = "F";
}

abstract class FetchStatus
{
    const Success = "S";
    const Failed = "F";
}

abstract class ItemType
{
    const ConfiscatedGoods = "C";           // 充公物品
    const UnclaimedProperties = "UP";       // 無人認領物品
    const UnserviceableStores = "M";        // 廢棄物品及剩餘物品
    const SurplusServiceableStores = "MS";  // 仍可使用之廢棄物品及剩餘物品
}

abstract class PushStatus
{
    const Pending = "P";
    const Sending = "I";
    const Sent = "S";
    const Failed = "F";
    const Cancelled = "X";
}

abstract class Status
{
    const Active = "A";
    const Inactive = "I";
}

abstract class TransactionStatus
{
    const Sold = "S";
    const NotSold = "N";
}

?>