<?php

namespace Ingenious\Isupport\Models;

use Illuminate\Database\Eloquent\Builder;

class RepWorkHistory extends BaseModel
{
    const CREATED_AT = 'DT_CREATED';

    public $cacheKeyUsesMaxPrimaryKey = true;
    public $cacheKeyMaxPrimaryKeyTTL = 300;
    public $cacheKeyUsesModelCount = true;
    public $cacheKeyModelCountTTL = 300;
    
    protected $connection = 'isupport';

    protected $table = 'REP_WORK_HISTORY';

    protected $primaryKey = 'ID';

    protected $hidden = [
        "ID",        
        "ID_OWNER",  
        "DT_CREATED",
        "ENTRY",
        "INCIDENT_NUMBER",
        "IS_EDITABLE",
        "ID_OWNER_GROUP",
        "TIME_WORKED",
        "FLAGGED",   
        "ID_WORK_HISTORY",
        "DT_START", 
        "DT_STOP",
        "ID_WORK_HISTORY_TYPE"
    ];
    
    protected $casts = [
        'ID' => 'int',
        'ID_OWNER' => 'int',
        'TIME_WORKED' => 'int'
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
    public function scopeOfRep(Builder $query, $rep)
    {
        return $query->where('ID_OWNER',$rep);
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

    ///**
    // * Get the time_worked attribute
    // *
    // * @return int
    // */
    //public function getTimeWorkedAttribute()
    //{
    //    return $this->TIME_WORKED;
    //}
}