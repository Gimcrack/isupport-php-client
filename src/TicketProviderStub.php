<?php

namespace Ingenious\Isupport;

use Ingenious\Isupport\Contracts\TicketProvider;
use Ingenious\Isupport\Concerns\TicketProviderCanBeFaked;

abstract class TicketProviderStub {

    use TicketProviderCanBeFaked;

    protected $endpoint;

    protected $archive_flag;

    protected $force_flag;

    protected $faker;

    public function __construct()
    {
        $this->archive_flag = false;
    }

    /**
     * Force a refresh
     * @method force
     *
     * @return   $this
     */
    public function force() : TicketProvider
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
    public function archive() : TicketProvider
    {
        $this->archive_flag = true;

        return $this;
    }
}
