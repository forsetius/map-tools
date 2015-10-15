<?php
/**
 * @package forseti.pl\reuse
 */
namespace pl\forseti\reuse;

use pl\forseti\maptools\CapabilityException;
class Config
{
    protected $app = array();
    protected $cap = array();
    protected $con = array();
    protected $def = array();
    
    public function __construct($configFile)
    {
        if (! \file_exists($configFile))
            throw new FilesystemException("Config file `$configFile` not found", FilesystemException::FILE_NOT_FOUND);
        
        require_once $configFile;
        $this->app = $app;
        $this->cap = $capabilities;
        $this->con = $connections;
        $this->def = $defaults;
    }
    
    public function __get($key)
    {
        $pref = \substr($key, 0, 3);
        if (! isset($this->$pref))
            throw new CapabilityException("No such configuration prefix `$pref`", CapabilityException::CONFIG_ISSUE);
        
        $prefix = $this->$pref;
        $key = (\substr($key, 3));
        if (! \array_key_exists($key, $prefix))
            throw new CapabilityException("No configuration key `$key` with prefix `$pref`", CapabilityException::CONFIG_ISSUE);
        
        return $prefix[$key];
    }
}
 ?>