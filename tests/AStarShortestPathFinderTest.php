<?php namespace wspf;

/**
 * Class AStarShortestPathFinderTest
 * @package wspf
 */
class AStarShortestPathFinderTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @param $expectedResult
	 * @throws \ErrorException
	 */
	private function runWordTest($expectedResult)
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);
		//as the algorithm is dictionary based, fist check if the set being tested is valid
		$wl = WordLibrary::withFileName($fn)->setCaseInsensitive();
		$startStringLength = strlen(current($expectedResult));
		$wl->reduceSetByStringLength($startStringLength);
		reset($expectedResult);
		foreach ($expectedResult as $word) {
			$this->assertEquals($startStringLength, strlen($word));
			$this->assertContains($word, $wl->getWordList());
		}
		reset($expectedResult);
		$spf = new AStarShortestPathFinder($fn, current($expectedResult), end($expectedResult));
		$output = $spf->go();
		if ($_ENV['PHPUNIT_LOOSE']) {
			//only check if our result is 'equal or better' compared to the example
			$this->assertGreaterThan(1, count($output));
			$this->assertLessThanOrEqual(count($expectedResult), count($output));
		} else {
			//check if we have actually found the shortest path
			$this->assertCount(count($expectedResult), $output);
			//check if we have the same shortest path as the solution predicts.
			$this->assertEquals($expectedResult, $output);
		}
	}

	public function testCannotBeLoadedWithoutLibrary()
	{
		$fn = '/does/not/exist';
		$this->assertFileNotExists($fn);
		$this->setExpectedException('ErrorException', 'Word Library could not be found');
		new AStarShortestPathFinder($fn, 'gogo', 'stop');
	}

	public function testCannotBeLoadedWithoutStartString()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);
		$this->setExpectedException('ErrorException', '$start is not a valid string');
		new AStarShortestPathFinder($fn, null, 'stop');
	}
	public function testCannotBeLoadedWithoutEndString()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);
		$this->setExpectedException('ErrorException', '$end is not a valid string');
		new AStarShortestPathFinder($fn, 'gogo', null);
	}
	public function testCannotBeLoadedWithDifferentStringLengths()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);
		$this->setExpectedException('LengthException', 'This program expects both strings to be of equal length.');
		new AStarShortestPathFinder($fn, 'start', 'stop');
	}

	public function testCanFindAdjacentPath()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);
		//Test a path with a hamming distance of 1, which is adjacent, and should return immediately.
		$spf = new AStarShortestPathFinder($fn, 'if', 'in');
		$result = $spf->go();
		$this->assertCount(2, $result);
		$this->assertEquals(array('if', 'in'), $result);
	}

	public function testCanFindIntermediatePath()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);
		//Test a path with a hamming distance of 2, with one predicatable intermediate.
		$spf = new AStarShortestPathFinder($fn, 'bar', 'boo');
		$result = $spf->go();
		$this->assertCount(3, $result);
		$this->assertThat(
			$result,
			$this->equalTo(array('bar', 'bor', 'boo')),
			$this->logicalOr(
				$this->equalTo(array('bar', 'bao', 'boo'))
			)
		);
		$this->assertEquals(array('bar', 'bor', 'boo'), $result);
	}

	public function testCannotFindBogusWords()
	{
		$fn = '/usr/share/dict/words';
		$this->assertFileExists($fn);
		$spf = new AStarShortestPathFinder($fn, 'gg', 'hh');
		$result = $spf->go();
		$this->assertFalse($result);
	}

	public function testCanFindFluxAlem()
	{
		$expectedResult = array('flux', 'flex', 'flem', 'alem');
		$this->runWordTest($expectedResult);
	}
	public function testCanFindRialFoud()
	{
		$expectedResult = array('rial', 'real', 'feal', 'foal', 'foul', 'foud');
		$this->runWordTest($expectedResult);
	}
	public function testCanFindDungGeez()
	{
		$expectedResult = array('dung', 'dunt', 'dent', 'gent', 'geet', 'geez');
		$this->runWordTest($expectedResult);
	}
	public function testCanFindDoegSick()
	{
		$expectedResult = array('doeg', 'dong', 'song', 'sing', 'sink', 'sick');
		$this->runWordTest($expectedResult);
	}
	public function testCanFindJehuGuha()
	{
		$expectedResult = array('jehu', 'jesu', 'jest', 'gest', 'gent', 'gena', 'guna', 'guha');
		$this->runWordTest($expectedResult);
	}
	public function testCanFindBrooImmi()
	{
		$expectedResult = array('broo', 'brod', 'brad', 'arad', 'adad', 'adai', 'admi', 'ammi', 'immi');
		$this->runWordTest($expectedResult);
	}
	public function testCanFindYagiBlup()
	{
		$expectedResult = array('yagi', 'yali', 'pali', 'palp', 'paup', 'plup', 'blup');
		$this->runWordTest($expectedResult);
	}
	public function testCanFindBittMeum()
	{
		$expectedResult = array('bitt', 'butt', 'burt', 'bert', 'berm', 'germ', 'geum', 'meum');
		$this->runWordTest($expectedResult);
	}
	public function testCanFindJinaPray()
	{
		$expectedResult = array('jina', 'pina', 'pint', 'pent', 'peat', 'prat','pray');
		$this->runWordTest($expectedResult);
	}
	public function testCanFindFikeCamp()
	{
		$expectedResult = array('fike', 'fire', 'fare', 'care', 'carp', 'camp');
		$this->runWordTest($expectedResult);
	}
}
