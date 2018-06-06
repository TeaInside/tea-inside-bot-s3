<?php

namespace Bot\Line;

use Bot\Line\Data;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Line
 * @license MIT
 * @since 0.0.1
 */
abstract class ResponseFoundation
{
	/**
	 * @var \Bot\Line\Data
	 */
	protected $data;

	/**
	 * @param \Bot\Line\Data $data
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct(Data $data)
	{
		$this->data = $data;
	}
}
