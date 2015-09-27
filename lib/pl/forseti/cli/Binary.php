<?php
namespace pl\forseti\cli;

/**
 * Command-line switch used to enable some functionality.
 * Can't have value specified. Default: false (disable functionality)
 * @author forseti
 *
 */
class Binary extends aArgument{


    public function __construct($name, $default = false)
    {
        parent::__construct($name, $default);
    }
    
    public function getNameV()
    {
        return $this->name;
    }
    
    /**
     * Set boolean value for this switch-type argument.
     * Note: Value passed is negated. This is to ensure that getopt's 'false' turns to true indicating that given switch was specified in command line.
     * @param boolean $val
     * @return void
     * @throws SyntaxException if non-boolean passed
     */
    public function setValue($val)
    {
        if (! \is_bool($val)) throw new SyntaxException("Invalid value. Expected boolean, passed: \n". var_dump($val), 67);
        $this->value = ! $val;
    }
    
    public function isRequired()
    {
        return false;
    }
}