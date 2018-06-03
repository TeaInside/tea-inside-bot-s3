<?php

namespace Bot\Telegram\Plugins\VirtualizorLanguages;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Bot\Telegram\Plugins\VirtualizorLanguages
 * @license MIT
 * @since 0.0.1
 */
abstract class Interpreter
{
	/**
	 * @var string
	 */
	protected $code;

	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * @param string $code
	 * @return void
	 *
	 * Constructor.
	 */
	abstract public function __construct($code);

	/**
	 * @return string
	 */
	abstract public function run();
}
