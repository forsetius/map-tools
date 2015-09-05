<?php
namespace pl\forseti\reuse;

/**
 * Exception thrown if there are serious error in program flow
 * leading to bad calls, lack of resources of their incorrect
 * type or wrong datatypes passed.
 * @author forseti
 *
 */
class LogicException extends \Exception
{
    const FAULTY_LOGIC = 100;
    const BAD_METHOD_CALL = 101;
    const METHOD_NOT_SUPPORTED = 102;
    const INVALID_RESOURCE = 103;
    
    public function __construct ($message = "", $code = self::FAULTY_LOGIC, \Exception $previous = NULL)
    {
        echo "Internal script error. Please send the `error.log` file to forseti.pl@gmail.com\n";
        parent::__construct($message, $code, $previous);
    }
}
 ?>