<?php

namespace Ingenious\Isupport;

use StdClass;
use Zttp\Zttp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Ingenious\Isupport\Contracts\TicketProvider as TicketProviderContract;

class Isupport extends TicketProviderStub implements TicketProviderContract {

    /**
     * New up a new Isupport class
     */
    public function __construct()
    {
        parent::__construct();

        $this->endpoint = config('isupport.endpoint');
    }

    /**
     * Get the endpoint
     * @method endpoint
     *
     * @return   string
     */
    private function endpoint()
    {
        return $this->endpoint;
    }

    /**
     * Get the formatted url
     * @method url
     *
     * @return   string
     */
    private function url($url)
    {
        $url = vsprintf("%s/%s%s", [
            $this->endpoint,
            ($this->archive_flag) ? 'Archive/v2/' : '',
            trim($url,'/')
        ]);

        $this->archive_flag = false;

        return $url;
    }

    /**
     * Get the requested url
     *
     * @param      <type>  $url    The url
     */
    private function get( $url )
    {
        $response = Zttp::get( $url );

        $this->archive_flag = false;

        return $response;
    }

    /**
     * Get the request url and return json
     * @method getJson
     *
     * @return   response
     */
    private function getJson($url)
    {
        $expanded = $this->url($url);

        if ( $this->force_flag )
        {
            Cache::forget("isupport.{$expanded}");
        }

        $this->force_flag = false;

        return Cache::remember( "isupport.{$expanded}", 15, function() use ($expanded) {

            $json = $this->get($expanded)->json();

            $json['data'] = collect( $json['data'] )
                ->transform( function($ticket)
                {
                    if ( array_key_exists('id', $ticket) )
                    {
                        $ticket['id'] = (int) $ticket['id'];
                    }

                    if ( array_key_exists('created_date', $ticket) )
                    {
                        $ticket['created_date'] = $this->jsDateToCarbon( $ticket['created_date'] );
                    }

                    if ( array_key_exists('modified_date', $ticket) )
                    {
                        $ticket['modified_date'] = $this->jsDateToCarbon( $ticket['modified_date'] );
                    }

                    if ( array_key_exists('closed_date', $ticket) )
                    {
                        $ticket['closed_date'] = $this->jsDateToCarbon( $ticket['closed_date'] );
                    }

                    if ( array_key_exists('first_response_date', $ticket) )
                    {
                        $ticket['first_response_date'] = $this->jsDateToCarbon($ticket['first_response_date']);
                    }

                    if ( array_key_exists('last_response_date', $ticket) )
                    {
                        $ticket['last_response_date'] = $this->jsDateToCarbon($ticket['last_response_date']);
                    }

                    if ( array_key_exists('last_customer_response_date', $ticket) )
                    {
                        $ticket['last_customer_response_date'] = $this->jsDateToCarbon($ticket['last_customer_response_date']);
                    }

                    if ( array_key_exists('customer_responded_last',$ticket) )
                    {
                        $ticket['customer_responded_last'] = !! $ticket['customer_responded_last'];
                    }

                    return (object) $ticket;
                });
            return (object) $json;
        });
    }

