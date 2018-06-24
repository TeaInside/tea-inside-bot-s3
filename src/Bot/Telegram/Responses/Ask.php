<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Plugins\Brainly\Brainly;

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
	public function brainly($query)
	{
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
