<?php

namespace Bot\Telegram\Contracts;

use Bot\Telegram\Data;

interface LoggerInterface
{
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct(Data $data);

	/**
	 * @return void
	 */
	public function run();
}
