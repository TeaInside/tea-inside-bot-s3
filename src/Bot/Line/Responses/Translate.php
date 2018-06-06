<?php

namespace Bot\Line\Responses;

use Bot\Line\Exe;
use Bot\Line\ResponseFoundation;
use Bot\Telegram\Plugins\Translators\GoogleTranslate;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Line\Responses
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
		Exe::push(
			[
				"to" => $this->data["chat_id"],
				"messages" => Exe::buildLongTextMessage($st)
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
			Exe::push(
				[
					"to" => $this->data["chat_id"],
					"messages" => Exe::buildLongTextMessage($st)
				]
			);
		}
		return true;
	}

	/**
	 * @param string $lang1
	 * @param string $lang2
	 * @return bool
	 */
	public function tlr2(string $lang1, string $lang2)
	{
		if (isset($this->data->in["message"]["reply_to_message"]["text"])) {
			$st = new GoogleTranslate(
				$this->data->in["message"]["reply_to_message"]["text"], $lang1, $lang2
			);
			$st = trim($st->exec());
			$st = $st === "" ? "~" : $st;
			Exe::push(
				[
					"to" => $this->data["chat_id"],
					"messages" => Exe::buildLongTextMessage($st)
				]
			);
		}
		return true;
	}
}
