<?php
namespace pl\forseti\cli;

/**
 * Command-line argument.
 * Every argument must have a value - either specified by the user
 * when invoking the script or default one. There are three types of
 * arguments:
 * - Binary - a switch used to turn option on or off. Default: false (off)
 * - Parameter - argument intended to provide value. Must have default used
 * if user omits it. If user specifies it, they must provide value.
 * - Option - the cross of above two. It's a switch that can enable some
 * feature and user can optionally provide value. Default: false (off) and
 * also another default must be provided in case if user specifies argument
 * without value
 *
 * @author forseti
 *
 */
abstract class aArgument
{
    protected $name;
    protected $value;

    /**
     * Constructor.
     * Defines new argument
     * @param string $name One-letter names are preferred. After parsing they will be referred like $claObject->v
     * @param string|integer|boolean $default Default value - used if user haven't specified the argument when calling the script
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
