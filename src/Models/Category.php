<?php

namespace Ingenious\Isupport\Models;

use Illuminate\Database\Eloquent\Model;
use Ingenious\Isupport\Models\Scopes\ActiveScope;

class Category extends Model
{
    protected $connection = 'isupport';

    protected $table = 'CATEGORIES';

    protected $primaryKey = 'ID';

    protected $hidden = [
        'ID',
        'GUID',
        'ID_PARENT',
        'ENTRY',
        'CAT_LEVEL',
        'CHILDREN',
        'SCRIPT',
        'ID_CUSTOM_FIELD_COLLECTION',
        'AVAILABLE_TO_CUSTOMERS',
        'PENDING_DELETION',
        'ID_APPROVAL_CYCLE',
        'ID_INCIDENT_RULE_GROUP',
        'ID_CHANGE_RULE_GROUP',
        'ID_IMAGE',
        'ID_PROBLEM_RULE_GROUP',
        'ID_KNOWLEDGE_RULE_GROUP',
        'ID_INCIDENT_LAYOUT',
        'ID_PROBLEM_LAYOUT',
        'ID_CHANGE_LAYOUT',
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

    public function parent()
    {
        return $this->belongsTo(Category::class,"ID_PARENT","ID");
    }

    public function getNameAttribute()
    {
        $parents = [];

        if ( $this->parent ) {
            $parents[] = $this->parent->name;
        }

        $parents[] = $this->ENTRY;

        return implode(" - ", $parents) ;
    }

}