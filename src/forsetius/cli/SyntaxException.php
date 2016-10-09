<?php
namespace forsetius\cli;

use forsetius\reuse\aException;

/**
 * Exception thrown if command line parameters can't be correctly parsed.
 * Used to cover the cases strictly connected to syntactic correctness of
 * script parameters and presence of required parameters and values.
 * @author forseti
 *
 */
class SyntaxException extends aException
{
    const BAD_SYNTAX = 75;
    const REQUIRED_VALUE = 76;
    const INVALID_VALUE = 77;
    const VALUE_NOT_ALLOWED = 78;
    const UNEXPECTED_ARGUMENT = 79;
        
    public function __construct ($message = "", $code = self::BAD_SYNTAX, \Exception $previous = NULL)
    {
        $script = \basename($GLOBALS['argv'][0]);

        $message = "Invalid syntax: $message \nSee: `$script --help` for more information\n";
        parent::__construct($message, $code, $previous);
    }
}
 ?>