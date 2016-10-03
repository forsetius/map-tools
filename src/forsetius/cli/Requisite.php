<?php
namespace forsetius\cli;

/**
 * Command-line parameter used to introduce some value.
 * Must have default that's used if user omits it.
 * If user specifies it, they must obligatorily provide value.
 * @author forseti
 *
 */
class Requisite extends aArgument
{
    public function __construct($name)
    {
        parent::__construct($name, null);
    }

    public function getValue()
    {
        if ($this->value === null)
            throw new SyntaxException('Required value for parameter "'. $this->name .'" not supplied', SyntaxException::REQUIRED_VALUE);
        
        return parent::getValue();
    }
}