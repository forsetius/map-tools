<?php
namespace forsetius\reuse;

class GlobalPool
{
	protected static $collection = [];
	
	public static function get($name)
	{
		if (! key_exists($name, static::$collection))
			throw new \Exception("No `$name` object in collection");
		
		return static::collection[$name];
	}
	
	public static function set($name, $obj)
	{
		static::$collection[$name] = $obj;
	}
	
	public static function has($name)
	{
		return (key_exists($name, static::$collection));
	}
	
	public static function __callStatic($name, $args = [])
	{
		$verb = substr($name, 0, 3);
		$subject = lcfirst(substr($name, 3));
	
		switch ($verb) {
			case 'get' :
				if (! static::has($subject))
					throw new \Exception("No `$subject` object in collection");
				
				return static::$collection[$subject];
				
			case 'has' :
				return key_exists($subject, static::$collection);
				
			case 'set' :
				if (count($args)<1)
					throw new \Exception("No value for `$subject` passed");
					
				static::$collection[$subject] = $args[0];
				break;
				
			default:
				throw new \Exception("No `$verb` method");
		}
	}
}