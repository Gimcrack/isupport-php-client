<?php

namespace Ingenious\Isupport;

use Illuminate\Support\Facades\DB;
use Ingenious\Isupport\Concerns\MakesHttpRequests;
use Ingenious\Isupport\Concerns\QueriesIsupportDatabase;
use Ingenious\Isupport\Contracts\TicketProvider;
use Ingenious\Isupport\Models\Group;
use Ingenious\Isupport\Models\Rep;
use Ingenious\Isupport\Models\Status;
use Ingenious\Isupport\Models\Incident;
use function last;
use StdClass;
use Zttp\Zttp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Query\Builder;
use Ingenious\Isupport\Contracts\TicketProvider as TicketProviderContract;

class Isupport extends TicketProviderStub implements TicketProviderContract {

    use QueriesIsupportDatabase,
        MakesHttpRequests;

    /**
     * New up a new Isupport class
     */
    public function __construct()
    {
        parent::__construct();

        $this->endpoint = config('isupport.endpoint');
    }

    /**
     * Get the reps with open tickets
     * @method reps
     *
     * @return   array
     */
    public function reps() : array
    {
        $ids = Incident::active()->pluck('ID_ASSIGNEE')->unique()->all();
        $all = Rep::whereIn('ID',$ids)->get()->pluck('name');

        return [
            'persons' => $all->reject( function($ass) {
                    return preg_match("/\d/", $ass) || $ass == "Web Change Request H";
                })->values()->sort()->values(),
            'groups' => $all->filter( function($ass) {
                    return preg_match("/\d/", $ass) || $ass == "Web Change Request H";
                })->values()->sort()->values()
        ];
    }

    /**
     * Get the tickets by reps
     * @method openTicketsByReps
     *
     * @return   \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function openTicketsByReps(array $reps) : TicketProvider
    {
        $ids = Rep::all()->filter(function($rep) use ($reps) {
           return in_array($rep->name, $reps);
        })->pluck('ID')->all();

        return $this->unclosed()
            ->whereIn('ID_ASSIGNEE',$ids);
    }

    /**
     * Get tickets
     * @method tickets
     *
     * @return \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function tickets($groupOrIndividual = null, $id = null, $period = null) : TicketProvider
    {
        $this->baseQuery();

        if ( is_numeric($groupOrIndividual) )
            $period = $groupOrIndividual;

        if ( $groupOrIndividual === 'Rep' ) {
            $id = Rep::all()->where('name',$id)->first()->ID;
            $this->where('ID_ASSIGNEE', $id);
        }

        if ( $groupOrIndividual === 'Group' ) {
            $id = Group::all()->where('name',$id)->first()->ID;
            $this->where('ID_GROUP',$id);
        }

        if ( $period > 0 )
            $this->where('DT_CREATED','>=',Carbon::now()->subYears($period));

        return $this;
    }

    /**
     * Get my tickets
     *
     * @param $rep
     * @return \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function mine($rep) : TicketProvider
    {
        return $this->force()->unclosed("Rep",$rep);
    }

    /**
     * Get the hot tickets
     * @method hot
     *
     * @return   \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function hot($groupOrIndividual = null, $id = null) : TicketProvider
    {
        $days = ( date('N') > 1 ) ? 1 : 3; // mondays

        return $this->unclosed($groupOrIndividual, $id)
                    ->where('DT_CREATED','>',Carbon::now()->subDays($days));
    }

    /**
     * Get the aging tickets
     * @method aging
     *
     * @return   \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function aging($groupOrIndividual = null, $id = null) : TicketProvider
    {
        $days_start = ( date('N') > 1 ) ? 2 : 4; // mondays
        $days_end  = ( date('N') > 1 ) ? 7 : 9; // mondays

        return $this->unclosed($groupOrIndividual, $id)
            ->whereBetween('DT_CREATED',[
                Carbon::now()->subDays($days_end),
                Carbon::now()->subDays($days_start),
            ]);
    }

    /**
     * Get the stale tickets
     * @method stale
     *
     * @return   \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function stale($groupOrIndividual = null, $id = null) : TicketProvider
    {
        $days = ( date('N') > 1 ) ? 7 : 9; // mondays

        return $this->unclosed($groupOrIndividual, $id)
            ->where('DT_CREATED','>',Carbon::now()->subDays($days));
    }

    /**
     * Get all open tickets
     * @method open
     *
     * @return   \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function open($groupOrIndividual = null, $id = null) : TicketProvider
    {
        return $this->tickets($groupOrIndividual, $id)
                    ->where('ID_STATUS',Status::OPEN);
    }

    /**
     * Get all unclosed tickets
     * @method unclosed
     *
     * @return   \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function unclosed($groupOrIndividual = null, $id = null) : TicketProvider
    {
        return $this->tickets($groupOrIndividual, $id);
    }

    /**
     * Get the active work stoppage tickets
     * 
     * @param null $groupOrIndividual
     * @param null $id
     * @param bool $open_only
     * @return \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function workStoppage($groupOrIndividual = null, $id = null, $open_only = false) : TicketProvider
    {
        $method = $open_only ? 'open' : 'unclosed';
        
        $ret = $this->$method($groupOrIndividual, $id);

        $ret->query->ofWorkStoppage(true);

        return $ret;
    }

    /**
     * Get closed tickets
     * @method closed
     *
     * @param null|int $period
     * @return \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function closed($period = null) : TicketProvider
    {
        return $this->archive()->tickets($period);
    }

    /**
     * Get tickets closed recently
     * @method recentClosed
     *
     * @return   \Ingenious\Isupport\Contracts\TicketProvider
     */
    public function recentClosed() : TicketProvider
    {
        $days = ( date('N') > 1 ) ? 2 : 4; // mondays

        return $this->closed()
            ->where('DT_CLOSED','>=',Carbon::now()->subDays($days));

    }
    
