<?php
abstract class Filter extends Base {
	private static $page;

	public static function setPage($page) {
		self::$page = $page;
	}

	public static function get() {
		return new static();
	}

	public static function before() {
		if (is_file(BASE_PATH . '/config/filters_before.php')) {
			require BASE_PATH . '/config/filters_before.php';
		}
	}

	public static function after() {
		if (is_file(BASE_PATH . '/config/filters_after.php')) {
			require BASE_PATH . '/config/filters_after.php';
		}
	}

	private $enable = true;

	public function acceptByPath($path) {
		if (strpos($_REQUEST['q'],$path) !== 0) {
			$this->enable = false;
		}
		return $this;
	}

	public function acceptByProperty($name,$value) {
		if (!self::$page || !property_exists(self::$page, $name) || self::$page->$$name != $value) {
			$this->enable = false;
		}
		return $this;
	}

	public function rejectByPath($path) {
		if (strpos($_REQUEST['q'],$path) === 0) {
			$this->enable = false;
		}
		return $this;
	}

	public function rejectByProperty($name,$value) {
		if (self::$page && property_exists(self::$page, $name) && self::$page->$$name == $value) {
			$this->enable = false;
		}
		return $this;
	}

	public function apply() {
		if ($this->enable) {
			$this->filter();
		}
	}

	public function filter() {

	}
}