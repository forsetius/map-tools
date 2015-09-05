<?php
namespace pl\forseti\reuse;

class ExternalDataException extends \Exception
{
    const WRONG_DATA = 200;
    const INCORRECT_DATATYPE = 104;

    public function __construct ($message = "", $code = self::WRONG_DATA, \Exception $previous = NULL)
    {
        echo "Internal script error. Please send the `error.log` file to forseti.pl@gmail.com\n";
        parent::__construct($message, $code, $previous);
    }
}