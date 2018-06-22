<?php

namespace Bot\Telegram\Responses;

use Isolator;
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
			$shell = trim(shell_exec("cd /home/ammarfaizi2 && ".$filename." 2>&1"));
			$shell = $shell === "" ? "~" : $shell;
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
			$sharenet = false;
			if (file_exists(data."/tmp/telegram/shell_count/".$this->data["user_id"])) {
				$c = json_decode(file_get_contents(data."/tmp/telegram/shell_count/".$this->data["user_id"]), true);
				if (isset($c["count"], $c["last"])) {
					$c["count"]++;
				} else {
					$c = ["count" => 1];	
				}
			} else {
				is_dir(data."/tmp/telegram/shell_count/") or mkdir(data."/tmp/telegram/shell_count/");
				$c = ["count" => 1];
			}

			if (file_exists($f = data."/clients/".$this->data["user_id"])) {
				$j = json_decode(file_get_contents($f), true);
				if (isset($j["limit_per_day"]) && isset($j["sharenet"])) {
					$sharenet = true;
					$limit = $j["limit_per_day"];
				}
			}

			if ($c["count"] > $limit) {
				if (time() > ($c["last"]+(3600*24))) {
					@unlink(data."/tmp/telegram/shell_count/".$this->data["user_id"]);
					return true;
				} else {
					$reply = "You have reached the max number of limit. Please try again later!\n\nYour limitation will be removed at ".date("d F Y h:i:s A", $c["last"]+(3600*24))." GMT+7\n\nYou can also buy our service to increase the shell exec limitation.\n\nContact Us: @KodingTeh (24 hours)";	
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
					$id = Isolator::generateUserId($this->data["user_id"]);
					$st = new Isolator($id);

					if (! file_exists($f = ISOLATOR_HOME."/".$id."/u".$id."/".($n = substr(md5(sha1($cmd).md5($cmd)), 0, 4).".sh"))) {
						file_put_contents($f, $cmd);
					}
					$st->sharenet = $sharenet;
					$st->setMemoryLimit(1024 * 1024);
					$st->setMaxProcesses(30);
					$st->setMaxWallTime(15);
					$st->setMaxExecutionTime(10);
					$st->setExtraTime(5);

					$st->run("/bin/sh /home/u".$id."/".$n);

					$rr = htmlspecialchars($st->getStdout());
					$rr.= htmlspecialchars($st->getStderr());
					$rr = trim($rr);
					$reply = "<pre>";
					$reply.= $rr === "" ? "~" : $rr;
					$reply.= "</pre>";

					$c["last"] = time();
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
