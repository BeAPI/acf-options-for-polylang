<?php

namespace BEA\ACF_Options_For_Polylang;

trait Singleton {

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * @return self
	 */
	final public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Constructor protected from the outside
	 */
	final private function __construct() {
		$this->init();
	}

	/**
	 * Add init function by default
	 * Implement this method in your child class
	 * If you want to have actions send at construct
	 */
	protected function init() {
	}

	/**
	 * prevent the instance from being cloned
	 *
	 * @return void
	 */
	private function __clone() {
	}

	/**
	 * prevent from being unserialized
	 *
	 * @return void
	 */
	public function __wakeup() {
	}
}
