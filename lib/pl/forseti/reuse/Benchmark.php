<?php
/**
 * @package forseti.pl\reuse
 */
namespace pl\forseti\reuse;

/**
 * Benchmark speed and memory usage of a script
 *
 * It is Singleton to allow access to centralized instance from every point of complex script
 * or framework. Also, we don't want to complicate its usage by requiring to change the method invocation
 * by adding an argument containing benchmark object. Hence Singleton - workflow: insert Benchmark method
 * invocations -> check & fix -> delete -> profit
 * @author forseti
 * @version 1
 */
class Benchmark
{
    use tSingleton;
    
    /**
     * Returns the instance of tSingleton-enabled object.
     * If not set yet, calls static::init to initialize
     * @return Benchmark
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @var Timer[] Timers with their time logs.
     */
    private $timers = array();

    /**
     * @var MemoryUsage Memory usage log.
     */
    private $memoryUsage;

    /**
     * @var boolean Should we output measurement text?
     */
    private $echo = false;

    /**
     * Here, we set up 'main' Timer to start immediately
     * @see Timer
     * @return void
     */
    protected function init() {
        $this->addTimer('main');
        $this->memoryUsage = new MemoryUsage();
    }

    /**
     * Should we output text
     * @param boolean $val Set true to output text for measurements. False only logs to file
     * @return Benchmark
     */
    public function setEcho($val)
    {
        $this->echo = $val;
        return $this;
    }

    /**
     * Adds new named Timer and starts it
     * @param string $timerName Name for this timer
     * @return Benchmark
     * @uses pl\forseti\reuse\Timer
     */
    public function addTimer($timerName)
    {
        $this->timers[$timerName] = new Timer();
        return $this;
    }

    /**
     * Returns named Timer
     * @param string $timerName Name of Timer. If omitted 'main' is assumed
     * @return Timer
     */
    public function getTimer($timerName = 'main')
    {
        return $this->timers[$timerName];
    }

    /**
     * Shortcut to recTime() and recMemory()
     * @param string $event
     * @param string $timerName
     * @return Benchmark
     */
    public function rec($event, $timerName = 'main')
    {
        return $this->recTime($event, $timerName)->recMemory($event);
    }
    
    /**
     * Record time.
     * If $this->echo is true, time is outputted.
     * If $event != '', also time elapsed from previous named event is output
     * @param string $event Description of measurement point. If omitted '' is assumed
     * @param string $timerName Name of Timer to record. If omitted 'main' is assumed
     * @return Benchmark
     */
    public function recTime($event, $timerName = 'main')
    {
        $value = $this->timers[$timerName]->check($event);
        if ($this->echo) {
            echo Timer::format($value) . " (+" . Timer::format($this->timers[$timerName]->getDiff()) .")\n";
        }
        return $this;
    }


    /**
     * Record memory usage.
     * If $this->echo is true, usage is outputted.
     * @param string $event Description of measurement point.
     * @return Benchmark
     */
    public function recMemory($event){
        $value = $this->memoryUsage->check($event);
        if ($this->echo) {
            echo MemoryUsage::format($value) . " (+" . MemoryUsage::format($this->memoryUsage->getDiff()) .")\n";
        }
        return $this;
    }

    /**
     * Description
     * @param integer|float $n
     * @return void
     */
    public function idle($n=10) {
        for($i=0;$i<10000000*$n;$i++);
    }

    public function outputAll() {
        echo "\nMemory usage statistics:\n========================\n" . $this->memoryUsage->outputAll() ."\n";
        echo "\nTimers:\n========================";
        foreach ($this->timers as $name=>$timer) {
            echo $name . ":\n". $timer->outputAll() . "\n";
        }
    }

    public function saveCSV() {
        $output = "Memory usage statistics:\n". $this->memoryUsage->outputCSV() ."\n";
        $output .= "Timers:\n";
        foreach ($this->timers as $name=>$timer) {
            $output .= $name . ":\n". $timer->outputCSV() . "\n";
        }
        \file_put_contents('benchmark.txt', $output);
    }
}
