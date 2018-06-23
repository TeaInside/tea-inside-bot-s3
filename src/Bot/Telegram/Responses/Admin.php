<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
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
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * @return bool
	 */
	private function isAdmin(): bool
	{
		isset($this->pdo) or $this->pdo = DB::pdo();
		$st = $this->pdo->prepare(
			"SELECT `user_id` FROM `group_admins` WHERE `user_id`=:user_id AND `group_id`=:group_id LIMIT 1;"
		);
		$st->execute(
			[
				":user_id" => $this->data["user_id"],
				":group_id" => $this->data["chat_id"]
			]
		);

		return (bool) $st->fetch(PDO::FETCH_NUM);
	}

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

	/**
	 * @return bool
	 */
	public function warn()
	{
		isset($this->pdo) or $this->pdo = DB::pdo();
	}

	/**
	 * @return bool
	 */
	public function promote()
	{
		isset($this->pdo) or $this->pdo = DB::pdo();
		if (in_array($this->data["user_id"], SUDOERS) || $this->isAdmin()) {
			if (isset($this->data["reply_to"])) {
				$exe = Exe::promoteChatMember(
					[
						"chat_id" => $this->data["chat_id"],
						"user_id" => $this->data["reply_to"]["from"]["id"],
						"can_change_info" => 1,
						"can_restrict_members" => 1,
						"can_pin_messages" => 1,
						"can_promote_members" => 1,
						"can_delete_messages" => 1,
						"can_invite_users" => 1
					]
				);
				$exe = json_decode($exe["out"], true);			
				if ($exe["ok"]) {
					$exe = Exe::sendMessage(
						[
							"chat_id" => $this->data["chat_id"],
							"text" => Lang::get("admin.promote_success",
								[
									":promotor" => Lang::namelink($this->data["user_id"],$this->data["first_name"]), 
									":new_admin" => Lang::namelink($this->data["reply_to"]["from"]["id"], $this->data["reply_to"]["from"]["first_name"])
								]),
							"parse_mode" => "HTML"
						]
					);
				} else {
					Exe::sendMessage(
						[
							"chat_id" => $this->data["chat_id"],
							"text" => 
									"<b>An error occured!</b>\n\n"
									."<b>Error Code:</b> <code>".htmlspecialchars($exe["error_code"], ENT_QUOTES, "UTF-8")."</code>"
									."\n<b>Description:</b> <code>".htmlspecialchars($exe["description"], ENT_QUOTES, "UTF-8")."</code>",
							"parse_mode" => "HTML",
							"reply_to_message_id" => $this->data["msg_id"]
						]
					);
				}
			} else {
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => Lang::get("admin.need_reply_or_mention"),
						"parse_mode" => "HTML",
						"reply_to_message_id" => $this->data["msg_id"]
					]
				);
			}
		} else {
			Exe::sendMessage(
				[
					"chat_id" => $this->data["chat_id"],
					"text" => Lang::get("admin.command_not_allowed"),
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		}
		return true;
	}

	/**
	 * @param string $reason
	 * @return bool
	 */
	public function ban($reason = null)
	{
		isset($this->pdo) or $this->pdo = DB::pdo();
		if (in_array($this->data["user_id"], SUDOERS) || $this->isAdmin()) {

			$mentioned_users = $this->getMentionedUsers();

			if (isset($this->data["reply_to"]) || (count($mentioned_users) > 0)) {
				$exe = Exe::kickChatMember(
					[
						"chat_id" => $this->data["chat_id"],
						"user_id" => $this->data["reply_to"]["from"]["id"]
					]
				);

				$exe = json_decode($exe["out"], true);

				if ($exe["ok"]) {
					$exe = Exe::sendMessage(
						[
							"chat_id" => $this->data["chat_id"],
							"text" => Lang::get("admin.banned_success", 
								[
									":admin" => Lang::namelink($this->data["user_id"],$this->data["first_name"]), 
									":banned_user" => Lang::namelink($this->data["reply_to"]["from"]["id"], $this->data["reply_to"]["from"]["first_name"])
								]
							),
							"parse_mode" => "HTML"
						]
					);	
				} else {
					Exe::sendMessage(
						[
							"chat_id" => $this->data["chat_id"],
							"text" => 
									"<b>An error occured!</b>\n\n"
									."<b>Error Code:</b> <code>".htmlspecialchars($exe["error_code"], ENT_QUOTES, "UTF-8")."</code>"
									."\n<b>Description:</b> <code>".htmlspecialchars($exe["description"], ENT_QUOTES, "UTF-8")."</code>",
							"parse_mode" => "HTML",
							"reply_to_message_id" => $this->data["msg_id"]
						]
					);
				}
			} else {
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => Lang::get("admin.need_reply_or_mention"),
						"parse_mode" => "HTML",
						"reply_to_message_id" => $this->data["msg_id"]
					]
				);
			}
		} else {
			Exe::sendMessage(
				[
					"chat_id" => $this->data["chat_id"],
					"text" => Lang::get("admin.command_not_allowed"),
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		}
		return true;
	}

	/**
	 * @param string $reason
	 * @return bool
	 */
	public function kick($reason = null)
	{
		isset($this->pdo) or $this->pdo = DB::pdo();
		if (in_array($this->data["user_id"], SUDOERS) || $this->isAdmin()) {
			if (isset($this->data["reply_to"])) {
				$exe = Exe::kickChatMember(
					[
						"chat_id" => $this->data["chat_id"],
						"user_id" => $this->data["reply_to"]["from"]["id"]
					]
				);

				$exe = json_decode($exe["out"], true);

				if ($exe["ok"]) {
					$exe = Exe::unbanChatMember(
						[
							"chat_id" => $this->data["chat_id"],
							"user_id" => $this->data["reply_to"]["from"]["id"]
						]
					);

					$exe = json_decode($exe["out"], true);

					if ($exe["ok"]) {
						$exe = Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => Lang::get("admin.kicked_success", 
									[
										":admin" => Lang::namelink($this->data["user_id"],$this->data["first_name"]), 
										":kicked_user" => Lang::namelink($this->data["reply_to"]["from"]["id"], $this->data["reply_to"]["from"]["first_name"])
									]
								),
								"parse_mode" => "HTML"
							]
						);	
					} else {
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => 
										"<b>An error occured!</b>\n\n"
										."<b>Error Code:</b> <code>".htmlspecialchars($exe["error_code"], ENT_QUOTES, "UTF-8")."</code>"
										."\n<b>Description:</b> <code>".htmlspecialchars($exe["description"], ENT_QUOTES, "UTF-8")."</code>",
								"parse_mode" => "HTML",
								"reply_to_message_id" => $this->data["msg_id"]
							]
						);
					}
				} else {
					Exe::sendMessage(
						[
							"chat_id" => $this->data["chat_id"],
							"text" => 
								"<b>An error occured!</b>\n\n"
									."<b>Error Code:</b> <code>".htmlspecialchars($exe["error_code"], ENT_QUOTES, "UTF-8")."</code>"
									."\n<b>Description:</b> <code>".htmlspecialchars($exe["description"], ENT_QUOTES, "UTF-8")."</code>",
							"parse_mode" => "HTML",
							"reply_to_message_id" => $this->data["msg_id"]
						]
					);
				}
			} else {
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => Lang::get("admin.need_reply_or_mention"),
						"parse_mode" => "HTML",
						"reply_to_message_id" => $this->data["msg_id"]
					]
				);
			}
		} else {
			Exe::sendMessage(
				[
					"chat_id" => $this->data["chat_id"],
					"text" => Lang::get("admin.command_not_allowed"),
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		}
		return true;
	}

	/**
	 * @param string $msg
	 * @return bool
	 */
	public function setWelcome($msg)
	{
		if (in_array($this->data["user_id"], SUDOERS) || $this->isAdmin()) {

			$exe = Exe::sendMessage(
				[
					"chat_id" => $this->data["chat_id"],
					"text" => $msg,
					"parse_mode" => "HTML"
				]
			);

			$exe = json_decode($exe["out"], true);
			if ($exe["ok"]) {
				$this->pdo->prepare(
					"UPDATE `group_settings` SET `welcome_message`=:welcome_message WHERE `group_id`=:group_id LIMIT 1;"
				)->execute(
					[
						":welcome_message" => $msg,
						":group_id" => $this->data["chat_id"]
					]
				);
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => Lang::get("welcome.set_success"),
						"reply_to_message_id" => $this->data["msg_id"]
					]
				);
				Exe::deleteMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"message_id" => $exe["result"]["message_id"]
					]
				);
			} else {
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => 
								"<b>An error occured!</b>\n\n"
								."<b>Error Code:</b> <code>".htmlspecialchars($exe["error_code"], ENT_QUOTES, "UTF-8")."</code>"
								."\n<b>Description:</b> <code>".htmlspecialchars($exe["description"], ENT_QUOTES, "UTF-8")."</code>",
						"reply_to_message_id" => $this->data["msg_id"],
						"parse_mode" => "HTML"
					]
				);
			}
		} else {
			Exe::sendMessage(
				[
					"chat_id" => $this->data["chat_id"],
					"text" => Lang::get("admin.command_not_allowed"),
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		}
		return true;
	}


	private function getMentionedUsers()
	{
		$mentioned_username = $queryData = $unknown = $mentioned_users = [];
		if (isset($this->data["entities"])) {
			$query = "SELECT `id`,`first_name`,`last_name`,`username` FROM `users` WHERE ";
			foreach($this->data["entities"] as $key => $ent) {
				if (isset($ent["type"]) && $ent["type"] == "mention") {
					$query = "`username` LIKE :username{$key} OR";
					$username = substr($data["text"], $ent["offset"] + 1, $ent["length"]);
					$queryData[":username{$key}"] = $username
					$mentioned_username[strtolower($username)] = 1;
				} elseif (isset($ent["type"]) && isset($ent["user"]["id"]) && $ent["type"] == "text_mention") {
					$mentioned_users[] = [
						"user_id" => $ent["user"]["id"],
						"first_name" => $ent["user"]["first_name"],
						"last_name" => $ent["user"]["last_name"],
					];
				}
			}
			if (count($queryData) > 0) {
				isset($this->pdo) or $this->pdo = DB::pdo();
				$st = $this->pdo->prepare(trim($query, "OR").";");
				$st->execute($queryData);
				while ($r = $st->fetch(PDO::FETCH_NUM)) {
					unset($mentioned_username[strtolower($r[3])]);
					$mentioned_users[] = [
						"user_id" => $r[0],
						"first_name" => $r[1],
						"last_name" => $r[2]
					];
				}

				$unknown = array_keys($mentioned_username);
			}
		}

		var_dump(
			"unknown", $unknown, "\n",
			"mentioned_users", $mentioned_users, "\n",
			"mentioned_username", $mentioned_username, "\n",
			"query", $query, "\n",
			"queryData", $queryData, "\n"
		);

		return [
			"mentioned_users" => $mentioned_users,
			"unknown" => $unknown
		];
	}
}
