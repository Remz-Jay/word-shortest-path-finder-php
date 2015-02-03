<?php
/**
 * @copyright (c) 2015 Remco Overdijk <remco@maxserv.com>
 * @license http://www.gnu.org/copyleft/gpl.html. GPLv3
 *
 * Bootstrapper for the wspf package. Nothing fancy here.
 * Best results are achieved by ignoring this file and running the phpunit tests instead.
 */
require_once('src/autoload.php');
ini_set('variables_order', 'EGPCS');
$_ENV['VERBOSE'] = true;
$asspf = new wspf\AStarShortestPathFinder('/usr/share/dict/words', 'zq', 'qz');
var_dump($asspf->go());