    /**
     * Get the reps with open tickets
     * @method reps
     *
     * @return   array
     */
    public function reps() : array
    {
        $response = $this->unclosed();

        $all = $response->data->pluck('assignee')->unique();

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
     * @return   json
     */
    public function openTicketsByReps(array $reps) : StdClass
    {
        $response = $this->unclosed();

        $response->data = $response->data
            ->filter( function($ticket) use ($reps) {
                return in_array($ticket->assignee, $reps);
            })
            ->values();

        $response->to = $response->count = $response->data->count();

        return $response;
    }

    /**
     * Get tickets
     * @method tickets
     *
     * @return   json
     */
    public function tickets($groupOrIndividual = null, $id = null) : StdClass
    {
        return $this->getJson( "{$groupOrIndividual}/{$id}" );
    }

    /**
     * Get my tickets
     *
     * @param $rep
     * @return \StdClass
     */
    public function mine($rep) : StdClass
    {
        return $this->force()->unclosed("Rep",$rep);
    }

    /**
     * Get the hot tickets
     * @method hot
     *
     * @return   json
     */
    public function hot($groupOrIndividual = null, $id = null) : StdClass
    {
        $response = $this->unclosed($groupOrIndividual, $id);

        $response->data = $response->data
            ->reject( function($ticket) {
                $days = ( date('N') > 1 ) ? 2 : 4; // mondays
                return Carbon::parse( $ticket->created_date )->lt( Carbon::now()->subDays($days) );
            })
            ->values();

        $response->to = $response->count = $response->data->count();

        return $response;
    }

    /**
     * Get the aging tickets
     * @method aging
     *
     * @return   json
     */
    public function aging($groupOrIndividual = null, $id = null) : StdClass
    {
        $response = $this->unclosed($groupOrIndividual, $id);

        $response->data = $response->data
            ->reject( function($ticket) {
                $days_start = ( date('N') > 1 ) ? 2 : 4; // mondays
                $days_end  = ( date('N') > 1 ) ? 7 : 9; // mondays

                return Carbon::parse( $ticket->created_date )->gt( Carbon::now()->subDays($days_start) )
                    || Carbon::parse( $ticket->created_date )->lt( Carbon::now()->subDays($days_end) );
            })
            ->values();

        $response->to = $response->count = $response->data->count();

        return $response;
    }

    /**
     * Get the stale tickets
     * @method stale
     *
     * @return   json
     */
    public function stale($groupOrIndividual = null, $id = null) : StdClass
    {
        $response = $this->unclosed($groupOrIndividual, $id);

        $response->data = $response->data
            ->reject( function($ticket) {
                $days = ( date('N') > 1 ) ? 7 : 9; // mondays

                return Carbon::parse( $ticket->created_date )->gt( Carbon::now()->subDays($days) );
            })
            ->values();

        $response->to = $response->count = $response->data->count();

        return $response;
    }

    /**
     * Get all open tickets
     * @method open
     *
     * @return   json
     */
    public function open($groupOrIndividual = null, $id = null) : StdClass
    {
        $response = $this->getJson($groupOrIndividual, $id);

        $response->data = $response->data
            ->reject( function($ticket) {
                return $ticket->status != 'Open';
            })
            ->values();

        $response->to = $response->count = $response->data->count();

        return $response;
    }

    /**
     * Get all unclosed tickets
     * @method unclosed
     *
     * @return   json
     */
    public function unclosed($groupOrIndividual = null, $id = null) : StdClass
    {
        $response = $this->tickets($groupOrIndividual, $id);

        $response->data = $response->data
            ->reject( function($ticket) {
                return $ticket->status == 'Closed';
            })
            ->values();

        $response->to = $response->count = $response->data->count();

        return $response;
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
     * Get closed tickets
     * @method closed
     *
     * @param null|string $period
     * @return \StdClass
     */
    public function closed($period = null) : StdClass
    {
        return $this->archive()->tickets($period);
    }

    /**
     * Get the recently closed tickets.
     * @method recent
     *
     * @return   void
     */
    public function recent() : StdClass
    {
        return $this->archive()->getJson('Recent');
    }

    /**
     * Get tickets closed recently
     * @method recentClosed
     *
     * @return   void
     */
    public function recentClosed() : StdClass
    {
        $response = $this->recent();

        $response->data = $response->data
            ->reject( function($ticket) {
                $days = ( date('N') > 1 ) ? 2 : 4; // mondays

                return Carbon::parse( $ticket->closed_date )->lt( Carbon::now()->subDays($days) );
            })
            ->values();

        $response->to = $response->count = $response->data->count();

        return $response;
    }

    public function ticketResponseTimes($resolution = 10, $groupOrIndividual = null, $id = null, $years = 2)
    {
        return $this->averageTimeOpen($resolution, $groupOrIndividual, $id, $years);
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

        $response = $this->trends($groupOrIndividual, $id, $years);

        $response->data = $response->data
            ->reject( function($ticket) {
                return empty($ticket->closed_date);
            })
            ->reject( function($ticket) use ($oit) {
                if ( ! $oit ) return false;
                return preg_match("/TRIM|Records Support Team|GIS Team/", $ticket->assignee_group);
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
