<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
class ShellExec extends ResponseFoundation
{
	/**
	 * @param string $cmd
	 * @return bool
	 */
	public function run(string $cmd)
	{
		if (in_array($this->data["user_id"], SUDOERS)) {
			$fp = fopen(
				$filename = "/tmp/".substr(md5(sha1($cmd).md5($cmd)), 0, 4).".sh", 
				"w"
			);
			flock($fp, LOCK_EX);
			fwrite($fp, "#!/usr/bin/env bash\n".$cmd);
			fflush($fp);
			fclose($fp);
			shell_exec("sudo chmod +x ".$filename);
			Exe::sendMessage(
				[
					"parse_mode" => "html",
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"text" => "<pre>".htmlspecialchars(shell_exec($filename." 2>&1"))."</pre>"
				]
			);
			shell_exec("sudo rm -f ".$filename);
			return true;
		}
	}
}
