<?php
/**
 * @package forseti.pl\reuse
 */
namespace pl\forseti\reuse;

class MemoryUsage extends aRecorder
{
    
    /**
     * Converts size in bytes to size expressed with binary prefix (1024 based)
     * @param integer $value Size in bytes
     * @return string $value expressed with binary prefix
     */
    public static function format($value)
    {
        $val = \abs($value);
        $unit=array('B','kiB','MiB','GiB','TiB','PiB');
        $val = @round($val/pow(1024,($i=floor((($val==0)?0:log($val,1024))))),2).' '.$unit[$i];
        return ($value < 0) ? '-'.$val : $val;
    }
    
    public function output($record) {
        return \sprintf("%s:%15s\n", $record['event'], static::format($record['value']));
    }
    
    /**
     * Returns amount of system memory.
     * Uses Linux call to 'free' command
     * @return integer
     */
    public function current() {
        $output = \shell_exec('free -b');
        return (int) \trim(\substr($output, 166, 11));
    }
    
}