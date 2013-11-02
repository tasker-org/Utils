<?php
/**
 * Class Metrics
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 02.11.13
 */
namespace Tasker\Utils;

class Metrics
{

	/**
	 * @param $bytes
	 * @param int $precision
	 * @return string
	 */
	public static function formatBytes($bytes, $precision = 2)
	{
		$base = log($bytes) / log(1024);
		$suffixes = array('', 'k', 'M', 'G', 'T');

		return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
	}
} 