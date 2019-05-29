<?php

namespace Ingenious\Isupport\Models;

use Ingenious\Isupport\Models\Scopes\ActiveScope;

class Rep extends BaseModel
{
    public $cacheKeyUsesMaxPrimaryKey = true;
    public $cacheKeyMaxPrimaryKeyTTL = 60 * 60 * 24;
    public $cacheKeyUsesModelCount = true;
    public $cacheKeyModelCountTTL = 60 * 60 * 24;

    protected $connection = 'isupport';

    protected $table = 'REPS';

    protected $primaryKey = 'ID';

    protected $hidden = [
        "ID",
        "GUID",
        "ID_LOCATION",
        "FNAME",
        "LNAME",
        "PHONE",
        "FAX",
        "PAGER",
        "CELLULAR",
        "EMAIL",
        "IM",
        "ID_MGR1",
        "ID_MGR2",
        "IS_ADMIN",
        "IS_AVAILABLE",
        "IS_KB_ADMIN",
        "ALLOW_INC_DATA_OVERRIDE",
        "LOGIN",
        "PASSWORD",
        "ADMIN_RESET_PASSWORD",
        "DT_PASSWORD_RESET",
        "PREVIOUS_PASSWORDS",
        "FORGOTTEN_PASSWORD_PIN",
        "FORGOTTEN_PASSWORD_PIN_TIMESTAMP",
        "HOME_VIEW",
        "ID_DEFAULT_GROUP",
        "VIEW_PAGE_SIZE",
        "PENDING_DELETION",
        "TIME_ZONE_KEY",
        "SIGNATURE_BLOCK",
        "SHOW_CONFIGURATION_WIZARD",
        "ALLOW_APPROVER_OVERRIDE",
        "ENABLE_TAB",
        "IS_VENDOR",
        "ALLOW_MOBILE_ACCESS",
        "ENABLE_MOBILE_STYLES",
        "MOBILE_CUSTOM_FIELD_COUNT",
        "ID_CONFIGURATION_ITEM",
        "TOOLBAR_MODE",
        "DESKTOP_QUICK_ACCESS_SETTINGS",
        "DESKTOP_QUICK_ACCESS_LOCATION",
        "ID_USER_AVATAR_IMAGE",
        "IS_MAINTENANCE_ADMIN",
        "IS_SOCIAL_CLIENT_WALL_ADMIN",
        "CONFIG_FIRST_VIEW",
        "DESKTOP_HELP_OVERLAY_SHOWN",
        "OUTLOOK_CALENDAR_INTEGRATION_EXCHANGE_USERNAME",
        "OUTLOOK_CALENDAR_INTEGRATION_EXCHANGE_PASSWORD",
        "WORK_DAY_TIME_START",
        "WORK_DAY_TIME_END",
        "GOOGLE_CALENDAR_INTEGRATION_EMAIL",
        "GOOGLE_CALENDAR_INTEGRATION_OAUTH_REFRESH_TOKEN",
        "ID_LAST_VISITED_DASHBOARD",
        "IS_BIG_BROTHER_ADMIN",
        "ID_OPPORTUNITY_RULE_GROUP",
        "INCIDENT_QUICK_ACCESS_ITEMS",
        "CUSTOMER_QUICK_ACCESS_ITEMS",
        "CHANGE_QUICK_ACCESS_ITEMS",
        "PROBLEM_QUICK_ACCESS_ITEMS",
        "KNOWLEDGE_QUICK_ACCESS_ITEMS",
        "COMPANY_QUICK_ACCESS_ITEMS",
        "ASSET_QUICK_ACCESS_ITEMS",
        "PURCHASE_QUICK_ACCESS_ITEMS",
        "DISPLAY_IN_AWARENESS",
        "AVAILABLE_FOR_REP_CHAT",
        "ALLOW_REP_CHAT_BROADCAST",
        "ID_WALL_POST_FOLLOW_PROFILE",
        "ID_DISCUSSION_FEED_FOLLOW_PROFILE",
        "MOBILE_HTML5_ONLY",
        "ALLOW_MOBILE_ACCESS_INSIDE_FIREWALL",
        "INCIDENT_VIEW_QUICK_ACCESS_ITEMS",
        "CHANGE_VIEW_QUICK_ACCESS_ITEMS",
        "PROBLEM_VIEW_QUICK_ACCESS_ITEMS",
        "ASSET_VIEW_QUICK_ACCESS_ITEMS",
        "CUSTOMER_VIEW_QUICK_ACCESS_ITEMS",
        "COMPANY_VIEW_QUICK_ACCESS_ITEMS",
        "SURVEY_REQUEST_VIEW_QUICK_ACCESS_ITEMS",
        "SURVEY_RESPONSE_VIEW_QUICK_ACCESS_ITEMS",
        "KNOWLEDGE_VIEW_QUICK_ACCESS_ITEMS",
        "ARCHIVED_CORRESPONDENCE_VIEW_QUICK_ACCESS_ITEMS",
        "ARCHIVED_INCIDENT_VIEW_QUICK_ACCESS_ITEMS",
        "ARCHIVED_CHANGE_VIEW_QUICK_ACCESS_ITEMS",
        "ARCHIVED_PROBLEM_VIEW_QUICK_ACCESS_ITEMS",
        "ARCHIVED_PURCHASE_VIEW_QUICK_ACCESS_ITEMS",
        "SOFTWARE_LICENSE_VIEW_QUICK_ACCESS_ITEMS",
        "HEADLINE_VIEW_QUICK_ACCESS_ITEMS",
        "FAQ_VIEW_QUICK_ACCESS_ITEMS",
        "PURCHASE_VIEW_QUICK_ACCESS_ITEMS",
        "PRODUCT_VIEW_QUICK_ACCESS_ITEMS",
        "CORRESPONDENCE_VIEW_QUICK_ACCESS_ITEMS",
        "SCAN_COMPARISON_VIEW_QUICK_ACCESS_ITEMS",
        "DYNAMIC_SCAN_VIEW_QUICK_ACCESS_ITEMS",
        "INVENTORY_SCAN_VIEW_QUICK_ACCESS_ITEMS",
        "WORKITEM_VIEW_QUICK_ACCESS_ITEMS",
        "SERVICE_CONTRACT_VIEW_QUICK_ACCESS_ITEMS",
        "OPPORTUNITY_VIEW_QUICK_ACCESS_ITEMS",
        "CONFIGURATION_ITEM_VIEW_QUICK_ACCESS_ITEMS",
        "NETWORK_MONITORING_VIEW_QUICK_ACCESS_ITEMS",
        "EVENT_LOG_VIEW_QUICK_ACCESS_ITEMS",
        "FAILED_REP_LOGIN_LOG_VIEW_QUICK_ACCESS_ITEMS",
        "SUPPORT_REP_VIEW_QUICK_ACCESS_ITEMS",
        "DT_LAST_INCIDENT_ROUND_ROBIN_ROUTE",
        "DT_LAST_PROBLEM_ROUND_ROBIN_ROUTE",
        "DT_LAST_CHANGE_ROUND_ROBIN_ROUTE",
        "DT_LAST_LOGIN",
        "SKIP_LOGOUT_PROMPT",
        "SHOW_COUNTDOWN_SECONDS",
        "CHAT_SOUNDS_ENABLED",
        "USE_REP_EMAIL_AS_DEFAULT",
        "FOLLOW_DISCUSSION_POSTS",
        "LOCKOUT_FAILED_LOGIN_COUNT",
        "LOCKOUT_UNTIL",
        "LOCKOUT_TYPE",
        "DESKTOP_SEARCH_ENTITY_TYPES",
        "FAILED_CUSTOMER_LOGIN_LOG_VIEW_QUICK_ACCESS_ITEMS",
        "CATEGORY_VIEW_QUICK_ACCESS_ITEMS",
        "CHANGE_MANAGEMENT_VIEW_QUICK_ACCESS_ITEMS",
        "CHANGE_RULE_GROUP_VIEW_QUICK_ACCESS_ITEMS",
        "CORRESPONDENCE_TEMPLATE_VIEW_QUICK_ACCESS_ITEMS",
        "INCIDENT_RULE_GROUP_VIEW_QUICK_ACCESS_ITEMS",
        "MY_SUPPORT_PORTAL_VIEW_QUICK_ACCESS_ITEMS",
        "PROBLEM_MANAGEMENT_VIEW_QUICK_ACCESS_ITEMS",
        "PROBLEM_RULE_GROUP_VIEW_QUICK_ACCESS_ITEMS",
        "PURCHASE_RULE_GROUP_VIEW_QUICK_ACCESS_ITEMS",
        "PURCHASING_VIEW_QUICK_ACCESS_ITEMS",
        "LOCKOUT_EMAIL_UNLOCK_CODE",
        "ID_AD_DEFINITION",
        "ID_LDAP_DEFINITION",
        "SYNC_KEY",
        "ID_AD_SYNC_SETTING",
        "ID_LDAP_SYNC_SETTING",
        "SOURCE",
        "HOW_TO_SHOW_VIEWS_IN_WORK_ITEM_FORMS",
        "SYNC_TIME",
        "SECONDARY_LOGIN",
        "VIEWED_TOURS",
        "EVAL_EMAIL",
        "EVAL_EMAIL_VERIFIED",
        "EVAL_COMPANY",
        "IS_AVAILABLE_FOR_LOAD_BALANCED_ROUTING",
        "IS_AVAILABLE_FOR_ROUND_ROBIN_ROUTING",
        "CORRESPONDENCE_CAMPAIGN_VIEW_QUICK_ACCESS_ITEMS",
        "PIN_FAVORITES",
        "NOTIFICATION_CENTER_ALERTS_SHOW_TOAST",
        "NOTIFICATION_CENTER_ALERTS_ITEM_COUNT",
        "NOTIFICATION_CENTER_CHANGE_SHOW_TOAST",
        "NOTIFICATION_CENTER_CHANGE_ITEM_COUNT",
        "NOTIFICATION_CENTER_CUSTOMER_SHOW_TOAST",
        "NOTIFICATION_CENTER_CUSTOMER_ITEM_COUNT",
        "NOTIFICATION_CENTER_INCIDENT_SHOW_TOAST",
        "NOTIFICATION_CENTER_INCIDENT_ITEM_COUNT",
        "NOTIFICATION_CENTER_KNOWLEDGE_SHOW_TOAST",
        "NOTIFICATION_CENTER_KNOWLEDGE_ITEM_COUNT",
        "NOTIFICATION_CENTER_OPPORTUNITY_SHOW_TOAST",
        "NOTIFICATION_CENTER_OPPORTUNITY_ITEM_COUNT",
        "NOTIFICATION_CENTER_PROBLEM_SHOW_TOAST",
        "NOTIFICATION_CENTER_PROBLEM_ITEM_COUNT",
        "NOTIFICATION_CENTER_PURCHASE_SHOW_TOAST",
        "NOTIFICATION_CENTER_PURCHASE_ITEM_COUNT",
        "NOTIFICATION_CENTER_SURVEY_SHOW_TOAST",
        "NOTIFICATION_CENTER_SURVEY_ITEM_COUNT",
        "DESKTOP_MENU_RECENT_ITEMS_TYPE",
        "NOTIFICATION_CENTER_ASSET_SHOW_TOAST",
        "NOTIFICATION_CENTER_ASSET_ITEM_COUNT",
        "USE_UPDATED_VIEW_COMPONENT",
        "VIEW_SPACING",
        "DISPLAY_IN_CHAT_MENU",
        "HTML5_MOBILE_EXPERIENCE",
        "DEFAULT_MOBILE_VIEW",
        "FAILED_LOGIN_ATTEMPTS_VIEW_QUICK_ACCESS_ITEMS",
        "CHAT_NOTIFICATIONS_ENABLED",
        "ID_PERSONAL_CONTACTS_LIST",
        "INCLUDE_ROUTING_AVAILABILITY_ON_DESKTOP_PROFILE_MENU",
        "SCHEDULE_AVAILABILITY_ENABLED",
        "REP_AVAILABILITY_CHANGED_ACTION",
    ];

    protected $appends = [
        'name'
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


}