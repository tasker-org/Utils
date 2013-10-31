<?php
/**
 * Class Timer
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 31.10.13
 */
namespace Tasker\Utils;

use Tasker\ErrorException;

class Timer
{

	const MILI_SECONDS = 1000;
	const SECONDS = 1;

	/**
	 * @param $name
	 * @return int
	 */
	public static function d($name)
	{
		static $time = array();
		$now = microtime(TRUE);
		$delta = isset($time[$name]) ? $now - $time[$name] : 0;
		$time[$name] = $now;
		return $delta;
	}

	/**
	 * @param $time
	 * @param int $units
	 * @param int $precision
	 * @return float
	 */
	public static function convert($time, $units = self::SECONDS, $precision = 2)
	{
		return round($time * static::getUnits($units), $precision);
	}

	/**
	 * @param $units
	 * @return mixed
	 * @throws \Tasker\ErrorException
	 */
	private static  function getUnits($units)
	{
		if(in_array($units, array(static::MILI_SECONDS, static::SECONDS))) {
			return $units;
		}

		throw new ErrorException('Unsupported units.');
	}
} 