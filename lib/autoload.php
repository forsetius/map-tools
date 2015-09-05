<?php
use pl\forseti\cli\SyntaxException;

error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

spl_autoload_register(function( $class ) {
        require strtr($class, '_\\', '//') . '.php';
});

set_exception_handler(function(Exception $e) {
    error_log(\basename($GLOBALS['argv'][0]) . ';'. date('Y-m-d H:i:s') .';'. $e->getCode() .';'. $e->getMessage() .';'. $e->getFile() .';'. $e->getLine(), 3, './error.log');
});
?>