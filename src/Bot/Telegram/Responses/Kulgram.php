<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Mpdf\Mpdf;
use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class Kulgram extends ResponseFoundation
{	

	public function handle()
	{
		$this->data["text"] = str_replace(chr(226).chr(128).chr(148), "--", $this->data["text"]);
		if (preg_match("/^(?:\\/|\\!|\\~)(?:kulgram)(?:[\\s\\n]{1,})?([^\\s\\n]+)?(?:[\\s\\n]{1,})?(.*)?$/", $this->data["text"], $m)) {
			$_argv = [];
			if (isset($m[2]) && preg_match_all("/\-{2}([^\\s\\n]+)(?:\\s+|\\n+|\=)((?:\\\"|\\')(.+)(?:[^(\\\\\")]\\\"|\\')|[^\\s\\n]+)(?:[\\s\\n]|$)/Usi", $m[2], $n)) {
				foreach ($n[2] as $k => &$v) {
					if (! empty($n[3][$k])) {
						$v = substr($v, 1, -1);
					}
					do {
						$v = str_replace("\\\"", "\"", $v, $nn);
					} while ($nn);
					do {
						$v = str_replace("\\\\", "\\", $v, $nn);
					} while ($nn);
					$_argv[$n[1][$k]] = ($v = trim($v));
				}
			}

			switch ($m[1]) {
				case 'init':
					if ((!isset($_argv["title"])) && (!isset($_argv["author"]))) {
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => "krec: fatal error: You need to provide the title and author.

	--title		Set kulgram title
	--author	Set kulgram author

For bug reporting please send to @KodingTeh (24 hours)",
								"reply_to_message_id" => $this->data["msg_id"]
							]
						);
						return true;
					}

					if (! isset($_argv["title"])) {
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => "krec: fatal error: You need to provide the title.

	--title		Set kulgram title
	--author	Set kulgram author

For bug reporting please send to @KodingTeh (24 hours)",
								"reply_to_message_id" => $this->data["msg_id"]
							]
						);
						return true;
					}

					if (! isset($_argv["author"])) {
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => "krec: fatal error: You need to provide the author.

	--title		Set kulgram title
	--author	Set kulgram author

For bug reporting please send to @KodingTeh (24 hours)",
								"reply_to_message_id" => $this->data["msg_id"]
							]
						);
						return true;
					}

					return $this->init($_argv["title"]." oleh ".$_argv["author"]);
				case "start":
					return $this->start();
				case "stop":
					return $this->stop();
				default:
				Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => "Usage: /kulgram [command] [option]
Commands:
	init	Initialize a kulgram session
	start	Start a kulgram session
	stop 	Stop a kulgram session

