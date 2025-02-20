<?php
/**
 * Abstract class for Qalas classes.
 * It's purpose is simply to make it easier to create new classes in Qala.
 *
 * @package Qala
 */

namespace DtRacing;

abstract class Qala_Abstract {

	/**
	 * The class constructor.
	 * This will run automatically in the classes extending Qala_Abstract as long as they don't define their own __construct().
	 * If you need a constructor in your class this one can be called by parent::__construct();
	 *
	 * @return void
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Require extending classes to have an init function.
	 *
	 * @return void
	 */
	abstract protected function init();
}
