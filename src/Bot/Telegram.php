<?php

namespace Bot;

use Bot\Telegram\Data;
use Bot\Telegram\Response;

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
		$resp = new Response(new Data($json));
		$resp->run();
	}
}
