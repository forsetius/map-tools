<?php
/**
 * @package forseti.pl\reuse
 */
namespace pl\forseti\reuse;

class Timer extends aRecorder
{
    const DECIMALS = 8;
    
    private $pauseTime = 0;

    /*  pause the timer  */
    public function pause()
    {
        $this->pauseTime = $this->current();
        return $this;
    }

    /*  unpause the timer  */
    public function unpause()
    {
        $this->startTime += ($this->current() - $this->pauseTime);
        $this->pauseTime = 0;
        return $this;
    }
    
    public function check($event) {
        return ($this->pauseTime > 0) ? parent::check($event, $this->pauseTime) : parent::check($event);
    }
    
    public static function format($value)
    {
        return ((int) ($value / 3600)) . date(':i:s', $value);
    }

    public function output($record) {
        return \sprintf("%s:%10s\n", $record['event'], static::format($record['value']));
    }
    
    public function current()
    {
        list($usec,$sec) = explode(' ', microtime());
        return round(((float)$usec + (float)$sec), Timer::DECIMALS);
    }

}