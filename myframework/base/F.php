<?php
namespace myframework;

/**
 */
abstract class F
{
	public static CApplication $app;

	public static CMysqlInstance $mysql;

	public static function assert($condition, $message = '')
	{
		if ((bool) $condition === false)
		{
			throw new CAssertException($message);
		}
	}

}
