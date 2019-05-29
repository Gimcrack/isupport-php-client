<?php

namespace Ingenious\Isupport\Models;

use Ingenious\Isupport\Models\Scopes\ActiveScope;

class Customer extends BaseModel
{
    protected $connection = 'isupport';

    protected $table = 'CUSTOMERS';

    public $cacheKeyUsesMaxPrimaryKey = true;
    public $cacheKeyMaxPrimaryKeyTTL = 60 * 60 * 24;
    public $cacheKeyUsesModelCount = true;
    public $cacheKeyModelCountTTL = 60 * 60 * 24;

    protected $primaryKey = 'ID';

    protected $hidden = [
        "ID",
        "GUID",
        "LNAME",
        "FNAME",
        "EMAIL",
        "PHONE",
        "FAX",
        "CELLULAR",
        "TIME_ZONE_KEY",
        "LOGIN",
        "PASSWORD",
        "PENDING_DELETION",
        "SOURCE",
        "SYNC_KEY",
        "SYNC_TIME",
        "SECONDARY_LOGIN",
        "ID_AD_DEFINITION",
        "IS_VENDOR",
        "ID_LDAP_DEFINITION",
        "ID_CONFIGURATION_ITEM",
        "ID_USER_AVATAR_IMAGE",
        "ID_OPPORTUNITY_RULE_GROUP",
        "ID_WALL_POST_FOLLOW_PROFILE",
        "ID_DISCUSSION_FEED_FOLLOW_PROFILE",
        "DT_MODIFIED",
        "ID_MODIFIED_BY",
        "FOLLOW_DISCUSSION_POSTS",
        "UNSUBSCRIBE_STATUS",
        "UNSUBSCRIBED_DATE",
        "ID_UNSUBSCRIBED_SOURCE_CORRESPONDENCE",
        "ID_PURCHASE_RULE_GROUP",
        "REP_RESET_PASSWORD",
        "DT_PASSWORD_RESET",
        "PREVIOUS_PASSWORDS",
        "FORGOTTEN_PASSWORD_PIN",
        "FORGOTTEN_PASSWORD_PIN_TIMESTAMP",
        "DT_LAST_LOGIN",
        "LOCKOUT_FAILED_LOGIN_COUNT",
        "LOCKOUT_UNTIL",
        "LOCKOUT_TYPE",
        "LOCKOUT_EMAIL_UNLOCK_CODE",
        "ID_AD_SYNC_SETTING",
        "ID_LDAP_SYNC_SETTING",
        "EVAL_EMAIL",
        "EVAL_EMAIL_VERIFIED",
        "ID_COMPANY",
        "LOCATION",
        "DEPT",
        "ADDRESS1",
        "ADDRESS2",
        "ADDRESS3",
        "CITY",
        "STATE",
        "ZIP",
        "COUNTRY",
        "TITLE",
        "MANAGER",
        "COMMENTS",
        "IS_APPROVED",
        "CUSTOMER_NUMBER",
        "CAN_VIEW_COMPANY_INCIDENTS",
        "CAN_VIEW_DEPT_INCIDENTS",
        "CAN_VIEW_LOC_INCIDENTS",
        "CAN_VIEW_GROUP_INCIDENTS",
        "CAN_VIEW_OTN_INCIDENTS",
        "ID_PRIMARY_GROUP",
        "ID_APPROVAL_CYCLE",
        "ID_PURCHASING_APPROVAL_CYCLE",
        "CAN_SUBMIT_PURCHASE",
        "CAN_SELECT_VENDOR",
        "ID_COST_CENTER",
        "ID_JOB_FUNCTION",
        "CAN_EDIT_RATE",
        "CAN_VIEW_SERVICE_COST",
        "CAN_SEARCH_INCIDENT_ARCHIVE",
        "ADDITIONAL_EMAIL_ADDRESSES",
        "ID_RDB_DEFINITION",
        "ID_INCIDENT_RULE_GROUP",
        "ID_CHANGE_RULE_GROUP",
        "ID_CUSTOMER_APPROVER",
        "ID_REP_APPROVER",
        "ID_SOCIAL_CLIENT",
        "ID_SOURCE_SOCIAL_CLIENT",
        "ID_SMS_INFO",
        "ID_TWITTER_INFO",
        "NOTIFICATION_TYPES",
        "CAN_ADD_OTN",
        "EXCLUDE_FROM_SEARCH",
        "FOLLOWUP",
        "ID_REP_OWNER",
        "TWITTER_USERNAME",
        "DT_CREATED",
        "IS_POWER_USER",
        "FACEBOOK_USERNAME",
        "ID_AUTHOR",
    ];

    protected $appends = [
        'name',
        'department'
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

    /**
     * Get the name attribute
     * 
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->FNAME . ' ' . $this->LNAME;
    }

    /**
     * Get the department attribute
     * 
     * @return string
     */
    public function getDepartmentAttribute()
    {
        return $this->DEPT;
    }


}