<?php

namespace Bot\Telegram\Logger;

use DB;
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
		$st = $this->pdo->prepare("SELECT `first_name`,`last_name`,`username`,`photo` FROM `users` WHERE `id`=:user_id LIMIT 1;");
		$st->execute([":user_id" => $this->data["user_id"]]);
		if ($u = $st->fetch(PDO::FETCH_ASSOC)) {
			
		} else {
			
		}
	}
}
