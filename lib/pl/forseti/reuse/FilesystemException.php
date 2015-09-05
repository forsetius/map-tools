<?php
namespace pl\forseti\reuse;

class FilesystemException extends \Exception
{
    const FILESYSTEM_ISSUE = 400;
    const FILE_NOT_FOUND = 401;
    const ALREADY_EXISTS = 402;
    const ACCESS_DENIED = 403;

    public function __construct ($message = "", $code = self::FILESYSTEM_ISSUE, \Exception $previous = NULL)
    {
        echo "Filesystem issue:\n$message.\nPlease check the directory in which you run the script.\n";
        parent::__construct($message, $code, $previous);
    }
}