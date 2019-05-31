<?php

namespace Ingenious\Isupport\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Incident extends BaseModel
{
    const CREATED_AT = 'DT_CREATED';
    const UPDATED_AT = 'DT_MODIFIED';

    public $cacheKeyUsesMaxPrimaryKey = true;
    public $cacheKeyUsesModelCount = true;
    
    protected $connection = 'isupport';

    protected $table = 'INCIDENTS';

    protected $primaryKey = 'ID';

    protected $dates = [
        'DT_CREATED',
        'DT_MODIFIED',
        'DT_CLOSED'
    ];

    protected $casts = [
        'ID' => 'int'
    ];

    protected $with = [
        'assignee',
        'author',
        'group',
        'customer',
        'category',
        'correspondence',
        'audit',
        'work',
        'status',
        'custom_fields'
    ];

    protected $hidden = [
        "ID",
        "GUID",
        "ID_ASSIGNEE",
        "ID_ASSIGNEE_OLD",
        "ID_AUTHOR",
        "TWITTER_STATUS_IDENTIFIER",
        "FEEDBACK_QUESTION",
        "FEEDBACK_RESPONSE",
        "ID_CUSTOMER_AUTHOR",
        "FACEBOOK_POST_IDENTIFIER",
        "FINISHED_COUNT",
        "IN_PROGRESS_COUNT",
        "FUTURE_COUNT",
        "USER_BROWSER",
        "USER_DEVICE",
        "MODIFIED_BY_ASSIGNED_CUSTOMER",
        "MY_SUPPORT_SUBMISSION_IP_ADDRESS",
        "CORRESPONDENCE_MESSAGE_ID",
        "ID_TWILIO_INTEGRATION",
        "ID_CATEGORY",
        "ID_CLOSED_BY",
        "ID_CUSTOMER",
        "ID_COMPANY",
        "ID_GROUP",
        "ID_SLA",
        "ROUTE_COUNT",
        "DT_CLOSED",
        "DT_CREATED",
        "DT_FOLLOWUP",
        "DT_ESC_LAST_OCCUR",
        "ESC_OFFENSE_COUNT",
        "ESC_OFFENSE_COUNT_EMERGENCY",
        "LEVEL_AR",
        "NUMBER",
        "PRIORITY",
        "PROBLEM",
        "RESOLUTION",
        "SOURCE",
        "DT_MODIFIED",
        "ID_MODIFIED_BY",
        "ID_TEMPLATE",
        "ID_STATUS",
        "SLA_CLOSE_WARN_SENT",
        "SLA_PRIORITY_WARN_SENT",
        "SLA_AUTO_REASSIGN_WARN_SENT",
        "ID_CURRENT_SLA_MATRIX",
        "ID_CURRENT_ACKNOWLEDGMENT",
        "DT_CONVERSION",
        "DT_GENERATED",
        "SCH_WARN_SENT",
        "APPROVAL_WORKFLOW_GUID",
        "ID_CURRENT_APPROVAL_CYCLE",
        "IS_CURRENT_APPROVAL_CYCLE_AD_HOC",
        "SHORT_DESCRIPTION",
        "ID_URGENCY",
        "ID_IMPACT",
        "CUSTOM_NUMBER",
        "ID_EMAIL_SERVER_ACCOUNT",
        "ID_SERVICE_CONTRACT",
        "IS_COURTESY_TICKET",
        "MODIFIED_BY_CUSTOMER",
        "ID_RULE_GROUP",
        "ID_SOCIAL_CLIENT",
        "EVALUATED_POST_HIERARCHY_CREATION",
    ];

    public function assignee()
    {
        return $this->belongsTo(Rep::class,'ID_ASSIGNEE','ID');
    }

    public function group()
    {
        return $this->belongsTo(Group::class,'ID_GROUP','ID');
    }

