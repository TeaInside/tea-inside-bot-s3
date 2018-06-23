<?php

namespace Tests\Languages\LangTest;

require __DIR__."/../test_init.php";
require __DIR__."/json.dummy.php";

use Bot\Telegram\Lang;
use Bot\Telegram\Data;
use PHPUnit\Framework\TestCase;

class LangTest extends TestCase
{
	public function setUp()
	{
		Lang::init(new Data(test_json), "en");
	}

	public function testEnLang()
	{
		// $this->assertEquals(
		// 	Lang::get("welcome.set_success"),
		// 	\Bot\Telegram\Lang\En\Fx\Welcome::$map["set_success"]
		// );

		$this->assertEquals(
			Lang::get("admin.banned_success", [":admin" => "Admin", ":banned_user" => "User"]),
			"Admin banned User!"
		);
	}
}
