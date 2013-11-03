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
	 * @param $file
	 * @param bool $need
	 * @return bool
	 * @throws \Exception
	 */
	public static function rm($file, $need = true)
	{
		if (is_dir((string)$file)) {
			return static::rmDir($file, false, $need);
		}

		if (false === ($result = @unlink((string)$file)) && $need) {
			throw new \Exception("Unable to delete file '$file'");
		}

		return $result;
	}


	/**
	 * @param $dir
	 * @param bool $recursive
	 * @param bool $need
	 * @return bool
	 * @throws \Exception
	 */
	public static function rmDir($dir, $recursive = true, $need = true)
	{
		$recursive && self::cleanDir($dir = (string)$dir, $need);
		if (is_dir($dir) && false === ($result = @rmdir($dir)) && $need) {
			throw new \Exception("Unable to delete directory '$dir'.");
		}

		return isset($result) ? $result : true;
	}

	/**
	 * @param string $dir
	 * @param bool   $need
	 *
	 * @return bool
	 */
	public static function cleanDir($dir, $need = true)
	{
		if (!file_exists($dir)) {
			return true;
		}

		foreach (new \RecursiveDirectoryIterator($dir) as $file) {
			if (false === static::rm($file, $need)) {
				return false;
			}
		}

		return true;
	}

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
	 * @return void
	 * @throws \ErrorException
	 */
	public static function cp($source, $dest, $need = true)
	{
		if(!file_exists($source) && $need === true) {
			throw new \ErrorException('File "' . $source . '" not found.');
		}

		$dir = opendir($source);
		static::mkDir($dest);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($source . DIRECTORY_SEPARATOR . $file) ) {
					static::cp($source . DIRECTORY_SEPARATOR . $file,$dest . DIRECTORY_SEPARATOR . $file);
				}
				else {
					copy($source . DIRECTORY_SEPARATOR . $file,$dest . DIRECTORY_SEPARATOR . $file);
				}
			}
		}

		closedir($dir);
	}

}