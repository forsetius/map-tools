<?php
namespace pl\forseti\cli;

/**
 * Exception thrown if command line parameters can't be correctly parsed.
 * Used to cover the cases strictly connected to syntactic correctness of
 * script parameters and presence of required parameters and values.
 * @author forseti
 *
 */
class SyntaxException extends \Exception
{
    const CLI_SYNTAX = 300;
    const REQUIRED_PARAM = 301;
    const REQUIRED_VALUE = 302;
    const VALUE_TYPE = 303;
        
    public function __construct ($message = "", $code = self::CLI_SYNTAX, \Exception $previous = NULL)
    {
        $script = \basename($GLOBALS['argv'][0]);

        echo "Invalid syntax: $message \nSee: `$script -- help` for more information\n";
        parent::__construct($message, $code, $previous);
    }
}
 ?>