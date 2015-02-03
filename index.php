<?php
require_once('src/ShortestPathFinder.php');
$spf = new ShortestPathFinder('/usr/share/dict/words', 'flux', 'alem');
$spf->go();
