<?php

namespace Bot\Line\Responses;

use Bot\Line\Exe;
use Bot\Line\ResponseFoundation;
use Bot\Telegram\Exe as TelegramExe;
use Bot\Telegram\Plugins\Translators\GoogleTranslate;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Line\Responses
 * @license MIT
 * @since 0.0.1
 */
class Solid extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function run(string $sendTo)
	{
		if ($this->data["msg_type"] === "text") {
			
			$u = json_decode(
				Exe::profile(
					$this->data['user_id'], 
					(($this->data['chat_type'] !== "private" ? $this->data['chat_id'] : null))
				)['content']
			, true);

	        isset($u['displayName']) or $u['displayName'] = $this->b['user_id'];
	        $msg = "<b>".htmlspecialchars($u['displayName'])."</b>\n".htmlspecialchars(str_replace("@Ammar F.", "@ammarfaizi2", $this->data["text"]));
			TelegramExe::bg()::sendMessage(
	         	[
					"text" => $msg,
					"chat_id" => $sendTo,
					"parse_mode" => "HTML"
	         	]
	        );

		} elseif ($this->data["msg_type"] === "image") {

	        is_dir(data."/tmp/line") or mkdir(data."/tmp/line");
	        is_dir(data."/tmp/line/images") or mkdir(data."/tmp/line/images");

	        $binary = Exe::getContent($this->data["msg_id"])["content"];
	        $filename = sha1($image)."_".time().".jpg";
	        file_put_contents(data."/tmp/line/images/{$filename}", $binary);
	        unset($binary);

	        $u = json_decode(
				Exe::profile(
					$this->data['user_id'], 
					(($this->data['chat_type'] !== "private" ? $this->data['chat_id'] : null))
				)['content']
			, true);

	        isset($u["displayName"]) or $u["displayName"] = $this->data["user_id"];

			Telegram::bg()::sendPhoto(
				[
					"caption" => htmlspecialchars($u['displayName']),
					"chat_id" => $sendTo,
					"photo" => public_storage_url."/tmp/line/images/{$filename}"
				]
			);

		}
	}
}