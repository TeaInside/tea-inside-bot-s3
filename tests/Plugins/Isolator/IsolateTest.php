<?php

namespace Tests\Plugins\Isolator;

require __DIR__."/../../test_init.php";

use Isolator;
use PHPUnit\Framework\TestCase;

class IsoaltorTest extends TestCase
{
	public function testIsolate1()
	{
		$st = new Isolator(1);
		$st->run("/bin/echo Hello World");
		$st = trim($st->getStdout());
		$this->assertEquals($st, "Hello World");
	}
}
