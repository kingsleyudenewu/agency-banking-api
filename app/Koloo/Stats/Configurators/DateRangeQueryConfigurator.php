<?php

namespace App\Koloo\Stats\Configurators;

interface DateRangeQueryConfigurator {

    public function fromDateOperator() : string ;
    public function toDateOperator() : string ;
    public function fromDate() : string ;
    public function toDate() : string ;
    public function getField(): string ;

}
