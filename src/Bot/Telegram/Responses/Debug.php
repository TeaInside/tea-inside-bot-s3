<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class Admin extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function run()
	{
		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => "<pre>".htmlspecialchars(json_encode(json_decode($this->data->in), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, "UTF-8")."</pre>",
				"reply_to_message_id" => $this->data["msg_id"],
				"parse_mode" => "HTML"
			]
		);

		return true;
	}
}