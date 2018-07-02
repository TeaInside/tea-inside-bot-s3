<?php

namespace Bot\Telegram\Logger;

use DB;
use Bot\Telegram\Exe;
use Bot\Telegram\Data;
use Bot\Telegram\Contracts\LoggerInterface;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Logger
 * @license MIT
 * @since 0.0.1
 */
class Message implements LoggerInterface
{	
	/**
	 * @var \Bot\Telegram\Data
	 */
	private $data;

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * Consturctor
	 *
	 * @param \Bot\Telegram\Data
	 */
	public function __construct(Data $data)
	{
		$this->data = $data;
		$this->pdo  = DB::pdo();
	}

	/**
	 * @return void
	 */
	public function run()
	{
		if ($this->data["chat_type"] === "private") {
			$this->savePrivateMessage();
		} else {
			$this->saveGroupMessage();
		}
	}

	/**
	 * @return int
	 */
	private function savePhoto()
	{
		$p = $this->data['photo'][count($this->data['photo']) - 1];
		$fileid = $p['file_id'];
		unset($p["file_id"]);
	    $a = json_decode(Exe::getFile([
	        "file_id" => 
	    ])["out"], true);
	    if (isset($a["result"]["file_path"])) {
	        $ch = curl_init("https://api.telegram.org/file/bot".TOKEN."/".$a['result']['file_path']);
	        curl_setopt_array($ch,
	        	[
	        		CURLOPT_RETURNTRANSFER => true,
	        		CURLOPT_SSL_VERIFYPEER => false,
	        		CURLOPT_SSL_VERIFYHOST => false
	        	]
	        );
	        $binary = curl_exec($ch);
	        $filename = ($sha1 = sha1($binary))."_".($md5 = md5($binary)).".jpg";
	        $handle = fopen(file_storage."/".$filename, "w");
	        fwrite($handle, $binary);
	        fclose($handle);
	        $now = date("Y-m-d H:i:s");
	        $this->pdo->prepare(
	        	"INSERT INTO `files` 
	        	(`type`, `md5_checksum`, `sha1_checksum`, `filesize`, `hit_count`, `telegram_file_id`, `info`, `created_at`, `last_hit`) 
	        	VALUES
	        	(:type, :md5_checksum, :sha1_checksum, :filesize, :hit_count, :telegram_file_id, :info, :created_at, :last_hit);"
	        )->execute(
	        	[
	        		":tyoe" => "photo",
	        		":md5_checksum" => $md5,
	        		":sha1_checksum" => $sha1_checksum,
	        		":filesize" => strlen($binary),
	        		":hit_count" => 1,
	        		":telegram_file_id" => $p["file_id"],
	        		":info" => json_encode($p),
	        		":created_at" => $now,
	        		":last_hit" => $now
	        	]
	        );
	        return $this->pdo->lastInsertId();
	    }
	}

	/**
	 * @return void
	 */
	private function savePrivateMessage()
	{
		$st = $this->pdo->prepare("INSERT INTO `private_messages` (`tmsg_id`, `user_id`, `reply_to_tmsg_id`, `created_at`) VALUES (:tmsg_id, :user_id, :reply_to_tmsg_id, :created_at);");
		$st->execute(
			[
				":tmsg_id" => $this->data["msg_id"],
				":user_id" => $this->data["user_id"],
				":reply_to_tmsg_id" => (isset($this->data->in["message"]["reply_to_message"]) ? $this->data->in["message"]["reply_to_message"]["message_id"] : null),
				":created_at" => date("Y-m-d H:i:s")
			]
		);
		$lastInsertId = $this->pdo->lastInsertId();
		$st = $this->pdo->prepare("INSERT INTO `private_messages_data` (`message_id`, `text`, `file`, `type`) VALUES (:message_id, :text, :file, :type);");
		$data =	[
			":message_id" => $lastInsertId,
			":text" => $this->data["text"],
			":file" => null,
			":type" => $this->data["msg_type"]
		];
		switch ($this->data["msg_type"]) {
			case "text":
				$st->execute($data);
				break;
			case "photo":
				$fx = $this->savePhoto();
				$data[":file"] = $fx;
				$st->execute($data);
				break;
			case "sticker":
				$file_id = $this->data["sticker"]["file_id"];
				unset($this->data["sticker"]["file_id"]);
				$st->execute(
					[
						":message_id" => $lastInsertId,
						":text" => json_encode($this->data["sticker"]),
						":file" => $file_id,
						":type" => $this->data["msg_type"]
					]
				);
				break;
			case "voice":
			var_dump(123);
				$file_id = $this->data["voice"]["file_id"];
				unset($this->data["voice"]["file_id"]);
				$st->execute(
					[
						":message_id" => $lastInsertId,
						":text" => json_encode($this->data["voice"]),
						":file" => $file_id,
						":type" => $this->data["msg_type"]
					]
				);
				break;
			default:
				$st->execute(
					[
						":message_id" => $lastInsertId,
						":text" => $this->data["text"],
						":file" => null,
						":type" => "unknown"
					]
				);
				break;
		}
	}

	/**
	 * @return void
	 */
	private function saveGroupMessage()
	{
		$st = $this->pdo->prepare("INSERT INTO `group_messages` (`group_id`, `tmsg_id`, `user_id`, `reply_to_tmsg_id`, `created_at`) VALUES (:group_id, :tmsg_id, :user_id, :reply_to_tmsg_id, :created_at);");
		$st->execute(
			[
				":group_id" => $this->data["group_id"],
				":tmsg_id" => $this->data["msg_id"],
				":user_id" => $this->data["user_id"],
				":reply_to_tmsg_id" => (isset($this->data->in["message"]["reply_to_message"]) ? $this->data->in["message"]["reply_to_message"]["message_id"] : null),
				":created_at" => date("Y-m-d H:i:s")
			]
		);
		$lastInsertId = $this->pdo->lastInsertId();

		$st = $this->pdo->prepare("INSERT INTO `group_messages_data` (`message_id`, `text`, `file`, `type`) VALUES (:message_id, :text, :file, :type);");
		switch ($this->data["msg_type"]) {
			case "text":
				$st->execute(
					[
						":message_id" => $lastInsertId,
						":text" => $this->data["text"],
						":file" => null,
						":type" => $this->data["msg_type"]
					]
				);
				break;
			case "photo":
				$st->execute(
					[
						":message_id" => $lastInsertId,
						":text" => $this->data["text"],
						":file" => $this->data['photo'][count($this->data['photo']) - 1]["file_id"],
						":type" => $this->data["msg_type"]
					]
				);
				break;
			case "sticker":
				$file_id = $this->data["sticker"]["file_id"];
				unset($this->data["sticker"]["file_id"]);
				$st->execute(
					[
						":message_id" => $lastInsertId,
						":text" => json_encode($this->data["sticker"]),
						":file" => $file_id,
						":type" => $this->data["msg_type"]
					]
				);
				break;
			case "voice":
			var_dump(123);
				$file_id = $this->data["voice"]["file_id"];
				unset($this->data["voice"]["file_id"]);
				$st->execute(
					[
						":message_id" => $lastInsertId,
						":text" => json_encode($this->data["voice"]),
						":file" => $file_id,
						":type" => $this->data["msg_type"]
					]
				);
				break;
			default:
				$st->execute(
					[
						":message_id" => $lastInsertId,
						":text" => $this->data["text"],
						":file" => null,
						":type" => "unknown"
					]
				);
				break;
		}
	}
}
