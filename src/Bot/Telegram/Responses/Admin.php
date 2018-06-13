<?php

namespace Bot\Telegram\Responses;

use DB;
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
			
			$name = trim(htmlspecialchars($u["user"]["first_name"], ENT_QUOTES, "UTF-8"));
			if ($name === "") {
				$name = "Unknown";
			}

			if ($u["status"] === "creator") {
				$creatorField = "<a href=\"tg://user?id=".$u["user"]["id"]."\">".$name."</a> (Creator)\n\n";
			} else {
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
		return true;
	}

	public function ban($reason = null)
	{
		$pdo = DB::pdo();
		if (in_array($this->data["user_id"], SUDOERS)) {
			Exe::kickChatMember(
				[
					"chat_id" => $this->data["chat_id"],
					"user_id" => 
				]
			);
		} else {
			$st = $pdo->prepare("SELECT `user_id`,`role` FROM `group_admins` WHERE `group_id`=:group_id LIMIT 1;");
		}
	}
}
