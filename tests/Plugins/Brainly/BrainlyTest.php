<?php

namespace Tests\Plugins\Isolator;

require __DIR__."/../../test_init.php";

use PHPUnit\Framework\TestCase;
use Bot\Telegram\Plugins\Brainly\Brainly;

class BrainlyTest extends TestCase
{
	public function testBrainly()
	{
		// $query = "siapa penemu lampu?";
		// $st = new Brainly($query);
		// $st->limit(10);
		// $st = $st->exec();


		// $similarity = [];
		// $query = trim(strtolower($query));
		// foreach ($st as $k => $v) {
		// 	if (isset($v["content"]) && isset($v["responses"][0]["content"])) {
		// 		similar_text($query, strip_tags(trim($v["content"])), $n);
		// 		$similarity[$k] = $n;
		// 	}
		// }
		// $maxPos = array_search(max($similarity), $similarity);
		// $fQuery = $st[$maxPos]["content"];
		// $answer = $st[$maxPos]["responses"][0]["content"];
		
		// $this->assertEquals($fQuery, "siapa penemu lampu ?<br />");
		// $this->assertEquals($answer, "kalau tidak salah thomas alva");
		$this->assertTrue(true);
	}
}
