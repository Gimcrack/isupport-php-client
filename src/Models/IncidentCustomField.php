<?php

namespace Ingenious\Isupport\Models;

use function array_search;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class IncidentCustomField extends Model
{
    protected $types = [
        0 => 'n/a',
        1 => 'subject',
        2 => 'urgency',
        3 => 'impact',
        4 => 'work_stoppage'
    ];

    protected $connection = 'isupport';

    protected $table = 'INCIDENT_CUSTOM_FIELDS';

    protected $primaryKey = 'ID';

    protected $hidden = [
        "ID",
        "ID_FIELD",
        "DATA",
        "ID_INCIDENT",
        "INCIDENT_NUMBER",
    ];

    public function definition()
    {
        return $this->hasOne(CustomFieldDef::class,"ID","ID_FIELD");
    }

    public function getNameAttribute()
    {
        return $this->types[$this->ID_FIELD] ?? $this->definition->name;
    }

    public function scopeOfType(Builder $query, $type)
    {
        $index = array_search($type, $this->types);
        return $query->where("ID_FIELD", $index);
    }

}