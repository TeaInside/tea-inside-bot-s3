<?php

namespace Bot\Telegram\Logger;

use DB;
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
		if ($this->data["msg_type"] === "text") {
			$st = $this->pdo->prepare("INSERT INTO `private_messages_data` (`message_id`, `text`, `file`, `type`) VALUES (:message_id, :text, :file, :type);");
			$st->execute(
				[
					":message_id" => $lastInsertId,
					":text" => $this->data["text"],
					":file" => null,
					":type" => "text"
				]
			);
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
		var_dump($this->data["msg_type"]);
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
			default:
				# code...
				break;
		}
	}
}
