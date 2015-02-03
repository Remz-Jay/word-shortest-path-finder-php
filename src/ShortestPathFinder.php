<?php
require_once('src/WordLibrary.php');

class ShortestPathFinder {
	/**
	 * @var WordLibrary
	 */
	private $wl;

	/**
	 * @var String
	 */
	private $startString;

	/**
	 * @var String
	 */
	private $endString;

	/**
	 * @var Array
	 */
	private $globalHits = array();

	/**
	 * @var int
	 */
	private $shortestPathFound = null;

	/**
	 * @var array
	 */
	private $paths = array();

	public function __construct($library, $start, $end) {
		$this->wl = WordLibrary::withFileName($library);
		if(!is_string($start) || strlen($start) < 2) throw new ErrorException('$start is not a valid string');
		if(!is_string($end) || strlen($end) < 2) throw new ErrorException('$end is not a valid string');
		if(strlen($start) !== strlen($end)) throw new LengthException('This program expects both strings to be of equal length.');
		$this->startString = $start;
		$this->endString = $end;
		$this->wl->reduceSetByStringLength(strlen($start));
		array_push($this->globalHits, $start, $end);
	}

	private function findRelativesFor($input) {
		$close = array();
		foreach($this->wl->getWordList() as $w) {
			if($this->HammingDistance($input, $w) === 1) {
				$close[] = $w;
			}
		}
		return $close;
	}

	private function doRound($input, $callStack = array()) {
		$callStack[] = $input;
		if (is_string($input)) {
			$matches = $this->findRelativesFor($input);
			if(in_array($this->endString, $matches)) {
				$callStack[] = $this->endString;
				//we found an out. Calculate the path length
				$this->paths[] = $callStack;
				if($this->shortestPathFound === null || (count($callStack)<$this->shortestPathFound)) {
					$this->shortestPathFound = count($callStack);
				}
				return true;
			}
			$trueMatches = array_diff($matches, $this->globalHits);
			$this->globalHits = array_merge($this->globalHits, $trueMatches);

			//echo "CallStack:" . var_export($callStack, true);
			//echo "Hits:" . var_export($trueMatches, true);
			$hdtt = null;
			$wordsToUse = array();
			foreach($trueMatches as $m) {
				//calculate the Hamming distance to the target and only use the closest match to continue.
				$x = $this->HammingDistance($m, $this->endString);
				if ($hdtt === null || $x < $hdtt) {
					//shorter match found, use this instead
					$hdtt = $x;
					$wordsToUse = array($m);
				} else if ($x == $hdtt) {
					//same length, add to words to use
					$wordsToUse[] = $m;
				}
                foreach($wordsToUse as $w) {
	                //do another round if this route isn't any longer than we've already found
	                if($this->shortestPathFound === null || count($callStack)+1 <= $this->shortestPathFound) {
		                //if($this->doRound($m, $callStack)) return true;
		                $this->doRound($m, $callStack);
	                }
                }
			}
			return false;
		} else {
			return false;
		}
	}

	public function go() {
		$this->doRound($this->startString);
		echo PHP_EOL;
		echo "Shortest path found:" . $this->shortestPathFound . PHP_EOL;
		echo "=====================" . PHP_EOL;
		foreach($this->paths as $p) {
			if(count($p) == $this->shortestPathFound) {
				var_dump($p);
				echo PHP_EOL;
			}
		}
	}

	private function HammingDistance($a, $b) {
		$res = array_diff_assoc(str_split($a), str_split($b));
		return count($res);
	}
}