<?php

namespace Bot\Telegram\Plugins\Stackoverflow;

use Stackoverflow\Stackoverflow as StackoverflowBase;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Plugins\Translators
 * @license MIT
 * @since 0.0.1
 */
class Stackoverflow
{
	/**
	 * @var \Stackoverflow\Stackoverflow
	 */
	private $stackoverflow;

	/**
	 * @param string $query
	 *
	 * Constructor.
	 */
	public function __construct(string $query)
	{
		$this->stackoverflow = new StackoverflowBase($query);
	}

	/**
	 * @param string $mehtod
	 * @param array	 $parameters
	 * @return mixed
	 */
	public function __call($mehtod, $parameters)
	{
		return $this->stackoverflow->{$mehtod}(...$parameters);
	}
}
