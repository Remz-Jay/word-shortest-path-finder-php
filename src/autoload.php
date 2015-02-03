<?php
spl_autoload_register(function ($class) {
	/**
	 * Usually we would use `require_once`, but PHPUnit has a nasty habit of aggresively looking
	 * for Extensions, which results in:
	 *
	 * `Fatal error: require_once(): Failed opening required 'src/PHPUnit_Extensions_Story_TestCase.php'`
	 *
	 * So we silenty ignore any non-loadable classes.
	 * The testSuite will fail anyway if anything essential is missing.
	 * */

	@include_once 'src/' . $class . '.php';
});