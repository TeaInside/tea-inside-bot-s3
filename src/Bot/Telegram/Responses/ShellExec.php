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
		} else {
			$reply = $report = 0;
			if ($this->sudoersOnly($cmd)) {
				$reply = "<a href=\"tg://user?id=".$this->data["user_id"]."\">".htmlspecialchars($this->data["name"], ENT_QUOTES, "UTF-8")."</a> is not in the sudoers file. This incident will be reported.";
				$report = 1;
			} elseif ($this->isNotSecure($cmd)) {
				$reply = "Rejected due to security reason!";
				$report = 1;
			}

			if (! $reply) {
				if (! ($a = trim(shell_exec("lastlog | grep limited && echo 123")))) {
					$passwd = rand().rand();
					shell_exec("echo -e \"{$passwd}\\n{$passwd}\" > /tmp/limited_passwd.tmp");
					shell_exec("sudo adduser limited < /tmp/limited_passwd.tmp");
					shell_exec("sudo rm -f /tmp/limited_passwd.tmp");
				}
				if (! ($a = trim(shell_exec("lastlog | grep limited && echo 123")))) {
					$reply = "Unable to create limited user";
				} else {
					$fp = fopen(
					$filename = "/tmp/".substr(md5(sha1($cmd).md5($cmd)), 0, 4).".sh", 
						"w"
					);
					flock($fp, LOCK_EX);
					fwrite($fp, "#!/usr/bin/env bash\n".$cmd);
					fflush($fp);
					fclose($fp);
					shell_exec("sudo chmod +x ".$filename);
					$reply = shell_exec("cd /home/limited && sudo -u limited ".$filename." 2>&1");
					shell_exec("sudo rm -f ".$filename);
				}
			}

			Exe::sendMessage(
				[
					"parse_mode" => "html",
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"text" => "<pre>".htmlspecialchars($reply)."</pre>"
				]
			);

			if ($report) {
				$this->reportToSudoers();
			}
			
			return true;
		}
	}

	/**
	 * @return bool
	 */
	private function sudoersOnly($cmd)
	{
		return (bool) preg_match("/sudo\s/", $cmd);
	}

	/**
	 * @return bool
	 */
	private function isNotSecure($cmd)
	{
		return (
			(strpos($cmd, "rm ")!==false && strpos($cmd, "-r")!==false)
		);
	}

	/**
	 * @return void
	 */
	private function reportToSudoers()
	{

	}
}
