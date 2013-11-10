<?php
/**
 * Class Randomizer
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 10.11.13
 */
namespace Tasker\Utils;

class Randomizer
{

	/**
	 * Generate random string.
	 * @param  int
	 * @param  string
	 * @return string
	 */
	public static function generate($length = 10, $charlist = '0-9a-z')
	{
		$charlist = str_shuffle(preg_replace_callback('#.-.#', function($m) {
			return implode('', range($m[0][0], $m[0][2]));
		}, $charlist));
		$chLen = strlen($charlist);

		static $rand3;
		if (!$rand3) {
			$rand3 = md5(serialize($_SERVER), TRUE);
		}

		$s = '';
		for ($i = 0; $i < $length; $i++) {
			if ($i % 5 === 0) {
				list($rand, $rand2) = explode(' ', microtime());
				$rand += lcg_value();
			}
			$rand *= $chLen;
			$s .= $charlist[($rand + $rand2 + ord($rand3[$i % strlen($rand3)])) % $chLen];
			$rand -= (int) $rand;
		}
		return $s;
	}
} 