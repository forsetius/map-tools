<?php
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

spl_autoload_register(function( $class ) {
        require strtr($class, '_\\', '//') . '.php';
});

set_exception_handler(function(Exception $e) {
    echo $e->getTraceAsString() ."\n";
    error_log(\basename($GLOBALS['argv'][0]) . ';'. date('Y-m-d H:i:s') .';'. $e->getCode() .';'. $e->getName() .';'. $e->getMessage() .';'. $e->getFile() .';'. $e->getLine() ."\n", 3, 'error.log');
    exit ($e->getCode());
});
?>