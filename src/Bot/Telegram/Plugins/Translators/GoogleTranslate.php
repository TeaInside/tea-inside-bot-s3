<?php

namespace Bot\Telegram\Plugins\Translators;

use GoogleTranslate\GoogleTranslate as BaseGoogleTranslate;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram\Plugins\Translators
 * @license MIT
 * @since 0.0.1
 */
class GoogleTranslate
{
	/**
	 * @var \GoogleTranslate\GoogleTranslate
	 */
	private $googleTranslate;

	/**
	 * @param string $text
	 * @param string $from
	 * @param string $to
	 *
	 * Constructor.
	 */
	public function __construct($text, $from, $to)
	{
		$this->googleTranslate = new BaseGoogleTranslate($text, $from, $to);
	}

	/**
	 * @param string $mehtod
	 * @param array	 $parameters
	 * @return mixed
	 */
	public function __call($mehtod, $parameters)
	{
		return $this->googleTranslate->{$mehtod}(...$parameters);
	}
}