    public function author()
    {
        return $this->belongsTo(Rep::class,'ID_AUTHOR','ID');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'ID_CATEGORY','ID');
    }

    public function status()
    {
        return $this->belongsTo(Status::class,'ID_STATUS','ID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'ID_CUSTOMER','ID');
    }

    public function correspondence()
    {
        return $this->hasMany(IncidentCorrespondenceHistory::class,"INCIDENT_NUMBER","NUMBER");
    }

    public function audit()
    {
        return $this->hasMany(IncidentAuditHistory::class,"INCIDENT_NUMBER","NUMBER");
    }

    public function work()
    {
        return $this->hasMany(RepWorkHistory::class,"INCIDENT_NUMBER","NUMBER");
    }

    public function custom_fields()
    {
        return $this->hasMany(IncidentCustomField::class,"ID_INCIDENT","ID");
    }

    public function getFieldsAttribute()
    {
        return (object) $this->custom_fields
            ->flatMap(function(IncidentCustomField $field) {
                return [$field->name => $field->DATA];
            })
            ->all();
    }

    public function getCustomerCorrespondenceAttribute()
    {
        return $this->correspondence()->ofCustomer()->get();
    }

    public function getRepCorrespondenceAttribute()
    {
        return $this->correspondence()->ofRep($this->ID_ASSIGNEE)->get();
    }

    public function getRepAuditAttribute()
    {
        return $this->audit()->ofRep($this->ID_ASSIGNEE)->get();
    }

    public function getRepWorkAttribute()
    {
        return $this->work()->ofRep($this->ID_ASSIGNEE)->get();
    }

    public function getLastCustomerResponseAttribute()
    {
        return $this->customer_correspondence->sortByDesc('DT_CREATED')->first();
    }

    public function getLastCustomerResponseDateAttribute()
    {
        return optional($this->last_customer_response)->DT_CREATED;
    }

    public function getLastCustomerResponseMessageAttribute()
    {
        return optional($this->last_customer_response)->ENTRY;
    }

    public function getLastResponseAttribute()
    {
        return collect([
            //$this->rep_audit->sortByDesc('DT_CREATED')->first(),
            $this->rep_correspondence->sortByDesc('DT_CREATED')->first(),
            $this->rep_work->sortByDesc('DT_CREATED')->first()
        ])
            ->filter()
            ->sortByDesc('DT_CREATED')
            ->first();
    }

    public function getFirstResponseAttribute()
    {
        return collect([
            //$this->rep_audit->sortByDesc('DT_CREATED')->first(),
            $this->rep_correspondence->sortBy('DT_CREATED')->first(),
            $this->rep_work->sortBy('DT_CREATED')->first()
        ])
            ->filter()
            ->sortBy('DT_CREATED')
            ->first();
    }

    public function getLastResponseDateAttribute()
    {
        return optional($this->last_response)->DT_CREATED;
    }

    public function getFirstResponseDateAttribute()
    {
        return optional($this->first_response)->DT_CREATED;
    }

    public function getCustomerRespondedLastAttribute()
    {
        $last_customer_response = $this->last_customer_response_date;
        $last_rep_response = $this->last_response_date;

        return optional($last_customer_response)->gt($last_rep_response) ?? false;
    }

    public function getTimeWorkedAttribute()
    {
        return $this->work->sum('TIME_WORKED');
    }

    public function priority()
    {
        switch( (int) $this->PRIORITY) {
            case 0 :
                return "1 Low";
                
            case 1 :
                return "2 Medium";
                
            case 2 :
                return "3 High";
        }
    }

    /**
     * Get the incident with the given number
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $number
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function scopeOfNumber(Builder $query, $number)
    {
        return $query->where('NUMBER',$number)->first();
    }

    /**
     * Get archive tickets
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeArchive(Builder $query)
    {
        return $query->where('ID_STATUS',Status::CLOSED);
    }

    /**
     * Get tickets with the specified work stoppage value
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $stoppage
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfWorkStoppage(Builder $query, $stoppage = true)
    {
        $stoppage = $stoppage ? 'yes' : 'no';

        return $query->whereHas('custom_fields', function($subquery) use ($stoppage) {
            return $subquery->where('ID_FIELD',4)->where('DATA',$stoppage);
        });
    }

    /**
     * Get active tickets
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('ID_STATUS','<>',Status::CLOSED);
    }

    public function toArray()
    {
        return [
            "assignee" =>  optional($this->assignee)->name,
            "author" => optional($this->author)->name,
            "category" => optional($this->category)->name,
            "closed_date" => optional($this->DT_CLOSED)->format("Y-m-d H:i:s.000"),
            "created_date" => $this->DT_CREATED->format("Y-m-d H:i:s.000"),
            "customer" => optional($this->customer)->name,
            "department" => optional($this->customer)->department,
            "id" => $this->ID,
            "number" => $this->NUMBER,
            "modified_date" => $this->DT_MODIFIED->format("Y-m-d H:i:s.000"),
            "first_response_date" => optional($this->first_response_date)->format("Y-m-d H:i:s.000"),
            "last_response_date" => optional($this->last_response_date)->format("Y-m-d H:i:s.000"),
            "last_customer_response_date" => optional($this->last_customer_response_date)->format("Y-m-d H:i:s.000"),
            "last_customer_response_message" => $this->last_customer_response_message,
            "customer_responded_last" => $this->customer_responded_last,
            "days_open" => Carbon::now()->diffInDays($this->DT_CREATED),
            "days_since_last_response" => Carbon::now()->diffInDays($this->last_response_date),
            "days_to_first_response" => optional($this->first_response_date)->diffInDays($this->DT_CREATED),
            "problem" => $this->PROBLEM,
            "group" => optional($this->group)->name,
            "time_worked" => $this->time_worked,
            "status" => $this->status->name,
            "priority" => $this->priority(),
            "subject" => optional($this->fields)->subject,
            "impact" => optional($this->fields)->impact,
            "urgency" => optional($this->fields)->urgency,
            "work_stoppage" => optional($this->fields)->work_stoppage,
            "custom_fields" => $this->fields,
        ];
    }
}