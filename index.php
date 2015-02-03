<?php
require_once('src/AStarShortestPathFinder.php');
$asspf = new AStarShortestPathFinder('/usr/share/dict/words', 'flux', 'alem');
var_dump($asspf->go());
