<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Plugins\VirtualizorLanguages\Compiler\C;
use Bot\Telegram\Plugins\VirtualizorLanguages\Compiler\Cpp;
use Bot\Telegram\Plugins\VirtualizorLanguages\Compiler\Java;
use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter\PHP;
use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter\Perl;
use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter\Ruby;
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
					$st = new PHP($this->data["__code"]);
					$st->version = "7.2";
					$reply = $st->run($this->data["user_id"]);
				break;
			case 'python':
					$st = new Python($this->data["__code"]);
					$st->version = "3";
					$reply = $st->run($this->data["user_id"]);
				break;
			case 'python3':
					$st = new Python($this->data["__code"]);
					$st->version = "3";
					$reply = $st->run($this->data["user_id"]);
				break;
			case 'python2':
					$st = new Python($this->data["__code"]);
					$st->version = "2";
					$reply = $st->run($this->data["user_id"]);
				break;
			case 'js':
			case 'node':
			case 'nodejs':
					$st = new NodeJS($this->data["__code"]);
					$reply = $st->run($this->data["user_id"]);
				break;
			case 'ruby':
					$st = new Ruby($this->data["__code"]);
					$reply = $st->run($this->data["user_id"]);
				break;
			case 'perl':
					$st = new Perl($this->data["__code"]);
					$st = $st->run($this->data["user_id"]);
				break;
			case 'c':
					$st = new C($this->data["__code"]);
					$reply = $st->run($this->data["user_id"]);
				break;
			case 'c++':
					$st = new Cpp($this->data["__code"]);
					$reply = $st->run($this->data["user_id"]);
				break;
			case 'java':
					$st = new Java($this->data["__code"]);
					$reply = $st->run($this->data["user_id"]);
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
