<?php
namespace pl\forseti\cli;

class Binary extends aArgument{


    public function __construct($name)
    {
        parent::__construct($name, false);
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
     * @throws \Exception if non-boolean passed
     */
    public function setValue($val)
    {
        if (! \is_bool($val)) throw new \Exception("Invalid value. Expected boolean, passed: \n". var_dump($val));
        $this->value = ! $val;
    }
    
    public function isRequired()
    {
        return false;
    }
}