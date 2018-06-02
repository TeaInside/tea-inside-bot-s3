<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Responses
 * @license MIT
 * @since 0.0.1
 */
class VirtualizorLanguages extends ResponseFoundation
{
	/**
	 * @param string $cmd
	 * @return bool
	 */
	public function run(string $cmd)
	{
		switch ($cmd) {
			case 'php':
				
				break;
			
			default:
				# code...
				break;
		}
	}
}
