<?php namespace wspf;

/**
 * Class AStarShortestPathFinder
 * @package wspf
 */
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

	/**
	 * @param $library
	 * @param $start
	 * @param $end
	 * @throws \ErrorException
	 * @throws \LengthException
	 */
	public function __construct($library, $start, $end)
	{
		$startTime = microtime(true);
		if (!is_string($start) || strlen($start) < 2) {
			throw new \ErrorException('$start is not a valid string');
		}
		if (!is_string($end) || strlen($end) < 2) {
			throw new \ErrorException('$end is not a valid string');
		}
		if (strlen($start) !== strlen($end)) {
			throw new \LengthException('This program expects both strings to be of equal length.');
		}
		$this->startString = $start;
		$this->endString = $end;
		$this->wl = WordLibrary::withFileName($library)->setCaseInsensitive();
		$this->wl->reduceSetByStringLength(strlen($start));
		$this->gScore[$start] = 0;
		$this->fScore[$start] = $this->gScore[$start] + $this->hammingDistance($start, $this->endString);
		$this->openSet[] = $start;
		$endTime = microtime(true);
		$execTime = ($endTime - $startTime);
		if ($_ENV['VERBOSE']) {
			echo 'A*SPF setup took: ' . $execTime . ' seconds.' . PHP_EOL;
		}
	}

	/**
	 * @param $a
	 * @param $b
	 * @return int
	 */
	private function hammingDistance($a, $b)
	{
		$res = array_diff_assoc(str_split($a), str_split($b));
		return count($res);
	}

	/**
	 * @param $input
	 * @return array
	 */
	private function findRelativesFor($input)
	{
		$close = array();
		foreach ($this->wl->getWordList() as $w) {
			if ($this->hammingDistance($input, $w) === 1) {
				$close[] = $w;
			}
		}
		return $close;
	}

	/**
	 *
	 * PHP implementation of http://en.wikipedia.org/wiki/A*_search_algorithm
	 *
	 * @return array|bool
	 */
	public function go()
	{
		$startTime = microtime(true);
		while (count($this->openSet)>0) {
			$current = false;
			$currentFScore = false;
			foreach ($this->openSet as $node) {
				$nodeFScore = $this->gScore[$node] + $this->hammingDistance($node, $this->endString);
				if ($current === false || $nodeFScore < $currentFScore) {
					$current = $node;
					$currentFScore = $nodeFScore;
				}
			}
			if ($current == $this->endString) {
				$path = $this->reconstructPath($this->cameFrom, $current);
				$endTime = microtime(true);
				$execTime = ($endTime - $startTime);

				if ($_ENV['VERBOSE']) {
					$format = 'Finding a path from %s to %s took %f seconds. %d elements in path: %s.';
					echo sprintf(
						$format,
						$this->startString,
						$this->endString,
						$execTime,
						count($path),
						implode(' → ', $path)
					) . PHP_EOL;
				}
				return $path;
			}
			unset($this->openSet[array_search($current, $this->openSet)]);
			$this->closedSet[] = $current;
			foreach ($this->findRelativesFor($current) as $neighbor) {
				if (in_array($neighbor, $this->closedSet)) {
					continue;
				}
				$tentativeGScore = $this->gScore[$current] + $this->hammingDistance($current, $neighbor);
				if (!in_array($neighbor, $this->openSet) || $tentativeGScore < $this->gScore[$neighbor]) {
					$this->cameFrom[$neighbor] = $current;
					$this->gScore[$neighbor] = $tentativeGScore;
					$this->fScore[$neighbor] = $tentativeGScore + $this->hammingDistance($neighbor, $this->endString);
					if (!in_array($neighbor, $this->openSet)) {
						$this->openSet[] = $neighbor;
					}
				}
			}
		}
		$endTime = microtime(true);
		$execTime = ($endTime - $startTime);

		if ($_ENV['VERBOSE']) {
			$format = 'It took %f seconds to figure out that no path exists from %s to %s';
			echo sprintf(
				$format,
				$execTime,
				$this->startString,
				$this->endString
			). PHP_EOL;
		}
		return false;
	}

	/**
	 * @param $cameFrom
	 * @param $current
	 * @return array
	 */
	private function reconstructPath(&$cameFrom, $current)
	{
		$totalPath = array($current);
		while (array_key_exists($current, $cameFrom)) {
			$current = $cameFrom[$current];
			$totalPath[] = $current;
		}
		return array_reverse($totalPath);
	}
}
