<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Plugins\Brainly\Brainly;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class Welcome extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function brainly($query)
	{
		$st = new Brainly($query);
		$st->limit(10);
		$st = $st->exec();
		return true;
	}
}
