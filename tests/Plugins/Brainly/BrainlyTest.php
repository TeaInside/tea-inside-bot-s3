<?php

namespace Tests\Plugins\Isolator;

require __DIR__."/../../test_init.php";

use PHPUnit\Framework\TestCase;
use Bot\Telegram\Plugins\Brainly\Brainly;

class BrainlyTest extends TestCase
{
	public function testBrainly()
	{
		$query = "siapa penemu lampu?";
		$st = new Brainly($query);
		$st->limit(10);
		$st = $st->exec();
		var_dump($st);
		return true;
	}
}
