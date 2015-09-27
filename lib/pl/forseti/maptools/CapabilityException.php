<?php
namespace pl\forseti\maptools;

use pl\forseti\reuse\aException;

/**
 * Exception thrown if script attempted to use unsupported library, image format etc.
 * Often it means that the user made a typo when specyfying command line parameters.
 * Other times the library may not be available in given PHP installation.
 * @author forseti
 *
 */
class CapabilityException extends aException
{
    const UNSUPPORTED_LIBRARY = 70;
    const UNSUPPORTED_FORMAT = 71;
    const REGISTRY_ISSUE = 72;
    const CONFIG_ISSUE = 73;

    public function __construct ($message = "", $code, \Exception $previous = NULL)
    {
        $script = \basename($GLOBALS['argv'][0]);
        
        echo "Incorrect option:\n$message.\nSee: `$script --help` for more information\n";
        parent::__construct($message, $code, $previous);
    }
}