<?php
namespace pl\forseti\maptools;

/**
 * Exception thrown if script attempted to use unsupported library, image format etc.
 * Often it means that the user made a typo when specyfying command line parameters.
 * Other times the library may not be available in given PHP installation.
 * @author forseti
 *
 */
class CapabilityException extends \Exception
{
    const INCAPABLE = 200;
    const UNSUPPORTED_LIBRARY = 201;
    const UNSUPPORTED_FORMAT = 202;
    const PARAM_OUT_OF_RANGE = 203;

    public function __construct ($message = "", $code = self::INCAPABLE, \Exception $previous = NULL)
    {
        $script = \basename($GLOBALS['argv'][0]);
        
        echo "Incorrect option:\n$message.\nSee: `$script -- help` for more information\n";
        parent::__construct($message, $code, $previous);
    }
}