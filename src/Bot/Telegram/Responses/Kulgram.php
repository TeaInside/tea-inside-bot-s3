<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class Kulgram extends ResponseFoundation
{
	/**
	 * @param string $judul
	 * @return bool
	 */
	public function start(string $judul)
	{
		if ($this->isStarted()) {
			
		} else {
			$judul = strtoupper($judul);

			var_dump($judul);

			$reply = "<b>".htmlspecialchars($judul)."</b>";
			
			Exe::sendMessage(
				[
					"chat_id" => $this->data["chat_id"],
					"text" => $reply,
					"parse_mode" => "HTML",
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		}
		return true;
	}

	private function isStarted()
	{
		
	}

	/**
	 * @return bool
	 */
	public function run()
	{

		return true;
	}
}
