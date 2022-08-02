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
    const ConfiscatedGoods = "C";
    const UnclaimedProperties = "UP";
    const UnserviceableStores = "M";
    const SurplusServiceableStores = "MS";
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