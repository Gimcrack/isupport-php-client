<?php

namespace Ingenious\Isupport\Models;


class IsupportClosedTicket extends BaseModel
{
    protected $connection = 'isupport';

    protected $table = 'vw_MSB_TicketArchive';

    protected $dates = [
        'created_date',
        'modified_date',
        'closed_date',
        'first_response_date',
        'last_response_date',
        'last_customer_response_date'
    ];

    protected $casts = [
        'id' => 'int',
        'customer_responded_last' => 'bool',
        'days_open' => 'int',
        'days_since_last_response' => 'int',
        'days_to_first_response' => 'int',
        'time_worked' => 'int'
    ];

}