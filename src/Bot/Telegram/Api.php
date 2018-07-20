<?php

namespace Bot\Telegram;

use DB;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
abstract class Api
{
	public function __construct()
	{
		$this->pdo = DB::pdo();
	}
}
