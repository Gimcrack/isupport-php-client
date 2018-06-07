<?php

namespace Ingenious\Isupport;

use Cache;
use StdClass;
use Zttp\Zttp;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Support\Collection;
use Ingenious\Isupport\Contracts\TicketProvider as TicketProviderContract;

class IsupportFake extends TicketProviderStub implements TicketProviderContract  {

    /**
     * New up a new IsupportFake class
     */
    public function __construct()
    {
        parent::__construct();

        $this->faker = app(Generator::class);
    }

    /**
     * Get the reps with open tickets
     * @method reps
     *
     * @return   array
     */
    public function reps() : array
    {
        return [
            'persons' => explode(" ", trim($this->faker->sentence(),'.')),
            'groups' => explode(" ", trim($this->faker->sentence(),'.'))
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
        return $this->fakeTickets();
    }

    /**
     * Get tickets
     * @method tickets
     *
     * @return   json
     */
    public function tickets($groupOrIndividual = null, $id = null) : StdClass
    {
        return $this->fakeTickets();
    }

    /**
     * Get my tickets
     *
     * @param $rep
     * @return \StdClass
     */
    public function mine($rep) : StdClass
    {
        return $this->unclosed("Rep",$rep);
    }

    /**
     * Get the hot tickets
     * @method hot
     *
     * @return   json
     */
    public function hot($groupOrIndividual = null, $id = null) : StdClass
    {
        return $this->fakeTickets();
    }

    /**
     * Get the aging tickets
     * @method aging
     *
     * @return   json
     */
    public function aging($groupOrIndividual = null, $id = null) : StdClass
    {
        return $this->fakeTickets();
    }

    /**
     * Get the stale tickets
     * @method stale
     *
     * @return   json
     */
    public function stale($groupOrIndividual = null, $id = null) : StdClass
    {
        return $this->fakeTickets();
    }

    /**
     * Get all open tickets
     * @method open
     *
     * @return   json
     */
    public function open($groupOrIndividual = null, $id = null) : StdClass
    {
        return $this->fakeTickets();
    }

    /**
     * Get all unclosed tickets
     * @method unclosed
     *
     * @return   json
     */
    public function unclosed($groupOrIndividual = null, $id = null) : StdClass
    {
        return $this->fakeTickets();
    }

    /**
     * Get the ticket trends
     * @method trends
     *
     * @return   json
     */
    public function trends($groupOrIndividual = null, $id = null, $years = null) : StdClass
    {
        return $this->fakeTickets();
    }

    /**
     * Get the average time open
     *
     * @return  StcClass
     */
    public function averageTimeOpen($resolution, $groupOrIndividual, $id, $years) : StdClass
    {
        return $this->fakeTickets();
    }

    /**
     * Get closed tickets
     * @method closed
     *
     * @return   void
     */
    public function closed() : StdClass
    {
        return $this->fakeTickets();
    }

    /**
     * Get tickets closed recently
     * @method recentClosed
     *
     * @return   void
     */
    public function recentClosed() : StdClass
    {
        return $this->fakeTickets();
    }

    /**
     * Get some fake tickets
     * @method fakeTickets
     *
     * @return   object
     */
    private function fakeTickets()
    {
        $tickets = collect( range(1,10) )
            ->transform( function($num) {
                return new TicketFake( $this->faker );
            });

        return (object) [
            'description' => 'Some Fake Tickets',
            'from' => 1,
            'to' => $tickets->count(),
            'count' => $tickets->count(),
            'data' => $tickets->all()
        ];
    }
}
