<?php

namespace Ingenious\Isupport\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

trait MakesHttpRequests
{
    protected $endpoint;

    /**
     * Get the endpoint
     * @method endpoint
     *
     * @return   string
     */
    protected function endpoint()
    {
        return $this->endpoint;
    }

    /**
     * Get the formatted url
     * @method url
     *
     * @return   string
     */
    protected function url($url)
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
    protected function get( $url )
    {
        $response = Http::get( $url );

        $this->archive_flag = false;

        return $response;
    }

    /**
     * Get the request url and return json
     * @method getJson
     *
     * @return   response
     */
    protected function getJson($url)
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
}
