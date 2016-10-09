<?php
namespace forsetius\reuse;

class FilesystemException extends aException
{
    const FILE_NOT_FOUND = 90;
    const FILE_EXISTS = 91;
    const ACCESS_DENIED = 92;
    const FOLDER_NOT_FOUND = 93;
    const FOLDER_NOT_EMPTY = 94;
    const INVALID_CONTENT = 95;

    public function __construct ($message = "", $code, \Exception $previous = NULL)
    {
        echo "Filesystem issue:\n$message.\n";
        parent::__construct($message, $code, $previous);
    }
}