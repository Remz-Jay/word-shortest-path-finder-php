<?php namespace wspf;

/**
 * Class WordLibrary
 * Encapsulates an array containing a word library.
 * Can be loaded from file using the `withFileName` Factory.
 *
 * @package wspf
 */
class WordLibrary
{
	/**
	 * @var Array <String>
	 */
	private $wordList;
	private $caseSensitive = true;

	/**
	 * Constructor. Initializes caseSensitivity as true.
	 */
	public function __construct()
	{
		//Enable case sensitive matching by default.
		$this->caseSensitive = true;
	}

	/**
	 * Factory method for loading the WordLibrary from a file.
	 *
	 * @param $fileName - Path to a file containing the library. One word per line.
	 * @return WordLibrary - This, to be chained.
	 * @throws \ErrorException
	 */
	public static function withFileName($fileName)
	{
		$instance = new self();
		$instance->loadByFileName($fileName);
		return $instance;
	}

	/**
	 * Enables CaseInsensitive matching.
	 * Returns object for chaining.
	 * @return $this
	 */
	public function setCaseInsensitive()
	{
		$this->caseSensitive = false;
		return $this;
	}

	/**
	 * Enables CaseSensitive matching.
	 * Returns object for chaining.
	 * @return $this
	 */
	public function setCaseSensitive()
	{
		$this->caseSensitive = true;
		return $this;
	}

	/**
	 * @param $fn
	 * @throws \ErrorException
	 */
	private function loadByFileName($fn)
	{
		if (!file_exists($fn) || !is_file($fn)) {
			throw new \ErrorException('Word Library could not be found');
		}
		$this->wordList = file($fn, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}

	/**
	 * @param $stringLength
	 * @throws \ErrorException
	 */
	public function reduceSetByStringLength($stringLength)
	{
		if (!is_int($stringLength)) {
			throw new \ErrorException('Cannot recude by a non-numeric value');
		}
		foreach ($this->wordList as $key => $value) {
			if (strlen($value) != $stringLength) {
				unset($this->wordList[$key]);
			} elseif (!$this->caseSensitive) {
				//normalize case when we do case insensitive matching.
				$this->wordList[$key] = strtolower($value);
			}
		}
	}

	/**
	 * @return Array
	 */
	public function getWordList()
	{
		return $this->wordList;
	}
}
