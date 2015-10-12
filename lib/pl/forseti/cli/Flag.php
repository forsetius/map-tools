<?php
namespace pl\forseti\cli;

/**
 * Command-line switch used to enable some functionality.
 * Can't have value specified. Default: false (disable functionality)
 * @author forseti
 *
 */
class Flag extends aArgument
{
    public function __construct($name, $default = false)
    {
        parent::__construct($name, $default);
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
        if (! \is_bool($val)) throw new SyntaxException("Invalid value. Expected boolean, passed: \n". var_dump($val), SyntaxException::INVALID_VALUE);
        $this->value = $val;
    }

    protected function validate($val) {
        if (! \is_bool($val))
            throw new SyntaxException("Incorrect value: `$val`", SyntaxException::INVALID_VALUE);
    }
}