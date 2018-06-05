<?php

namespace Bot\Telegram\Logger;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\Data;
use Bot\Telegram\Contracts\LoggerInterface;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Logger
 * @license MIT
 * @since 0.0.1
 */
class AdminLogger implements LoggerInterface
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
	 * @var bool
	 */
	public $run = 1;

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
		if ($this->run) {
			$a = Exe::getChatAdministrators(
				[
					"chat_id" => $this->data["group_id"]
				]
			);

			$a = json_decode($a["out"], true);

			$query = "INSERT INTO `group_admins` (`group_id`, `user_id`, `role`, `created_at`) VALUES ";
			$now = date("Y-m-d H:i:s");
			foreach ($a["result"] as $key => $admin) {

				$query .= "(:group_id, :user_id_{$key}, :role_{$key}, :created_at_{$key}),";

				$data[":group_id"] = $this->data["group_id"];
				$data[":user_id_{$key}"] = $admin["user"]["id"];
				$data[":role_{$key}"] = $admin["status"];
				$data[":created_at_{$key}"] = $now;

				$admin = $admin["user"];
				if (! isset($admin["last_name"])) {
					$admin["last_name"] = null;
				}

				if (! isset($admin["username"])) {
					$admin["last_name"] = null;
				}

				$st = $this->pdo->prepare("SELECT `first_name`,`last_name`,`username`,`photo` FROM `users` WHERE `id`=:id LIMIT 1;");
				$st->execute([":id" => $admin["id"]]);
				if ($u = $st->fetch(PDO::FETCH_ASSOC)) {
					$this->updateUser($u, $admin);
				} else {
					$this->addNewUser($admin);
				}
			}
			$st = $this->pdo->prepare(trim($query, ","));
			$st->execute($data);
		}
	}

	/**
	 * @param array $u
	 * @param array $i
	 * @return void
	 */
	private function updateUser($u, $i)
	{
		$query	= "UPDATE `users` SET ";
		$data	= [];
		$now	= date("Y-m-d H:i:s");

		if ($u["first_name"] !== $i["first_name"]) {
			$query .= "`first_name`=:first_name, ";
			$data[":first_name"] = $this->data["first_name"];
		}

		if ($u["last_name"] !== $i["last_name"]) {
			$query .= "`last_name`=:last_name, ";
			$data[":last_name"] = $this->data["last_name"];
		}

		if ($u["username"] !== $i["username"]) {
			$query .= "`username`=:username, ";
			$data[":username"] = $this->data["username"];
		}

		if (! empty($data)) {
			$query .= "`updated_at`=:updated_at ";
			$data[":updated_at"] = $now;
			$this->addUserHistory($now, $u, $i);

			$query .= " WHERE `id`=:id LIMIT 1;";
			$data[":id"] = $i["user_id"];
			
			$st = $this->pdo->prepare($query);
			$exe = $st->execute($data);
		}
	}

	/**
	 * @param array $u
	 * @param array $i
	 * @return void
	 */
	private function addNewUser($i)
	{
		$now = date("Y-m-d H:i:s");

		$st = $this->pdo->prepare("INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `photo`, `private_message_count`, `group_message_count`, `created_at`, `updated_at`, `last_seen`) VALUES (:id, :first_name, :last_name, :username, :photo, :private_message_count, :group_message_count, :created_at, :updated_at, :last_seen);");
		$st->execute(
			[
				":id" => $i["id"],
				":first_name" => $i["first_name"],
				":last_name" => $i["last_name"],
				":username" => $i["username"],
				":photo" => null,
				":private_message_count" => 0,
				":group_message_count" => 0,
				":created_at" => $now,
				":updated_at" => null,
				":last_seen" => null
			]
		);

		$this->addUserHistory($now, $i);
	}

	/**
	 * @param string $now
	 * @param array $u
	 * @param array $i
	 * @return void
	 */
	private function addUserHistory($now, $i)
	{
		$st = $this->pdo->prepare("INSERT INTO `users_history` (`user_id`, `first_name`, `last_name`, `username`, `photo`, `created_at`) VALUES (:user_id, :first_name, :last_name, :username, :photo, :created_at);");
		$st->execute(
			[
				":user_id" => $i["id"],
				":first_name" => $i["first_name"],
				":last_name" => $i["last_name"],
				":username" => $i["username"],
				":photo" => null,
				":created_at" => $now
			]
		);
	}
}