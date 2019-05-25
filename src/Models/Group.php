<?php

namespace Ingenious\Isupport\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Ingenious\Isupport\Models\Scopes\ActiveScope;

class Group extends Model
{
    protected $connection = 'isupport';

    protected $table = 'GROUPS';

    protected $primaryKey = 'ID';

    protected $hidden = [
        "ID",
        "GROUP_NAME",
        "DESCRIPTION",
        "PENDING_DELETION",
        "GROUP_TYPE",
        "ID_LOCATION",
        "SOURCE",
        "ID_CONFIGURATION_ITEM",
        "ID_SOCIAL_CLIENT",
        "GUID",
        "ALLOWED_WIDGETS",
        "OVERRIDE_ALLOWED_WIDGETS",
        "CHAT_ENABLED",
        "CHAT_REP_SESSION_LIMIT",
        "CHAT_ID_INCIDENT_TEMPLATE",
        "ID_CHAT_ACCEPTED_CANNED_CHAT_RESPONCE",
        "CHAT_ENDED_MESSAGE",
        "GROUP_TAG",
        "ID_INCIDENT_LAYOUT",
        "ID_PROBLEM_LAYOUT",
        "ID_CHANGE_LAYOUT",
        "ALLOWED_GLOBAL_SEARCH_ENTITIES",
        "OVERRIDE_ALLOWED_GLOBAL_SEARCH_ENTITIES",
        "DEFAULT_INCIDENT_QUICK_ACCESS_ITEMS",
        "DEFAULT_CHANGE_QUICK_ACCESS_ITEMS",
        "DEFAULT_PROBLEM_QUICK_ACCESS_ITEMS",
        "DEFAULT_COMPANY_QUICK_ACCESS_ITEMS",
        "DEFAULT_CUSTOMER_QUICK_ACCESS_ITEMS",
        "DEFAULT_KBASE_QUICK_ACCESS_ITEMS",
        "DEFAULT_ASSET_QUICK_ACCESS_ITEMS",
        "DEFAULT_PURCHASE_QUICK_ACCESS_ITEMS",
        "CHAT_AUTO_CREATE_INCIDENT",
        "CHAT_REQUEST_FEEDBACK",
        "CHAT_REQUIRE_INCIDENT_ON_CLOSE",
        "LIMIT_ROUTE_TO_PRIMARY_MEMBERS",
    ];
    
    protected $casts = [
        'ID' => 'int',
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
        return $this->GROUP_NAME;
    }
}