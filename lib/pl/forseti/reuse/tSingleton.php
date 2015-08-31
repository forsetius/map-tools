<?php
/**
 * Reusable tools used in different projects
 *
 * @package forseti.pl\reuse
 */
namespace pl\forseti\reuse;


/**
 * Provide single instance of object available from any part of application
 * @version 1.0
 */
trait tSingleton
{
    /**
     * @var tSingleton Object with tSingleton trait
     */
    protected static $instance;
    
    /**
     * Returns the instance of tSingleton-enabled object.
     * If not set yet, calls static::init to initialize
     * @return tSingleton
     */
    public static function getInstance()
    {
        return isset(static::$instance) ? static::$instance : static::$instance = new static;
    }

    /**
     * Disables public constructor.
     * Calls static::init() to do initialization
     */
    final private function __construct()
    {
        static::init();
    }
    
    /**
     * Initializes the Singleton's instance
     * @return void
     */
    protected function init() {}

    /**
     * Disables public unserialize
     */
    final private function __wakeup() {}
    
    /**
     * Disables public cloning
     */
    final private function __clone() {}
}
 ?>
