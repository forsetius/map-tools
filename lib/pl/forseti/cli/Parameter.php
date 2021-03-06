<?php
namespace pl\forseti\cli;

/**
 * Command-line parameter used to introduce some value.
 * Must have default that's used if user omits it.
 * If user specifies it, they must obligatorily provide value.
 * @author forseti
 *
 */
class Parameter extends aArgument
{
    public function __construct($name, $default)
    {
        parent::__construct($name, $default);
    }
}