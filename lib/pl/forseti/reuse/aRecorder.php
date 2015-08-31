<?php
/**
 * @package forseti.pl\reuse
 */
namespace pl\forseti\reuse;

abstract class aRecorder
{
    protected $start = 0;
    protected $history = array();

    /**
     * Constructor.
     * Starts the recording
     * @return void
     */
    public function __construct()
    {
        $this->start = $this->current();
        $this->check('Start');
    }

    /**
     * Check the indicator's value and record it in $this->history
     * @param string $event Description of measurement point
     * @return integer|float $value
     */
    public function check($event, $value = null) {
        $record = array('event'=>$event,
                        'value'=>((\is_null($value)) ? ($this->current() - $this->start) : $value)
                        );
        $this->history[] = $record;
        return $record['value'];
    }

    /**
     * Returns difference between the last record and previous one
     * @return integer
     */
    public function getDiff() {
        $h = $this->history;
        $e = end($h)['value'];
        $p = prev($h)['value'];
        $result = $e - ((count($h)>1) ? $p : 0);
        return $result;
    }

    /**
     * Return $value in human-readable format
     * @param integer $value
     * @return string
     */
    abstract public static function format($value);

    /**
     * Output one record in human-readable format
     * @param array $record
     * @return string
     */
    abstract public function output($record);

    /**
     * Outputs whole history to monospaced string
     * @return string
     */
    public function outputAll() {
        $result = '';
        foreach ($this->history as $record) {
            $result .= $this->output($record) . "\n";
        }
        return $result;
    }

    /**
     * Outputs whole history to csv-type string
     * @return string
     */
    public function outputCSV() {
        $result = '';
        foreach ($this->history as $record) {
            $result .= $record['event'] . ';' . $record['usage'] . "\n";
        }
        return $result;
    }

    /**
     * Returns measurement of current value of indicator
     * @return integer
     */
    abstract public function current();

}
 ?>
