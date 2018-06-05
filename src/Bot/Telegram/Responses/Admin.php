<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Logger\AdminLogger;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class ShellExec extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function show()
	{
		$st = new AdminLogger($this->data);
		$st->run = 1;
		$st->reset = 1;
		$st->run();
		$data = $st->get();
		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => json_encode($data)
			]
		);
	}
}
