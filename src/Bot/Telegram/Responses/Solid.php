<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Line\Exe as LineEXE;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class Solid extends ResponseFoundation
{
	/**
	 * @param string $sendTo
	 * @return bool
	 */
	public function run(string $sendTo)
	{
		if ($this->data["msg_type"] === "text") {
			LineExe::bg()::push(
				[
					"to" => $sendTo,
					"messages" => LineExe::buildLongTextMessage($this->data["name"]."\n\n".$this->data["text"])
				]
			);
		}
	}
}
