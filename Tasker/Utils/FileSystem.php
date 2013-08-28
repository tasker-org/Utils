<?php
/**
 * Class FileSystem
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Utils;

class FileSystem
{

	/**
	 * @param $dir
	 * @param bool $recursive
	 * @param int $chmod
	 * @param bool $need
	 * @return bool
	 * @throws \ErrorException
	 */
	public static function mkDir($dir, $recursive = true, $chmod = 0777, $need = true)
	{
		$parentDir = $dir;
		while (!is_dir($parentDir)) {
			$parentDir = dirname($parentDir);
		}

		@umask(0000);
		if (!is_dir($dir) && false === ($result = @mkdir($dir, $chmod, $recursive)) && $need) {
			throw new \ErrorException('Unable to create directory ' . $dir);
		}

		if ($dir !== $parentDir) {
			do {
				@umask(0000);
				@chmod($dir, $chmod);
				$dir = dirname($dir);
			} while ($dir !== $parentDir);
		}

		return isset($result) ? $result : true;
	}


	/**
	 * @param $file
	 * @param $contents
	 * @param bool $createDirectory
	 * @param int $chmod
	 * @param bool $need
	 * @return int
	 * @throws \ErrorException
	 */
	public static function write($file, $contents, $createDirectory = true, $chmod = 0777, $need = true)
	{
		$createDirectory && static::mkDir(dirname($file), true, $chmod);

		if (false === ($result = @file_put_contents($file, $contents)) && $need) {
			throw new \ErrorException('File "' . $file . '" not found.');
		}

		@chmod($file, $chmod);
		return $result;
	}


	/**
	 * @param $file
	 * @param bool $need
	 * @return string
	 * @throws \ErrorException
	 */
	public static function read($file, $need = true)
	{
		if (false === ($contents = @file_get_contents($file)) && $need) {
			throw new \ErrorException('File "' . $file . '" is not readable.');
		}

		return $contents;
	}

	/**
	 * @param $source
	 * @param $dest
	 * @param bool $need
	 * @return bool
	 * @throws \ErrorException
	 */
	public static function cp($source, $dest, $need = true)
	{
		if(!file_exists($source) && $need === true) {
			throw new \ErrorException('File "' . $source . '" not found.');
		}

		$file = pathinfo($dest);
		$destFolder = str_replace(DIRECTORY_SEPARATOR . $file['filename'], '', $dest);

		if (!file_exists($destFolder)) {
			static::mkDir($destFolder, true, 0777, $need);
		}

		return copy($source, $dest);
	}

}