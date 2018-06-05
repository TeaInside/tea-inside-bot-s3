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
		$i = $st->reset = $st->run = 1;
		$st->run();
		
		$adminField = $creatorField = "";
		foreach ($st->get() as $u) {
			if ($u["status"] === "creator") {
				$name = trim(htmlspecialchars($u["user"]["first_name"], ENT_QUOTES, "UTF-8"));
				if ($name === "") {
					$name = "Unknown";
				}
				$creatorField = "<a href=\"tg://user?id=".$u["user"]["id"]."\">".$name."</a> (Creator)\n\n";
			} else {
				$name = trim(htmlspecialchars($u["user"]["first_name"], ENT_QUOTES, "UTF-8"));
				if ($name === "") {
					$name = "Unknown";
				}
				$adminField .= ($i++).". <a href=\"tg://user?id=".$u["user"]["id"]."\">".$name."</a>\n";
			}
		}

		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => trim("<b>Admin List:</b>\n".$creatorField.$adminField),
				"parse_mode" => "HTML",
				"reply_to_message_id" => $this->data["msg_id"]
			]
		);
	}
}
