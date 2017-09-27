<?php

namespace Ingenious\Isupport\Contracts;

use \StdClass;

interface TicketProvider {

    public function force() : TicketProvider;

    public function archive() : TicketProvider;

    public function reps() : array;

    public function openTicketsByReps(array $reps) : StdClass;

    public function tickets($groupOrIndividual, $id) : StdClass;

    public function hot($groupOrIndividual, $id) : StdClass;

    public function aging($groupOrIndividual, $id) : StdClass;

    public function stale($groupOrIndividual, $id) : StdClass;

    public function open($groupOrIndividual, $id) : StdClass;

    public function unclosed($groupOrIndividual, $id) : StdClass;

    public function trends($groupOrIndividual, $id, $years) : StdClass;

    public function closed() : StdClass;

    public function recentClosed() : StdClass;
}
