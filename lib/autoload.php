<?php
spl_autoload_register(function( $class ) {
        require strtr($class, '_\\', '//') . '.php';
    });
?>