<?php

namespace Ingenious\Isupport\Models;

use function array_search;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Ingenious\Isupport\Models\Scopes\ActiveScope;
use function snake_case;

class CustomFieldDef extends BaseModel
{
    protected $connection = 'isupport';

    protected $table = 'CUSTOM_FIELD_DEFS';

    protected $primaryKey = 'ID';

    protected $hidden = [
        "ID",
        "ID_COLLECTION",
        "TYPE",
        "LABEL",
        "REQUIRED",
        "POS",
        "OPTIONS",
        "PENDING_DELETION",
        "DEFAULT_VALUE",
        "REQUIRED_ON_CLOSE",
        "AVAILABLE_TO_CUSTOMERS",
        "ID_CUSTOM_FIELD_DATA_SOURCE",
        "MAX_COLUMNS",
        "TOOLTIP",
        "AVAILABLE_TO_REPS",
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
        return snake_case( preg_replace("/[^a-zA-Z\s]/i","",strtolower(trim($this->LABEL,'*')) ));
        //return $this->types[$this->ID_FIELD] ?? $this->ID_FIELDS;
    }

}