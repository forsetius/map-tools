<?php
/**
 * @package forseti.pl\reuse
 */
namespace forsetius\reuse;

class Config
{
    protected $conf;
    
    public function __construct($configFile)
    {
    	if (! file_exists($configFile))
    		throw new FilesystemException("Config file `$configFile` was not found", FilesystemException::FILE_NOT_FOUND);
    	
    	$conf = file_get_contents($configFile);
        if ($conf === false)
            throw new FilesystemException("Config file `$configFile` is unavailable", FilesystemException::ACCESS_DENIED);
        
        $conf = json_decode($conf, true);
        if ($conf === null)
        	throw new FilesystemException("Invalid JSON in config file", FilesystemException::INVALID_CONTENT);

        $this->conf = $conf;
    }
    
    public function get($key)
    {
    	$arr = (array) explode(':',	$key);
    	$val = $this->conf;
    	foreach ($arr as $elem) {
    		if (! key_exists($elem, $val)) {
    			var_dump($elem, $val);
    			throw new LogicException("Part `$elem` of `$key` key not found", LogicException::CONFIG_ERROR);
    		}
    		$val = $val[$elem];
    	}
    	
        return $val;
    }
}
 ?>