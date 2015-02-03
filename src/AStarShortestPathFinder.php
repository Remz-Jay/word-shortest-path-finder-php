<?php namespace wspf;

/**
 * Class AStarShortestPathFinder
 * @package wspf
 * @copyright (c) 2015 Remco Overdijk <remco@maxserv.com>
 * @license http://www.gnu.org/copyleft/gpl.html. GPLv3
 *
 * Implements a PHP A* algoritm with hamming distance as heuristic.
 * See: http://stackoverflow.com/a/1521973/813718
 * Pseudo Code for A*: http://en.wikipedia.org/wiki/A*_search_algorithm
 *
 * Limitations:
 * - Only one letter may be changed each step
 * - Each (intermediate) word must appear in the loaded dictionary
 * - Current implementation is Case INSensitive -- only lowercase matches.
 */
class AStarShortestPathFinder
{

	/**
	 * @var array - The set of nodes already evaluated.
	 */
	private $closedSet = array();

	/**
	 * @var array - The set of tentative nodes to be evaluated, initially containing the start node.
	 */
	private $openSet = array();

	/**
	 * @var array - The map of navigated nodes.
	 */
	private $cameFrom = array();

	/**
	 * @var array - Cost from start along best known path.
	 */
	private $gScore = array();

	/**
	 * @var array - Estimated total cost from start to goal through y.
	 */
	private $fScore = array();

	/**
	 * @var String - Reference to input string.
	 */
	private $startString;

	/**
	 * @var String - Reference to target string.
	 */
	private $endString;


	/**
	 * @var WordLibrary - The dictionary object we reference intermediates against.
	 */
	private $wl;

	/**
	 * Constructor for AStarShortestPathFinder
	 * @param $library - path to a word library file
	 * @param $start - input string
	 * @param $end - target string
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
	 * Calculates the Hamming Distance between strings A and B
	 * See: http://en.wikipedia.org/wiki/Hamming_distance
	 * @param $a - left side string
	 * @param $b - right side string
	 * @return int - hamming distance in characters
	 */
	private function hammingDistance($a, $b)
	{
		$res = array_diff_assoc(str_split($a), str_split($b));
		return count($res);
	}

	/**
	 * Finds neighbors for a given word based on the dictionary
	 * Can only return direct neighbors with a hamming distance of 1.
	 * @param $input - word to search relatives for
	 * @return array - all neighbors with hamming distance == 1
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
	 * PHP implementation of pseudocode at http://en.wikipedia.org/wiki/A*_search_algorithm
	 * Primary method for this Class. To be called after construction.
	 *
	 * @return array|bool - An array with the path segments on success. False on failure.
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
	 * Traverses the shortest path found once a path has been calculated.
	 * Reverses the path to match in -> out.
	 * @param $cameFrom - Reference to array containing neighbor steps
	 * @param $current - Current step in relation to $cameFrom
	 * @return array - An array with the path segments, ordered from in -> out.
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
