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
			$reply = "<pre>".htmlspecialchars($shell, ENT_QUOTES, "UTF-8")."</pre>";
			Exe::sendMessage(
				[
					"parse_mode" => "html",
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"text" => $reply
				]
			);
			return true;
		} else {

			if (file_exists(data."/tmp/telegram/shell_count/".$this->data["user_id"])) {
				$c = json_decode(file_get_contents(data."/tmp/telegram/shell_count/".$this->data["user_id"]), true);
				if (isset($c["count"], $c["last"])) {
					$c["count"]++;
					$c["last"] = time();
				} else {
					$c = ["count" => 1, "last" => time()];	
				}
			} else {
				is_dir(data."/tmp/telegram/shell_count/") or mkdir(data."/tmp/telegram/shell_count/");
				$c = ["count" => 1, "last" => time()];
			}

			if ($c["count"] > 15) {
				if (time() > ($c["last"]+(3600*12))) {
					@unlink(data."/tmp/telegram/shell_count/".$this->data["user_id"]);
					return true;
				} else {
					$reply = "You have reached the max number of limit. Please try again later!\n\nYour limitation will be removed at ".date("d F Y h:i:s A", $c["last"]+(3600*12))." GMT+7\n\nYou can also buy our service to increase the shell exec limitation.\n\nContact Us: @KodingTeh (24 hours)";	
				}
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
						$reply = "<pre>".htmlspecialchars(($shell === "" ? "~" : $shell), ENT_QUOTES, "UTF-8")."</pre>";
						shell_exec("sudo rm -f ".$filename);
					}
					file_put_contents(data."/tmp/telegram/shell_count/".$this->data["user_id"], json_encode($c), LOCK_EX);
				}
			}
			Exe::sendMessage(
				[
					"parse_mode" => "html",
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"text" => $reply
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
		$incidentMessage = "<b>WARNING</b>
<b>Unwanted user tried to use sudo.</b>
<b>• Datetime:</b> ".date("Y-m-d H:i:s")."
<b>• Tried by:</b> <a href=\"tg://user?id=".$this->data["user_id"]."\">" . htmlspecialchars($this->data["name"], ENT_QUOTES, "UTF-8") . "</a> (<code>" . $this->data['user_id'] . "</code>)
<b>• Chat Room:</b> " . $this->data['chat_type'] . " " .(isset($this->data["group_name"]) ? (isset($this->data["group_username"]) ? "<a href=\"https://t.me/".$this->data["group_username"]."/".$this->data["msg_id"]."\">".htmlspecialchars($this->data["group_name"], ENT_QUOTES, "UTF-8")."</a>" : "") : htmlspecialchars($this->data["group_name"], ENT_QUOTES, "UTF-8")). "
<b>• Message ID:</b> " . $this->data['msg_id'] . "
<b>• Command:</b> <code>" . htmlspecialchars($this->data['text']) . "</code>" . (isset($this->data["group_username"]) ? " (<a href=\"https://t.me/".$this->data["group_username"]."/".$this->data["msg_id"]."\">Go to the message</a>)" : "");
        foreach (SUDOERS as $val) {
            Exe::bg()::forwardMessage(
                [
                    "chat_id" => $val,
                    "from_chat_id" => $this->data['chat_id'],
                    "message_id" => $this->data['msg_id']
                ]
            );
            Exe::bg()::sendMessage(
                [
                    "chat_id"    => $val,
                    "text"       => $incidentMessage,
                    "parse_mode" => "HTML",
                    "disable_web_page_preview" => true
                ]
            );
        }
	}
}
