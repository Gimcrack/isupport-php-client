<?php

namespace Ingenious\Isupport\Contracts;

use \StdClass;
use Illuminate\Database\Query\Builder;

interface TicketProvider {

    public function force() : TicketProvider;

    public function archive() : TicketProvider;

    public function reps() : array;

    public function mine($rep) : self;
    
    public function openTicketsByReps(array $reps) : self;

    public function tickets($groupOrIndividual, $id, $period) : self;

    public function hot($groupOrIndividual, $id) : self;

    public function aging($groupOrIndividual, $id) : self;

    public function stale($groupOrIndividual, $id) : self;

    public function open($groupOrIndividual, $id) : self;

    public function unclosed($groupOrIndividual, $id) : self;

    public function closed() : self;

    public function recentClosed() : self;

    public function trends($groupOrIndividual, $id, $years) : StdClass;

    public function averageTimeOpen($resolution, $groupOrIndividual, $id, $years) : StdClass;
}
