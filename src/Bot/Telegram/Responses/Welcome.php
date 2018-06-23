<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class Welcome extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function run()
	{
		$pdo = DB::pdo();

		$st = $pdo->prepare(
			"SELECT `welcome_message`,`mute` FROM `group_settings` WHERE `group_id`=:group_id LIMIT 1;"
		);
		$st->execute([":group_id" => $this->data["chat_id"]]);
		if ($st = $st->fetch(PDO::FETCH_NUM)) {
			if ($st[0] !== null && $st[1] !== "on") {
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => $st[0],
						"parse_mode" => "HTML",
						"reply_to_message_id" => $this->data["msg_id"]
					]
				);
			}
		}
		var_dump($st);
		return true;
	}
}
