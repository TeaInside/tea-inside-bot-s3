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
class Group implements LoggerInterface
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
		$st = $this->pdo->prepare("SELECT `name`,`username`,`link`,`photo`,`msg_count` FROM `groups` WHERE `id`=:id LIMIT 1;");
		$st->execute([":id" => $this->data["group_id"]]);
		if ($u = $st->fetch(PDO::FETCH_ASSOC)) {
			$this->updateGroup($u);
		} else {
			$this->addNewGroup();
		}
	}

	/**
	 * @return void
	 */
	private function updateGroup($u)
	{
		if (($u["msg_count"] % 10) === 0) {
			$st = new AdminLogger($this->data);
			$st->run = 1;
			$st->reset = 1;
			$st->run();
			Exe::sendMessage(
				[
					"chat_id" => $this->data["group_id"],
					"text" => "Success!"
				]
			);
		}

		$query	= "UPDATE `groups` SET ";
		$data	= [];
		$now	= date("Y-m-d H:i:s");

		if ($u["name"] !== $this->data["group_name"]) {
			$query .= "`name`=:name, ";
			$data[":name"] = $this->data["group_name"];
		}

		if ($u["username"] !== $this->data["group_username"]) {
			$query .= "`username`=:username, ";
			$data[":username"] = $this->data["group_username"];
		}

		$query .= "`msg_count`=`msg_count`+1, ";

		if (! empty($data)) {
			$query .= "`updated_at`=:updated_at, ";
			$data[":updated_at"] = $now;
			$this->addGroupHistory($now);
		}

		$query .= "`last_seen`=:last_seen ";
		$data[":last_seen"] = $now;

		$query .= "WHERE `id`=:id LIMIT 1;";
		$data[":id"] = $this->data["group_id"];

		$st = $this->pdo->prepare($query);
		$st->execute($data);
	}

	/**
	 * @return void
	 */
	private function addNewGroup()
	{
		$now = date("Y-m-d H:i:s");
		$st = $this->pdo->prepare("INSERT INTO `groups` (`id`, `name`, `username`, `link`, `photo`, `msg_count`, `created_at`, `updated_at`, `last_seen`) VALUES (:id, :name, :username, :link, :photo, :msg_count, :created_at, :updated_at, :last_seen);");
		$st->execute(
			[
				":id" => $this->data["group_id"],
				":name" => $this->data["group_name"],
				":username" => $this->data["group_username"],
				":link" => null,
				":photo" => null,
				":msg_count" => 1,
				":created_at" => $now,
				":updated_at" => null,
				":last_seen" => $now
			]
		);

		$st = $this->pdo->prepare("INSERT INTO `group_settings` (`group_id`) VALUES (:group_id);");
		$st->execute([":group_id" => $this->data["group_id"]]);

		$this->addGroupHistory();

		$st = new AdminLogger($this->data);
		$st->run = 1;
		$st->run();
	}

	/**
	 * @param string $now
	 * @return void
	 */
	private function addGroupHistory($now = null)
	{
		$st = $this->pdo->prepare("INSERT INTO `groups_history` (`group_id`, `name`, `username`, `link`, `photo`, `created_at`) VALUES (:group_id, :name, :username, :link, :photo, :created_at);");
		$st->execute(
			[
				":group_id" => $this->data["group_id"],
				":name" => $this->data["group_name"],
				":username" => $this->data["group_username"],
				":link" => null,
				":photo" => null,
				":created_at" => (is_null($now) ? date("Y-m-d H:i:s") : $now)
			]
		);
	}
}
