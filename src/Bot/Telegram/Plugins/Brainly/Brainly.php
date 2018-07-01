<?php

namespace Bot\Telegram\Plugins\Brainly;

use Brainly\Brainly as BrainlyBase;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Plugins\Translators
 * @license MIT
 * @since 0.0.1
 */
class Brainly
{
	/**
	 * @var \Brainly\Brainly
	 */
	private $brainly;

	/**
	 * @param string $query
	 *
	 * Constructor.
	 */
	public function __construct(string $query)
	{
		$this->brainly = new BrainlyBase($query);
	}

	/**
	 * @param string $mehtod
	 * @param array	 $parameters
	 * @return mixed
	 */
	public function __call($mehtod, $parameters)
	{
		return $this->brainly->{$mehtod}(...$parameters);
	}
}
