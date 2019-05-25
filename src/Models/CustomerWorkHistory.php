<?php

namespace Ingenious\Isupport\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CustomerWorkHistory extends Model
{
    protected $connection = 'isupport';

    protected $table = 'CUSTOMER_WORK_HISTORY';

    protected $primaryKey = 'ID';

    protected $hidden = [
        "ID",
        "ID_OWNER",
        "DT_CREATED",
        "ENTRY",
        "INCIDENT_NUMBER",
        "SYSTEM_CREATED",
        "ID_CUSTOMER_OWNER"
    ];
    
    protected $casts = [
        'ID' => 'int',
        'ID_OWNER' => 'int'
    ];
    
    protected $dates = [
        'DT_CREATED',  
    ];

    /**
     * Get correspondence of customers
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfCustomer(Builder $query)
    {
        return $query->whereNull('ID_OWNER');
    }

    /**
     * Get the incident
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class,"NUMBER","INCIDENT_NUMBER");
    }
}