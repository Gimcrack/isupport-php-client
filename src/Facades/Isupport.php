<?php

namespace Ingenious\Isupport\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Isupport\Isupport
 */
class Isupport extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Isupport';
    }
}
