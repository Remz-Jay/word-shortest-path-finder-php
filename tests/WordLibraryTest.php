<?php namespace wspf;

/**
 * Class WordLibraryTest
 * @package wspf
 */
class WordLibraryTest extends \PHPUnit_Framework_TestCase
{
	public function testCanBeLoadedFromFile()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);

		$a = WordLibrary::withFileName($fn);
		$this->assertInstanceOf('wspf\WordLibrary', $a);
		$this->assertGreaterThan(1, count($a->getWordList()));
	}
	public function testCannotBeLoadedFromBogusFile()
	{
		$fn = '/does/not/exist';
		$this->assertFileNotExists($fn);
		$this->setExpectedException('ErrorException', 'Word Library could not be found');
		WordLibrary::withFileName($fn);
	}
	public function testCannotBeLoadedFromDirectory()
	{
		$fn = '/usr/local';
		$this->assertFileExists($fn);
		$this->setExpectedException('ErrorException', 'Word Library could not be found');
		WordLibrary::withFileName($fn);
	}
}
