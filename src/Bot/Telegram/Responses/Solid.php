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
		var_dump(123);
		if ($this->data["msg_type"] === "text") {
			LineExe::bg()::push(
				[
					"to" => $sendTo,
					"messages" => LineExe::buildLongTextMessage($this->data["name"]."\n\n".$this->data["text"])
				]
			);
		} elseif ($this->data["msg_type"] === "photo") {

			$p = $this->data['photo'][count($this->data['photo']) - 1];
	        $a = json_decode(Exe::getFile([
	            "file_id" => $p['file_id']
	        ])["out"], true);
	        if (isset($a["result"]["file_path"])) {

	        	$data = [];

		        $ch = curl_init("https://api.telegram.org/file/bot".TOKEN."/".$a['result']['file_path']);
		        curl_setopt_array($ch, 
		        	[
		        		CURLOPT_RETURNTRANSFER => true,
		        		CURLOPT_SSL_VERIFYPEER => false,
		        		CURLOPT_SSL_VERIFYHOST => false
		        	]
		        );

		        is_dir(data."/tmp/telegram") or mkdir(data."/tmp/telegram");
		        is_dir(data."/tmp/telegram/images") or mkdir(data."/tmp/telegram/images");

		        $binary = curl_exec($ch);
		        $filename = sha1($binary).".jpg";
		        file_put_contents(data."/tmp/telegram/images/{$filename}", curl_exec($ch));

		        curl_close($ch);

		        $url = public_storage_url."/tmp/telegram/images/{$filename}";

		        $data[] = [
	                "type" => "image",
	                "originalContentUrl" => $url,
	                "previewImageUrl" => $url
	            ];

				if (! empty($this->data['text'])) {
	                $data[] = [
	                    "type" => "text",
	                    "text" => $this->data["name"]."\n\n".$this->data["text"]
	                ];
	            }

            	LineExe::bg()::push(
            		[
	                	"to" => $sendTo,
	                	"messages" => $data
	            	]
            	);
		    }
		}
	}
}
