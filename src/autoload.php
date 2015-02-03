<?php namespace wspf;

/**
 * SPL autoloader for package wspf.
 * Primarily used for PHPUnit autoloading.
 * @package wspf
 * @copyright (c) 2015 Remco Overdijk <remco@maxserv.com>
 * @license http://www.gnu.org/copyleft/gpl.html. GPLv3
 */
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

	/** @noinspection PhpIncludeInspection */
	@include_once 'src/' . end(explode('\\', $class)) . '.php';
});
