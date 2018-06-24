<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
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
				foreach ($this->data["new_chat_members"] as $v) {

					$r1 = [
						"{user_id}",
						"{name}",
						"{name_link}",
						"{username}",
						"{first_name}",
						"{last_name}",
						"{group_id}",
						"{group_name}",
						"{group_name_link}",
						"{group_username}"
					];

					$r2 = [
						$v["id"],
						htmlspecialchars($v["first_name"].(isset($v["last_name"]) ? " ".$v["last_name"] : ""), ENT_QUOTES, "UTF-8"),
						Lang::namelink($v["id"], $v["first_name"].(isset($v["last_name"]) ? " ".$v["last_name"] : "")),
						isset($v["username"]) ? $v["username"] : "",
						htmlspecialchars($v["first_name"], ENT_QUOTES, "UTF-8"),
						isset($v["last_name"]) ? htmlspecialchars($v["last_name"], ENT_QUOTES, "UTF-8") : "",
						$this->data["chat_id"],
						htmlspecialchars($this->data["group_name"], ENT_QUOTES, "UTF-8"),
						isset($this->data["group_username"]) ? "<a href=\"https://t.me/".$this->data["group_username"]."\">".htmlspecialchars($this->data["group_name"], ENT_QUOTES, "UTF-8")."</a>"
						$this->data["group_username"]
					];

					Exe::sendMessage(
						[
							"chat_id" => $this->data["chat_id"],
							"text" => str_replace($r1, $r2, $st[0]),
							"parse_mode" => "HTML",
							"reply_to_message_id" => $this->data["msg_id"]
						]
					);
				}
			}
		}
		return true;
	}
}
