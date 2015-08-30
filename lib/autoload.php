<?php
spl_autoload_register(function( $class ) {
        include strtr($class, '_\\', '//') . '.php';
    });
?>