For bug reporting please send to @KodingTeh (24 hours)",
								"reply_to_message_id" => $this->data["msg_id"]
							]
						);
					return true;
					break;
			}

		}
	}

	/**
	 * @return bool
	 */
	public function isAdmin()
	{
		isset($this->pdo) or $this->pdo = DB::pdo();
		$st = $this->pdo->prepare(
			"SELECT `user_id` FROM `group_admins` WHERE `user_id`=:user_id AND `group_id`=:group_id LIMIT 1;"
		);
		$st->execute(
			[
				":user_id" => $this->data["user_id"],
				":group_id" => $this->data["chat_id"]
			]
		);

		return (bool) $st->fetch(PDO::FETCH_NUM);
	}

	/**
	 * @return void
	 */
	public function setUp()
	{
		$this->kulgramDir = data."/kulgram";
		$this->sDir = $this->kulgramDir."/".$this->data["chat_id"];
		is_dir($this->kulgramDir) or mkdir($this->kulgramDir);
		is_dir($this->sDir) or mkdir($this->sDir);
		is_dir($this->sDir."/archives") or mkdir($this->sDir."/archives");

		if (file_exists($f = $this->sDir."/kulgram_info")) {
			$this->info = json_decode(file_get_contents($f), true);
			if (!(is_int($this->info["count"]) && (!is_array($this->info["group_id"])) && is_string($this->info["status"]))) {
				$this->info = [
					"count" => 0,
					"group_id" => $this->data["chat_id"],
					"status" => "sleep"
				];
			}
		} else {
			$this->info = [
				"count" => 0,
				"group_id" => $this->data["chat_id"],
				"status" => "sleep"
			];
		}
	}

	/**
	 * @return void
	 */
	public function __destruct()
	{
		file_put_contents($this->sDir."/kulgram_info", json_encode($this->info));
	}

	/**
	 * @param string $judul
	 * @return bool
	 */
	public function start()
	{
		if (in_array($this->data["user_id"], SUDOERS) || $this->isAdmin()) {
			if ($this->info["status"] === "recording") {
				$reply = "An error occured, could not start system record.\nDevice is busy, please stop the current session first.";
			} elseif ($this->info["status"] === "sleep") {
				$reply = "An error occured, could not start system record.\nDevice is not ready, please initialize a session.";
			} elseif ($this->info["status"] === "idle") {
				$this->info["current_session"]["msg_id_pointer"] = $this->data["msg_id"] + 2;
				$this->info["status"] = "recording";
				$reply = "Start recording...";
			} else {
				$reply = "An error occured, unknown error!";
			}
		} else {
			$reply = Lang::get("admin.command_not_allowed");
		}
		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => $reply,
				"parse_mode" => "HTML",
				"reply_to_message_id" => $this->data["msg_id"]
			]
		);
		return true;
	}

	/**
	 * @return bool
	 */
	public function stop()
	{
		if (in_array($this->data["user_id"], SUDOERS) || $this->isAdmin()) {
			if ($this->info["status"] === "recording") {
				$exe = Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => "Stopping system record...",
						"parse_mode" => "HTML",
						"reply_to_message_id" => $this->data["msg_id"]
					]
				);
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => "System record stopped successfully!",
						"parse_mode" => "HTML",
						"reply_to_message_id" => $this->data["msg_id"]
					]
				);
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => "Building PDF data...",
						"parse_mode" => "HTML",
						"reply_to_message_id" => $this->data["msg_id"]
					]
				);
				$pdo = DB::pdo();			
				$st = $pdo->prepare(
					"SELECT 
						`b`.`text`,`b`.`file`,`b`.`msg_type`,`a`.`created_at`,`c`.`first_name`,
						`c`.`last_name`,`c`.`username`,`c`.`id`,`d`.`sha1_checksum`,`d`.`md5_checksum`,
						`d`.`info`,`d`.`filesize`,`d`.`type`
					FROM `group_messages` AS `a`
					INNER JOIN `group_messages_data` AS `b` ON `a`.`id`=`b`.`message_id`
					INNER JOIN `users` AS `c` ON `a`.`user_id` = `c`.`id`
					LEFT JOIN `files` AS `d` ON `b`.`file` = `d`.`id`
					WHERE `a`.`group_id`=:group_id AND `a`.`tmsg_id` >= :_start AND `a`.`tmsg_id` <= :_end
					ORDER BY `a`.`tmsg_id` ASC;"
				);
				$st->execute(
					[
						":group_id" => $this->data["chat_id"],
						":_start" => $this->info["current_session"]["msg_id_pointer"],
						":_end" => $this->data["msg_id"]
					]
				) or (
					var_dump($st->errorInfo()) xor die()
				);
				$mpdf = new Mpdf(
					["tempDir" => "/tmp"]
				);
				$mpdf->WriteHTML(
					"<center><h1>".htmlspecialchars($this->info["current_session"]["title"])."</h1></center><br>"
				);
				while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
					$name = htmlspecialchars(
						$r["first_name"].(isset($r["last_name"]) ? " ".$r["last_name"] : "").
						(isset($r["username"]) ? " (@".$r["username"].")" : ""), ENT_QUOTES, "UTF-8"
					);
					$text = str_replace("\n", "<br>", htmlspecialchars($r["text"]))	;
					$time = htmlspecialchars($r["created_at"]);
					if ($r["msg_type"] == "photo") {
						$mpdf->WriteHTML(
							"<b>".$name."</b> ".$time."<br>".$text."<br>"
						);
						$mpdf->WriteHTML(
							"<img src=\"data:image/jpg;base64,".base64_encode(file_get_contents(file_storage."/".$r["sha1_checksum"]."_".$r["md5_checksum"].".jpg"))."\">"
						);
						$mpdf->WriteHTML(
							"<br><br>"
						);
					} else {
						$mpdf->WriteHTML(
							"<b>".$name."</b> ".$time."<br>".$text."<br><br>"
						);
					}
				}
				unset($html);
				ob_start();
				$mpdf->Output();
				$content = ob_get_clean();
				file_put_contents($this->sDir."/archives/kulgram_".$this->info["count"].".pdf", $content);
				$this->info["status"] = "sleep";
				unset($this->info["current_session"], $content);
				$reply = "https://webhook-a2.teainside.tech/storage/kulgram/".$this->data["chat_id"]."/archives/kulgram_".$this->info["count"].".pdf";
			} elseif ($this->info["status"] === "sleep") {
				$reply = "An error occured, could not stop system record.\nDevice is not ready, please initialize a session.";
			} elseif ($this->info["status"] === "idle") {
				$reply = "An error occured, could not stop system record.\nDevice is not recording, please start a session.";
			} else {
				$reply = "An error occured, unknown error!";
			}
		} else {
			$reply = Lang::get("admin.command_not_allowed");
		}
		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => $reply,
				"parse_mode" => "HTML",
				"reply_to_message_id" => $this->data["msg_id"]
			]
		);
		return true;
	}

	/**
	 * @param string $judul
	 * @return bool
	 */
	public function init(string $judul)
	{
		if (in_array($this->data["user_id"], SUDOERS) || $this->isAdmin()) {
			if ($this->info["status"] === "sleep") {
				$judul = strtoupper($judul);
				$reply = "Title: <b>".htmlspecialchars($judul)."</b>";
				$this->info["status"] = "idle";
				$this->info["current_session"] = [
					"title" => $judul,
					"countable" => true,
				];
				$this->info["count"]++;
			} else {
				$reply = "An error occured, could not initialize system record.\nDevice is busy, please stop the current session first.";
			}
		} else {
			$reply = Lang::get("admin.command_not_allowed");
		}
		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => $reply,
				"parse_mode" => "HTML",
				"reply_to_message_id" => $this->data["msg_id"]
			]
		);
		return true;
	}

	/**
	 * @return bool
	 */
	public function run()
	{
		return true;
	}
}
