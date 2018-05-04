<?php

namespace Bot;

use Bot\Telegram\Data;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot
 * @license MIT
 * @version 0.0.1
 */
final class Telegram
{
	public static function run(string $json)
	{
		$data = new Data($json);
	}
}
