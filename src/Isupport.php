<?php

namespace Ingenious\Isupport;

use Cache;
use Zttp\Zttp;
use Carbon\Carbon;

class Isupport {

    protected $endpoint;

    protected $archive_flag;

    /**
     * New up a new Isupport class
     */
    public function __construct()
    {
        $this->endpoint = config('isupport.endpoint');
        $this->archive_flag = false;
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

                    return $ticket;
                });
            return collect($json);
        });
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
