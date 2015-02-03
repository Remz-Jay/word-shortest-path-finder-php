<?php
require_once('WordLibrary.php');

class AStarShortestPathFinder
{

	private $closedSet = array();

	private $openSet = array();

	private $cameFrom = array();

	private $gScore = array();

	private $fScore = array();

	/**
	 * @var String
	 */
	private $startString;

	/**
	 * @var String
	 */
	private $endString;


	/**
	 * @var WordLibrary
	 */
	private $wl;

	public function __construct($library, $start, $end)
	{
		if(!is_string($start) || strlen($start) < 2) throw new ErrorException('$start is not a valid string');
		if(!is_string($end) || strlen($end) < 2) throw new ErrorException('$end is not a valid string');
		if(strlen($start) !== strlen($end)) throw new LengthException('This program expects both strings to be of equal length.');
		$this->startString = $start;
		$this->endString = $end;
		$this->wl = WordLibrary::withFileName($library);
		$this->wl->reduceSetByStringLength(strlen($start));
		$this->gScore[$start] = 0;
		$this->fScore[$start] = $this->gScore[$start] + $this->HammingDistance($start, $this->endString);
		$this->openSet[] = $start;
	}

	private function HammingDistance($a, $b)
	{
		$res = array_diff_assoc(str_split($a), str_split($b));
		return count($res);
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

	/**
	 * PHP implementation of http://en.wikipedia.org/wiki/A*_search_algorithm
	 */
	public function go() {
		while(count($this->openSet)>0) {
			$current = false;
			$currentFScore = false;
			foreach($this->openSet as $node) {
				$nodeFScore = $this->gScore[$node] + $this->HammingDistance($node, $this->endString);
				if($current === false || $nodeFScore < $currentFScore) {
					$current = $node;
					$currentFScore = $nodeFScore;
				}
			}
			if($current == $this->endString) {
				return $this->reconstructPath($this->cameFrom, $current);
			}
			unset($this->openSet[array_search($current,$this->openSet)]);
			$this->closedSet[] = $current;
			foreach($this->findRelativesFor($current) as $neighbor) {
				if(in_array($neighbor, $this->closedSet)) continue;
				$tentativeGScore = $this->gScore[$current] + $this->HammingDistance($current, $neighbor);
				if(!in_array($neighbor, $this->openSet) || $tentativeGScore < $this->gScore[$neighbor]) {
					$this->cameFrom[$neighbor] = $current;
					$this->gScore[$neighbor] = $tentativeGScore;
					$this->fScore[$neighbor] = $tentativeGScore + $this->HammingDistance($neighbor, $this->endString);
					if(!in_array($neighbor, $this->openSet)) $this->openSet[] = $neighbor;
				}
			}
		}
	}

	private function reconstructPath(&$cameFrom, $current) {
		$totalPath = array($current);
		while(array_key_exists($current, $cameFrom)) {
			$current = $cameFrom[$current];
			$totalPath[] = $current;
		}
		return array_reverse($totalPath);
	}
}