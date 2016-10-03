<?php
namespace forsetius\cli;

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
    
    protected function validate($val) {
        if (! \is_bool($val)) throw new SyntaxException("Value for `{$this->name}` is not allowed". var_dump($val), SyntaxException::VALUE_NOT_ALLOWED);
    }
}