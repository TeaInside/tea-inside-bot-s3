<?php

namespace Bot;

use Bot\Line\Data;
use Bot\Line\Response;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot
 * @license MIT
 * @version 0.0.1
 */
final class Line
{
	public static function run(string $json)
	{
		$st = new Data($json);
		if (isset($st["events"])) {
			foreach ($st["events"] as $v) {
				$st->fullUpdate($v);
				$resp = new Response($st);
				$resp->run();
			}
		}
	}
}
