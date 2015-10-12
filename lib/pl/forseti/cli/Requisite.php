<?php
namespace pl\forseti\cli;

/**
 * Command-line parameter used to introduce some value.
 * Must have default that's used if user omits it.
 * If user specifies it, they must obligatorily provide value.
 * @author forseti
 *
 */
class Requisite extends aArgument
{
    public function __construct($name, $default = null)
    {
        parent::__construct($name, $default);
    }

    public function getValue()
    {
        if ($this->value === null)
            throw new SyntaxException('Required value for parameter "'. $this->name .'" not supplied', SyntaxException::REQUIRED_VALUE);
        
        return $this->value;
    }
}