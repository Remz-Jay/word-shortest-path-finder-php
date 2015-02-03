<?php

class WordLibrary
{
	/**
	 * @var Array <String>
	 */
	private $wordList;
	private $caseSensitive = true;

	public function __construct() {
		//Enable case sensitive matching by default.
		$this->caseSensitive = true;
	}

	public static function withFileName($fileName) {
		$instance = new self();
		$instance->loadByFileName($fileName);
		return $instance;
	}
	public function setCaseInsensitive() {
		$this->caseSensitive = false;
		return $this;
	}
	public function setCaseSensitive() {
		$this->caseSensitive = true;
		return $this;
	}
	private function loadByFileName($fn) {
		if(!file_exists($fn) || !is_file($fn)) throw new ErrorException('Word Library could not be found');
		$this->wordList = file($fn, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}
	public function reduceSetByStringLength($stringLength) {
		if(!is_int($stringLength)) throw new ErrorException('Cannot recude by a non-numeric value');
		foreach($this->wordList as $key=>$value) {
			if(strlen($value) != $stringLength) {
				unset($this->wordList[$key]);
			} else if (!$this->caseSensitive) {
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