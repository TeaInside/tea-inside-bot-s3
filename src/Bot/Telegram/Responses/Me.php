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
class Me extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function me()
	{
		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => "There is no data stored for this user.",
				"reply_to_message_id" => $this->data["msg_id"],
				"parse_mode" => "HTML"
			]
		);

		return true;
	}
}
