<?php

namespace Bot\Telegram;

use Bot\Telegram\Data;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
abstract class ResponseFoundation
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	protected $data;

	/**
	 * @param \Bot\Telegram\Data $data
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct(Data $data)
	{
		$this->data = $data;
	}

	public function setUp()
	{
	}
}
