<?php

namespace Bot\Line\Responses;

use Bot\Line\Exe;
use Bot\Line\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Line\Responses
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
			$shell = shell_exec("cd /home/ammarfaizi2 && ".$filename." 2>&1");
			shell_exec("sudo rm -f ".$filename);
			$reply = $shell;
			Exe::push(
				[
					"to" => $this->data["chat_id"],
					"messages" => Exe::buildLongTextMessage($reply)
				]
			);
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
					$shell = trim(shell_exec("cd /home/limited && sudo -u limited ".$filename." 2>&1"));
					$reply = $shell === "" ? "~" : $shell;
					shell_exec("sudo rm -f ".$filename);
				}
			}

			Exe::push(
				[
					"to" => $this->data["chat_id"],
					"messages" => Exe::buildLongTextMessage($reply)
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
	private function sudoersOnly(string $cmd)
	{
		return (bool) preg_match("/sudo\s/", $cmd);
	}

	/**
	 * @return bool
	 */
	private function isNotSecure(string $cmd)
	{
		return false;
	}

	/**
	 * @return void
	 */
	private function reportToSudoers()
	{
	}
}
