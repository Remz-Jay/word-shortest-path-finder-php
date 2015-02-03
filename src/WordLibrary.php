<?php

class WordLibrary
{
	/**
	 * @var Array <String>
	 */
	private $wordList;

	public function __construct() {

	}

	public static function withFileName($fileName) {
		$instance = new self();
		$instance->loadByFileName($fileName);
		return $instance;
	}
	private function loadByFileName($fn) {
		if(!file_exists($fn) || !is_file($fn)) throw new ErrorException('Word Library could not be found');
		$this->wordList = file($fn, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}
	public function reduceSetByStringLength($stringLength) {
		if(!is_int($stringLength)) throw new ErrorException('Cannot recude by a non-numeric value');
		foreach($this->wordList as $key=>$value) {
			if(strlen($value) != $stringLength) unset($this->wordList[$key]);
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