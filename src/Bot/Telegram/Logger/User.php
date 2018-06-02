<?php

namespace Bot\Telegram\Logger;

use DB;
use PDO;
use Bot\Telegram\Data;
use Bot\Telegram\Contracts\LoggerInterface;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
class User implements LoggerInterface
{	
	/**
	 * @var \Bot\Telegram\Data
	 */
	private $data;

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
		$st = $this->pdo->prepare("SELECT `first_name`,`last_name`,`username`,`photo` FROM `users` WHERE `id`=:id LIMIT 1;");
		$st->execute([":id" => $this->data["user_id"]]);
		if ($u = $st->fetch(PDO::FETCH_ASSOC)) {
			
		} else {
			$this->addNewUser();
		}
	}

	/**
	 * @return void
	 */
	private function addNewUser()
	{
		$st = $this->pdo->prepare("INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `photo`, `private_message_count`, `group_message_count`, `created_at`, `updated_at`, `last_seen`) VALUES (:id, :first_name, :last_name, :username, :photo, :private_message_count, :group_message_count, :created_at, :updated_at, :last_seen);");
		$now = date("Y-m-d H:i:s");
		$isPrivate = $this->data["chat_type"] === "private";
		$st->execute(
			[
				":id" => $this->data["user_id"],
				":first_name" => $this->data["first_name"],
				":last_name" => $this->data["last_name"],
				":username" => $this->data["username"],
				":photo" => null,
				":private_message_count" => ($isPrivate ? 1 : 0),
				":group_message_count" => ($isPrivate ? 0 : 1),
				":created_at" => $now,
				":updated_at" => null,
				":last_seen" => $now
			]
		);

		$st = $this->pdo->prepare("INSERT INTO `users_history` (`user_id`, `first_name`, `last_name`, `username`, `photo`, `created_at`) VALUES (:user_id, :first_name, :last_name, :username, :photo, :created_at);");
		$st->execute(
			[
				":user_id" => $this->data["user_id"],
				":first_name" => $this->data["first_name"],
				":last_name" => $this->data["last_name"],
				":username" => $this->data["username"],
				":photo" => null,
				":created_at" => $now
			]
		);
	}
}
