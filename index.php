<?php
/**
 * @copyright (c) 2015 Remco Overdijk <remco@maxserv.com>
 * @license http://www.gnu.org/copyleft/gpl.html. GPLv3
 *
 * Bootstrapper for the wspf package. Nothing fancy here.
 * Best results are achieved by ignoring this file and running the phpunit tests instead.
 */
require_once('src/autoload.php');
$asspf = new AStarShortestPathFinder('/usr/share/dict/words', 'flux', 'alem');
var_dump($asspf->go());
