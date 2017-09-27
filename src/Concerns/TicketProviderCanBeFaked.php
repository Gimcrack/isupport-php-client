<?php

namespace Ingenious\Isupport\Concerns;

use Ingenious\Isupport\Isupport as IsupportProvider;
use Ingenious\Isupport\IsupportFake;
use Ingenious\Isupport\Facades\Isupport;

trait TicketProviderCanBeFaked
{
    /**
     * Swap out the ticket provider
     * @method fake
     *
     * @return   this
     */
    public function fake()
    {
        Isupport::swap( new IsupportFake );

        return app('Isupport');
    }

    /**
     * Swap out the fake provider for a real one
     * @method dontFake
     *
     * @return   this
     */
    public function dontFake()
    {
        Isupport::swap( new IsupportProvider );

        return app('Isupport');
    }
}
