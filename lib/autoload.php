<?php
use pl\forseti\reuse\LogicException;

preg_match('/^(?:(\d+\.[.\d]*\d+))/', PHP_VERSION, $phpVer);
if (empty($phpVer) || (version_compare($phpVer[0], '5.6.0') < 0))
    exit("This script requires PHP 5.6.0 or later. Version ". PHP_VERSION ." found.\n");

    error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

spl_autoload_register(function( $class ) {
    $filepath = strtr($class, '_\\', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR). '.php';
    if (! file_exists(__DIR__ . '/'. $filepath)) {
        error_log(\basename($GLOBALS['argv'][0]) . ';'. date('Y-m-d H:i:s') .';1;'. 'LogicException' .';'. "Script error: can't find the `$class` class;lib/autoload.php;8\n", 3, 'error.log');
        echo "Script error: can't find the `$class` class\n";
        exit;
    }
    require_once $filepath;
});

set_exception_handler(function(Exception $e) {
    echo $e->getTraceAsString() ."\n";
    error_log(\basename($GLOBALS['argv'][0]) . ';'. date('Y-m-d H:i:s') .';'. $e->getCode() .';'. $e->getName() .';'. $e->getMessage() .';'. $e->getFile() .';'. $e->getLine() ."\n", 3, 'error.log');
    exit ($e->getCode());
});
?>