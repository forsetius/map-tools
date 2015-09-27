<?php
namespace pl\forseti\reuse;

class ExternalDataException extends aException
{
    const WRONG_DATA = 200;
    const INCORRECT_DATATYPE = 104;

    public function __construct ($message = "", $code = self::WRONG_DATA, \Exception $previous = NULL)
    {
        echo "Problem with external data:\n$message\n";
        parent::__construct($message, $code, $previous);
    }
}