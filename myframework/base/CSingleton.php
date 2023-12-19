<?php
namespace myframework;

/**
 */
abstract class CSingleton
{
	/** @var CSingleton[] */
	private static array $_instances = [];

	/**
	 * @return static
	 */
	public static function inst()
	{
		$class = static::class;

		if (! isset(self::$_instances[$class]))
		{
			self::$_instances[$class] = new $class();
		}

		return self::$_instances[$class];
	}

	private function __clone()
	{
	}

}