    public function ticketResponseTimes($resolution = 10, $groupOrIndividual = null, $id = null, $years = 2)
    {
        return $this->averageTimeOpen($resolution, $groupOrIndividual, $id, $years);
    }

    /**
     * Get the ticket trends
     * @method trends
     *
     * @return   json
     */
    public function trends($groupOrIndividual = null, $id = null, $years = null) : StdClass
    {
        if ( is_numeric($groupOrIndividual) ) {
            $years = $groupOrIndividual;
            $groupOrIndividual = null;
        }

        return $this->getJson("Trends/{$groupOrIndividual}/{$id}/{$years}");
    }

    /**
     * Description
     * @method averageTimeOpen
     *
     * @return   void
     */
    public function averageTimeOpen($resolution = 10, $groupOrIndividual = null, $id = null, $years = 2) : StdClass
    {
        $oit = false;
        if( $groupOrIndividual == 'Group' && $id == 'OIT' ) {
            $oit = true;
            $groupOrIndividual = null;
            $id = null;
        }

        $response = $this->archive()->tickets($groupOrIndividual, $id, $years);

        $response->data = $response->data
            ->reject( function($ticket) {
                return empty($ticket->closed_date);
            })
            ->reject( function($ticket) use ($oit) {
                if ( ! $oit ) return false;
                return preg_match("/TRIM|Records Support Team|GIS Team/", $ticket->group);
            })
            ->reject( function($ticket) use ($years) {
                return Carbon::now()->subYears($years)->gt( Carbon::parse($ticket->created_date) );
            })
            ->sortBy("created_date")
            ->groupBy( function($ticket) use ($resolution) {
                $ts = Carbon::parse( $ticket->created_date )->timestamp;
                $interval = $resolution * 24 * 60 * 60; // $resolution days converted to sec.
                return "period__" . floor( $ts / $interval );
            })
            ->transform( function($group) {
                return [
                    'min_date' => $group->min('created_date'),
                    'count' => (int) $group->count(),
                    'min_days_to_first_response' => (int) $group->min('days_to_first_response'),
                    'max_days_to_first_response' => (int) $group->max('days_to_first_response'),
                    'average_days_to_first_response' => (float) number_format($group->avg('days_to_first_response'),2),
                    'average_days_to_first_response_corrected_1' => (float) number_format($group->pluck('days_to_first_response')->remove_outliers(1)->avg(),2),
                    'average_days_to_first_response_corrected_2' => (float) number_format($group->pluck('days_to_first_response')->remove_outliers(2)->avg(),2),
                    'average_days_to_first_response_corrected_3' => (float) number_format($group->pluck('days_to_first_response')->remove_outliers(3)->avg(),2),
                    'std_dev' => $group->pluck('days_to_first_response')->stddev(),
                ];
            })
            ->values();

        return $response;
    }

    /**
     * Convert a JSON date string to a Carbon instance
     * @method jsDateToCarbon
     *
     * @return   string
     */
    private function jsDateToCarbon($date)
    {
        if ( ! $date ) return null;

        // timestamp in microsec
        $date_us = str_replace(["/Date(",")/"], null, $date);

        return date("Y-m-d", $date_us/1000 );
    }
}
