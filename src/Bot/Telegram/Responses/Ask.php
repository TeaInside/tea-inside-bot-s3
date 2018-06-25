<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Plugins\Brainly\Brainly;
use Bot\Telegram\Plugins\Stackoverflow\Stackoverflow;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class Ask extends ResponseFoundation
{
	/**
	 * @param string $query
	 * @return bool
	 */
	public function stackoverflow($query)
	{
		if ($this->data["chat_type"] !== "private") {
			$pdo = DB::pdo();
			$st = $pdo->prepare("SELECT `ask` FROM `group_settings` WHERE `group_id`=:group_id LIMIT 1;");
			$st->execute(
				[
					":group_id" => $this->data["chat_id"]
				]
			);
			if ($st = $st->fetch(PDO::FETCH_NUM)) {
				if ($st[0] === "off") {
					var_dump("ask off");
					return;
				}
			}
		}

		$st = new Stackoverflow($query);
		if (count($st = $st->exec()) > 0) {
			$r = "";

			foreach ($st as $k => $v) {
				$r .= ($k+1).". <a href=\"https://stackoverflow.com/".$v["link"]."\">".$v["title"]."</a>"."\n<b>".trim(htmlspecialchars(substr(html_entity_decode(strip_tags($v["desc"]), ENT_QUOTES, "UTF-8"), 0, 120), ENT_QUOTES, "UTF-8"))."...</b>\n\n";
			}

			if ($r === "") {
				$r = "<b>Not found</b>";
			}

		} else {
			$r = "<b>Not found</b>";
		}

		$exe = Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"reply_to_message_id" => $this->data["msg_id"],
				"text" => $r,
				"parse_mode" => "HTML"
			]
		);

		$exe = json_decode($exe["out"], true);

		if (! $exe["ok"]) {
			Exe::sendMessage(
				[
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"text" => "An error occured: ".json_encode($exe)
				]
			);
		}

		return true;
	}

	/**
	 * @param string $query
	 * @return bool
	 */
	public function brainly($query)
	{
		if ($this->data["chat_type"] !== "private") {
			$pdo = DB::pdo();
			$st = $pdo->prepare("SELECT `ask` FROM `group_settings` WHERE `group_id`=:group_id LIMIT 1;");
			$st->execute(
				[
					":group_id" => $this->data["chat_id"]
				]
			);
			if ($st = $st->fetch(PDO::FETCH_NUM)) {
				if ($st[0] === "off") {
					var_dump("ask off");
					return;
				}
			}
		}

		$st = new Brainly($query);
		$st->limit(10);
		$st = $st->exec();

		$similarity = [];
		$query = trim(strtolower($query));
		foreach ($st as $k => $v) {
			if (isset($v["content"]) && isset($v["responses"][0]["content"])) {
				similar_text($query, strip_tags(trim($v["content"])), $n);
				$similarity[$k] = $n;
			}
		}
		$maxPos = -1000;
		if (count($similarity) > 0) {
			$maxPos = array_search(max($similarity), $similarity);	
		}

		if (isset($st[$maxPos]["content"]) && isset($st[$maxPos]["responses"][0]["content"])) {
			$fQuery = $st[$maxPos]["content"];
			$answer = $st[$maxPos]["responses"][0]["content"];
			$r = "<b>The most similar questions:</b>\n".trim(htmlspecialchars(str_replace("<br />", "\n", $fQuery)))."\n\n<b>The answer:</b>\n".trim(htmlspecialchars(str_replace("<br />", "\n", $answer)));
		} else {
			$r = "<b>Not found</b>";
		}

		$exe = Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"reply_to_message_id" => $this->data["msg_id"],
				"text" => $r,
				"parse_mode" => "HTML"
			]
		);

		$exe = json_decode($exe["out"], true);

		if (! $exe["ok"]) {
			Exe::sendMessage(
				[
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"text" => "An error occured: ".json_encode($exe)
				]
			);
		}

		return true;
	}
}
