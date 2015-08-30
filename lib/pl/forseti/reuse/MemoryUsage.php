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
        $unit=array('B','kiB','MiB','GiB','TiB','PiB');
        return @round($value/pow(1024,($i=floor(log($value,1024)))),2).' '.$unit[$i];
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