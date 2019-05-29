<?php

namespace Ingenious\Isupport\Models;

use Illuminate\Database\Eloquent\Builder;

class IncidentCorrespondenceHistory extends BaseModel
{
    const CREATED_AT = 'DT_CREATED';

    public $cacheKeyUsesMaxPrimaryKey = true;
    public $cacheKeyMaxPrimaryKeyTTL = 300;
    public $cacheKeyUsesModelCount = true;
    public $cacheKeyModelCountTTL = 300;
    
    protected $connection = 'isupport';

    protected $table = 'INCIDENT_CORRESPONDENCE_HISTORY';

    protected $primaryKey = 'ID';

    protected $hidden = [
        "ID",
        "ID_OWNER",
        "DT_CREATED",
        "ENTRY",
        "INCIDENT_NUMBER",
        "FLAGGED",
    ];
    
    protected $dates = [
        'DT_CREATED'  
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
}