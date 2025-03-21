<?php

use \myframework\F as F;

/*
 * Перевод разницы времени из секунд в читаемую строку.
 * Примеры принимаемых и возвращаемых значений:
 * 1556 => 25 мин 56 сек
 * 21965 => 15 час 6 мин 5 сек
 * 9707028 => 112 дн 20 час 23 мин 48 сек
 */
function time_to_string($time)
{
	$s = '';

	if ($t = (int) ($time / 86400))
	{
		$s .= $t . ' дн';

		$time %= 86400;
	}

	if ($t = (int) ($time / 3600))
	{
		$s .= ' ' . $t . ' час';

		$time %= 3600;
	}

	if ($t = (int) ($time / 60))
	{
		$s .= ' ' . $t . ' мин';

		$time %= 60;
	}

	if ($t = $time or $s === '')
	{
		$s .= ' ' . $t . ' сек';
	}

	return trim($s);
}

function date_ru($format, $timestamp = null)
{
	if (strpos($format, 'M' ) !== false)
	{
		$months = [
			'января',
			'февраля',
			'марта',
			'апреля',
			'мая',
			'июня',
			'июля',
			'августа',
			'сентября',
			'октября',
			'ноября',
			'декабря',
		];

		$format = str_replace('M', $months[date('n', $timestamp) - 1], $format);
	}

	return date($format, $timestamp);
}

function remove_invisible_symbols(?string $str, bool $removeNewLines = true)
{
	if ($str !== null)
	{
		$str = str_replace(['​', '﻿'], '', $str); /// 0x200B (zero-width space невидимый символ), 0xFEFF (zero-width no-break space невидимый символ)

		$str = str_replace([' ', ' ', ' ', "\r", "\t", " "], ' ', $str); /// 0x200A (hair space), 0x00A0 (no-break space), 0x202F (narrow no-break space)

		$removeNewLines and $str = str_replace("\n", ' ', $str);

		$str = preg_replace('| {2,}|msui', ' ', $str);

		$str = trim($str);
	}

	return $str;
}

function remove_invisible_symbols_array(array $array, bool $removeNewLines = true)
{
	foreach ($array as &$value)
	{
		$value = remove_invisible_symbols($value, $removeNewLines);
	}

	return $array;
}

function remove_invisible_symbols_recursive(array $array, bool $removeNewLines = true)
{
	array_walk_recursive($array, function(&$v) use($removeNewLines)
	{
		$v = remove_invisible_symbols($v, $removeNewLines);
	});

	return $array;
}

/**
 * glob_recursive
 */
if (! function_exists('glob_recursive'))
{
	function glob_recursive($pattern, $flags = 0)
	{
		$files = glob($pattern, $flags);

		foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
		{
			$files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
		}

		return $files;
	}
}

/**
 * Ловим Fatal Error
 * Потому что Fatal Error (и не только) не ловится с помощью set_error_handler()
 * http://php.net/manual/ru/function.set-error-handler.php
 */
function catch_errors_on_shutdown()
{
	if ($error = error_get_last())
	{
		F::$app->handleError($error['type'], $error['message'], $error['file'], $error['line']);
	}
}

register_shutdown_function('catch_errors_on_shutdown');
