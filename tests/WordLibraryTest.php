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
	public function testCanSetCaseInsensitive()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);

		$a = WordLibrary::withFileName($fn)->setCaseInsensitive();
		$this->assertInstanceOf('wspf\WordLibrary', $a);
		$this->assertFalse($a->isCaseSensitive());
	}
	public function testCanSetCaseSensitive()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);

		$a = WordLibrary::withFileName($fn)->setCaseSensitive();
		$this->assertInstanceOf('wspf\WordLibrary', $a);
		$this->assertTrue($a->isCaseSensitive());
	}
	public function testCanReduceByStringLength()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);

		$a = WordLibrary::withFileName($fn);
		$a->reduceSetByStringLength(5);
	}
	public function testCannotReduceByString()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);

		$a = WordLibrary::withFileName($fn);
		$this->setExpectedException('ErrorException', 'Cannot recude by a non-numeric value');
		$a->reduceSetByStringLength('failure');
	}
	public function testCannotReduceByNull()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);

		$a = WordLibrary::withFileName($fn);
		$this->setExpectedException('ErrorException', 'Cannot recude by a non-numeric value');
		$a->reduceSetByStringLength(null);
	}
	public function testCannotReduceByBoolean()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);

		$a = WordLibrary::withFileName($fn);
		$this->setExpectedException('ErrorException', 'Cannot recude by a non-numeric value');
		$a->reduceSetByStringLength(true);
	}
	public function testCannotReduceByFloat()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);

		$a = WordLibrary::withFileName($fn);
		$this->setExpectedException('ErrorException', 'Cannot recude by a non-numeric value');
		$a->reduceSetByStringLength(2.337);
	}
}
