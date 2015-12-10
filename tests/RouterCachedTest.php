<?php
namespace vakata\routerCached\test;

class RouterCachedTest extends \PHPUnit_Framework_TestCase
{
	protected static $dir = null;
	protected static $cache = null;
	protected static $routerCached = null;

	public static function setUpBeforeClass() {
		self::$dir = __DIR__ . '/cache';
		mkdir(self::$dir);
		self::$cache = new \vakata\cache\Filecache(self::$dir);
		self::$cache->clear();
		self::$cache->clear('test');
	}
	public static function tearDownAfterClass() {
		self::$cache->clear();
		self::$cache->clear('test');
		rmdir(self::$dir.'/default');
		rmdir(self::$dir);
	}
	protected function setUp() {
	}
	protected function tearDown() {
	}

	public function testCreate() {
		self::$routerCached = new \vakata\routerCached\RouterCached(self::$cache);
		$this->assertEquals(true, self::$routerCached->isEmpty());
		self::$routerCached->get('/get1', function () { return 1; });
		$this->assertEquals(1, self::$routerCached->run('get1'));
		self::$routerCached->get('/get1', function () { return 2; });
		$this->assertEquals(1, self::$routerCached->run('get1'));
	}
	/**
	 * @depends testCreate
	 */
	public function testTimeout() {
		self::$routerCached = new \vakata\routerCached\RouterCached(self::$cache, 2);
		self::$routerCached->get('/get2', function () { return 3; });
		$this->assertEquals(3, self::$routerCached->run('get2'));
		self::$routerCached->get('/get2', function () { return 4; });
		$this->assertEquals(3, self::$routerCached->run('get2'));
		sleep(3);
		$this->assertEquals(4, self::$routerCached->run('get2'));
	}
	/**
	 * @depends testCreate
	 */
	public function testVerbs() {
		self::$routerCached = new \vakata\routerCached\RouterCached(self::$cache, 30, null, [ 'POST' ]);
		self::$routerCached->get('/get3', function () { return 5; });
		$this->assertEquals(5, self::$routerCached->run('get3'));
		self::$routerCached->get('/get2', function () { return 6; });
		$this->assertEquals(6, self::$routerCached->run('get2'));
	}
}
