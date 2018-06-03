<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter\PHP;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class VirtualizorLanguages extends ResponseFoundation
{
	/**
	 * @param string $cmd
	 * @return bool
	 */
	public function run(string $cmd)
	{
		$reply = "";
		switch ($cmd) {
			case 'php':
					$st = new PHP($this->data["text"]);
					if (! in_array($this->data["user_id"], SUDOERS)) {
						$st->user = "limited";
					}
					$reply = $st->run();
				break;
			
			default:
				break;
		}

		$reply = trim($reply);
		if ($reply === "") {
			$reply = "~";
		}
		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => $reply,
				"reply_to_message_id" => $this->data["msg_id"]
			]
		);
		return true;
	}
}
