<?php
namespace pl\forseti\cli;

use pl\forseti\reuse\aException;

/**
 * Exception thrown if command line parameters can't be correctly parsed.
 * Used to cover the cases strictly connected to syntactic correctness of
 * script parameters and presence of required parameters and values.
 * @author forseti
 *
 */
class SyntaxException extends aException
{
    const BAD_SYNTAX = 80;
    const REQUIRED_VALUE = 81;
    const INVALID_VALUE = 82;
    const VALUE_OUT_OF_BOUNDS = 83;
    const SUPERFLUOUS_VALUE = 84;
        
    public function __construct ($message = "", $code = self::BAD_SYNTAX, \Exception $previous = NULL)
    {
        $script = \basename($GLOBALS['argv'][0]);

        echo "Invalid syntax: $message \nSee: `$script --help` for more information\n";
        parent::__construct($message, $code, $previous);
    }
}
 ?>