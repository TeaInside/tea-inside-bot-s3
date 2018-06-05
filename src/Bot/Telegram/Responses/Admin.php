<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Logger\AdminLogger;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class Admin extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function show()
	{
		$st = new AdminLogger($this->data);
		$st->run = 1;
		$st->reset = 1;
		$st->run();
		$data = $st->get();
		$r1 = [chr(226), chr(225)];
		$r2 = ["", ""];
		$adminCount = 1;
		$adminField = $creatorField = "";
		foreach ($data as $u) {
			if ($u["status"] === "creator") {
				$creatorField = "<a href=\"tg://user?id=".$u["user"]["id"]."\">".
					htmlspecialchars(str_replace($r1, $r2, $u["user"]["first_name"], ENT_QUOTES, "UTF-8")).
					"</a> (Creator)\n";
			} else {
				$adminField .= "<a href=\"tg://user?id=".$u["user"]["id"]."\">".
					htmlspecialchars(str_replace($r1, $r2, $u["user"]["first_name"], ENT_QUOTES, "UTF-8")).
					"</a>\n";
			}
		}

		$reply = trim("<b>Admin List:</b>\n".$creatorField.$adminField);

		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => $reply,
				"parse_mode" => "HTML",
				"reply_to_message_id" => $this->data["msg_id"]
			]
		);
	}
}
