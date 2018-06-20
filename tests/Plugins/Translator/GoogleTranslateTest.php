<?php

namespace Tests\Plugins\Translator;

use PHPUnit\Framework\TestCase;
use Bot\Telegram\Plugins\Translators\GoogleTranslate;

class GoogleTranslateTest extends TestCase
{
	private static function tr($text, $from, $to)
	{
		$st	= new GoogleTranslate($text, $from, $to);
		return trim($st->exec());
	}

	public function testTranslate1()
	{
		$this->assertEquals(
			self::tr("Selamat pagi", "id", "en"), "Good morning"
		);
	}
}
