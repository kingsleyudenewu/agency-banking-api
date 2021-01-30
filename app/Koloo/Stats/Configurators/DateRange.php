<?php


namespace App\Koloo\Stats\Configurators;

/**
 * Class DateRange
 *
 * @package \App\Koloo\Stats\Configurators
 */
class DateRange implements DateRangeQueryConfigurator {

    private  $fromDate;
    private $toDate;
    private $fromDateOperator;
    private $toDateOperator;
    private $field;

    public function __construct(string $fromDate = '',
                                string $toDate = '',
                                string $field = 'created_at',
                                string $fromDateOperator = '>=',
                                string $toDateOperator = '<=') {

        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->fromDateOperator = $fromDateOperator;
        $this->toDateOperator  = $toDateOperator;
        $this->field = $field;
    }

    public function fromDateOperator(): string {
       return $this->fromDateOperator;
    }

    public function toDateOperator(): string {
        return $this->toDateOperator;
    }

    public function fromDate(): string {
        return $this->fromDate;
    }

    public function toDate(): string {
        return $this->toDate;
    }

    public function getField(): string {
        return $this->field;
    }
}
