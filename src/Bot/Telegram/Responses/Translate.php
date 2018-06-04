<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Plugins\Translators\GoogleTranslate;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class Translate extends ResponseFoundation
{
	/**
	 * @param string $cmd
	 * @return bool
	 */
	public function run(string $text, string $from, string $to)
	{
		$st = new GoogleTranslate($text, $from, $to);
		$st = trim($st->exec());
		$st = $st === "" ? "~" : $st;
		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => $st,
				"reply_to_message_id" => $this->data["msg_id"]
			]
		);
		return true;
	}

	/**
	 * @return bool
	 */
	public function tlr()
	{
		if (isset($this->data->in["message"]["reply_to_message"]["text"])) {
			$st = new GoogleTranslate(
				$this->data->in["message"]["reply_to_message"]["text"], "auto", "id"
			);
			$st = trim($st->exec());
			$st = $st === "" ? "~" : $st;
			Exe::sendMessage(
				[
					"chat_id" => $this->data["chat_id"],
					"text" => $st,
					"reply_to_message_id" => $this->data->in["message"]["reply_to_message"]["message_id"]
				]
			);
		}
		return true;
	}
}
