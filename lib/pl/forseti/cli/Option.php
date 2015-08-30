<?php
namespace pl\forseti\cli;

class Option
{
    const NO = '#NO';
    const OPT = '#OPT';
    const REQ = '#REQ';

    private $name;
    private $valuePresence;
    private $value;

    public function __construct($name, $value = self::REQ, $valuePresence = self::REQ)
    {
        $this->name = $name;
        $this->valuePresence = $valuePresence;
        $this->value = $value;
    }

    /**
     * name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get name with value presence indicator
     * @return string
     * @throws Exception if unknown parameter presence indicator
     */
    public function getNameV()
    {
    	switch ($this->valuePresence) {
    		case self::NO: return $this->name;
    		case self::OPT: return $this->name .'::';
    		case self::REQ: return $this->name .':';
    		default: throw new \Exception('Unknown value presence indicator: '. $this->param);
    	}
    }

    /**
     * Get default for option. If default == null then it is required
     * @return mixed
     * @throws Exception if the script was called without required option
     */
    public function getValue()
    {
    	if ($this->value === self::REQ) throw new \Exception('Required option '. $this->name .' not supplied');
        return $this->value;
    }
    
    public function setValue($val)
    {
    	$this->value = $val;
    }
    
    /**
     * Should the script be called explicitly with this option?
     * @return boolean
     */
    public function isRequired()
    {
    	return $this->value == self::REQ;
    }

}

 ?>
