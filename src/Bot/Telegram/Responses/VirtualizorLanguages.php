<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter\PHP;
use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter\Python;
use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter\NodeJS;

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
					$st->version = "7.2";
					$reply = $st->run();
				break;
			case 'python':
					$st = new Python(substr($this->data["text"], 4));
					if (! in_array($this->data["user_id"], SUDOERS)) {
						$st->user = "limited";
					}
					$st->version = "3";
					$reply = $st->run();
				break;
			case 'python3':
					$st = new Python(substr($this->data["text"], 5));
					if (! in_array($this->data["user_id"], SUDOERS)) {
						$st->user = "limited";
					}
					$st->version = "3";
					$reply = $st->run();
				break;
			case 'python2':
					$st = new Python(substr($this->data["text"], 5));
					if (! in_array($this->data["user_id"], SUDOERS)) {
						$st->user = "limited";
					}
					$st->version = "2";
					$reply = $st->run();
				break;
			case 'js':
			case 'node':
			case 'nodejs':
					$st = new NodeJS(substr($this->data["text"], 4));
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
