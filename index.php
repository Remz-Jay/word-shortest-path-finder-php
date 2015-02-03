<?php
require_once('src/autoload.php');
$asspf = new AStarShortestPathFinder('/usr/share/dict/words', 'flux', 'alem');
var_dump($asspf->go());