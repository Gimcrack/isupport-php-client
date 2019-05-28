<?php

namespace Ingenious\Isupport\Models;

use Ingenious\Isupport\Models\Scopes\ActiveScope;

class Status extends BaseModel
{
    public const OPEN = 1;
    public const CLOSED = 2;
    public const PENDING = 3;
    public const REOPENED = 4;
    public const SCHEDULED = 5;
    public const WAITING_FOR_CUSTOMER = 7;
    public const WAITING_FOR_VENDOR = 8;
    public const AWAITING_ARRIVAL = 9;
    public const AWAITING_PURCHASE = 10;
    public const IMAGING_COMPUTER = 11;
    public const READY_FOR_PLACEMENT = 12;
    public const WAITING_FOR_APPROVAL = 13;
    public const APPROVED = 14;
    public const IN_PROGRESS = 17;
    public const AWAITING_QUOTE = 18;
    public const AWAITING_PAYMENT = 19;
    public const AWAITING_INVOICE = 20;
    public const READY_FOR_PICKUP = 21;
    public const WAITING_LEGAL_REVIEW = 22;
    public const AWAITING_PAYMENT_AP = 23;
    public const AWAITING_BID_RESULT = 24;
    public const FOLLOW_UP = 25;
    public const IN_REVIEW = 26;
    public const REQUIRES_UPGRADE = 27;

    protected $connection = 'isupport';

    protected $table = 'INCIDENT_STATUSES';

    protected $primaryKey = 'ID';

    protected $hidden = [
        "ID",
        "POSITION",
        "LABEL",
        "LABEL_ON_EUD",
        "TYPE",
        "PENDING_DELETION",
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ActiveScope);
    }

    public function getNameAttribute()
    {
        return $this->LABEL;
    }

}