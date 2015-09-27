<?php
namespace pl\forseti\reuse;

/**
 * Exception thrown if there are serious error in program flow
 * leading to bad calls, lack of resources of their incorrect
 * type or wrong datatypes passed.
 * @author forseti
 *
 */
class LogicException extends aException
{
    const FAULTY_LOGIC = 1;
    const BAD_METHOD_CALL = 64;
    const METHOD_NOT_SUPPORTED = 65;
    const INVALID_RESOURCE = 66;
    const INVALID_TYPE = 67;
    const ARGUMENT_OUT_OF_BOUNDS = 68;
    const NOT_IMPLEMENTED = 69;
    
    public function __construct ($message = "", $code = self::FAULTY_LOGIC, \Exception $previous = NULL)
    {
        echo "Internal script error. Please send the `error.log` file to forseti.pl@gmail.com\n";
        parent::__construct($message, $code, $previous);
    }
}
 ?>