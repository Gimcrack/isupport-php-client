<?php

namespace Ingenious\Isupport;

use Cache;
use Zttp\Zttp;
use Carbon\Carbon;

class Isupport {

    protected $endpoint;

    protected $archive_flag;

    protected $force_flag;

    /**
     * New up a new Isupport class
     */
    public function __construct()
    {
        $this->endpoint = config('isupport.endpoint');

        $this->archive_flag = false;

        $this->force_flag = false;
    }

    /**
     * Get the endpoint
     * @method endpoint
     *
     * @return   string
     */
    public function endpoint()
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
        return vsprintf("%s/%s%s", [
            $this->endpoint,
            ($this->archive_flag) ? 'Archive/' : '',
            trim($url,'/')
        ]);
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

        return Cache::remember( "isupport.{$expanded}", 5, function() use ($expanded) {

            $json = $this->get($expanded)->json();

            $json['data'] = collect( $json['data'] )
                ->transform( function($ticket)
                {
                    if ( isset($ticket['created_date']) )
                    {
                        $ticket['created_date'] = Carbon::parse( $ticket['created_date'] )->format('Y-m-d');
                    }

                    $ticket['id'] = (int) $ticket['id'];

                    return (object) $ticket;
                });
            return (object) $json;
        });
    }

    /**
     * Force a refresh
     * @method force
     *
     * @return   $this
     */
    public function force()
    {
        $this->force_flag = true;

        return $this;
    }

    /**
     * Get archived tickets
     * @method archive
     *
     * @return   $this
     */
    public function archive()
    {
        $this->archive_flag = true;

        return $this;
    }

    /**
     * Get tickets
     * @method tickets
     *
     * @return   json
     */
    public function tickets($groupOrIndividual = null, $id = null)
    {
        return $this->getJson( "{$groupOrIndividual}/{$id}" );
    }

    /**
     * Get the hot tickets
     * @method hot
     *
     * @return   json
     */
    public function hot($groupOrIndividual = null, $id = null)
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
    public function aging($groupOrIndividual = null, $id = null)
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
    public function stale($groupOrIndividual = null, $id = null)
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
    public function open($groupOrIndividual = null, $id = null)
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
    public function unclosed($groupOrIndividual = null, $id = null)
    {
        $response = $this->getJson($groupOrIndividual, $id);

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
    public function trends($groupOrIndividual = null, $id = null, $years = null)
    {
        if ( is_numeric($groupOrIndividual) ) {
            $years = $groupOrIndividual;
            $groupOrIndividual = null;
        }

        return $this->getJson("Trends/{$groupOrIndividual}/{$id}/{$years}");
    }
}
