<?php

namespace Tests\Languages\LangTest;

require __DIR__."/../test_init.php";
require __DIR__."/json.dummy.php";

use Bot\Telegram\Lang;
use Bot\Telegram\Data;
use PHPUnit\Framework\TestCase;

class LangTest extends TestCase
{
	public function testEnLang()
	{
		$this->assertEquals(
			Lang::get("welcome.set_success"),
			\Bot\Telegram\Lang\En\Fx\Welcome::$map["set_success"]
		);

		$this->assertEquals(
			Lang::get("admin.banned_success", [":admin" => "Admin", ":banned_user" => "User"]),
			"Admin banned User!"
		);
	}

	public function testEntities()
	{
		Lang::init($data = new Data(test_json_2), "en");
		if (isset($data["entities"])) {
			$mentioned_users = [];
			$username_mentions = [];
			foreach($data["entities"] as $ent) {
				if (isset($ent["type"]) && $ent["type"] == "mention") {
					$username_mentions[] = substr($data["text"], $ent["offset"] + 1, $ent["length"]);
				} elseif (isset($ent["type"]) && isset($ent["user"]["id"]) && $ent["type"] == "text_mention") {
					$mentioned_users[] = [
						"user_id" => $ent["user"]["id"],
						"first_name" => $ent["user"]["first_name"],
						"last_name" => $ent["user"]["last_name"],
					];
				}
			}

			$this->assertEquals($username_mentions, ["czxasdPPrt"]);
		} else {
			$this->assertTrue(false);
		}
	}

	public function setUp()
	{
		Lang::init(new Data(test_json_1), "en");
	}
}
