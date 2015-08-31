<?php
namespace pl\forseti\cli;

abstract class aArgument
{
    protected $name;
    protected $value;

    /**
     * Constructor.
     * Defines new aArgument
     * @param string $name One-letter names are preferred. After parsing they will be referred like $claObject->v
     * @param string|integer|boolean $value This parameter is used to denote if argument itself is required or optional. If required (denoted by $value=aArgument::REQ), it MUST be specified on the command line along with it's value (as spript cannot make assumption about the value). If optional, $value is the default value the script assumes if user haven't used the argument when invoking the script. In parsing that default value will be overwritten if given argument was passed in command line. Specify 'false' if argument is of 0-1 switch type and value is forbidden. Default: aArgument::REQ.
     * @param string|int $optional Specifies if argument must, may or may not be followed by its value. aArgument::REQ is used if value is mandatory, aArgument::NO if value is forbidden. If argument may have value specified but it isn't mandatory then $optional's value is the default assumed if argument was used without specifying the value.
     */
    public function __construct($name, $default)
    {
        $this->name = $name;
        $this->value = $default;
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
     */
    abstract public function getNameV();

    public function getValue()
    {
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
    abstract public function isRequired();

}

 ?>
