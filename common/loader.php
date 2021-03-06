<?php

spl_autoload_register('Loader::loadClass');

class Loader {
	private static $classDirs = array();
	private static $classFiles = array();

	public static function getClassPath($name) {
		return self::$classDirs[$name];
	}

	public static function getClassFile($name) {
		return self::$classFiles[$name];
	}

	public static function loadClass($name) {
		$basePath = self::searchClass($name);
		self::$classDirs[$name] = preg_replace("/^" . str_replace('/', '\/', BASE_PATH) . "/u","",pathinfo($basePath . '.php',PATHINFO_DIRNAME)) . '/';
		self::$classFiles[$name] = preg_replace("/^" . str_replace('/', '\/', BASE_PATH) . "/u","",pathinfo($basePath . '.php',PATHINFO_FILENAME));

		require $basePath . '.php';
	}

	public static function classExists($name) {
		$basepath = self::searchClass($name);

		return is_file($basepath . '.php');
	}

	private static function searchClass($name) {
		if (class_exists('Cache',false) && Cache::isExists('class/' . $name)) {
			return Cache::get('class/' . $name);
		}
		$array = Helper::_toCamelArray($name);
		$basePath = BASE_PATH;
		$prefix = '';
		if (count($array) > 1) {
			switch (end($array)) {
				case 'Page':
					$basePath .= '/page';
					array_pop($array);
					break;
				case 'Dao':
					$basePath .= '/model';
					array_pop($array);
					break;
				case 'Parts':
					$basePath .= '/parts';
					array_pop($array);
					break;
				case 'Helper':
					$basePath .= '/helper';
					array_pop($array);
					break;
				case 'View':
					$basePath .= '/view';
					array_pop($array);
					break;
				case 'Filter':
					$basePath .= '/filter';
					array_pop($array);
					break;
				default:
					$basePath .= '/common';
					break;
			}
		} else {
			$basePath .= '/common';
		}
		$temp = array();
		foreach (array_reverse($array) as $val) {
			$temp[] = strtolower($val);
			if (is_dir($basePath . '/' . implode('_',array_reverse($temp)))) {
				$basePath .= '/';
				$basePath .= strtolower(implode('_',array_reverse($temp)));
				$temp = array();
			}
		}
		$basePath .= '/' . strtolower(implode('_',array_reverse($temp)));
		if (is_dir($basePath)) {
			$basePath .= '/' . strtolower(basename($basePath));
		}
		if (class_exists('Cache',false)) {
			Cache::set('class/' . $name, $basePath);
		}
		return $basePath;
	}
}