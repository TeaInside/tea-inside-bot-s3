<?php

namespace Bot\Telegram;

use Bot\Telegram\Lang\Map;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
final class Lang
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	private $data;

	/**
	 * @var string
	 */
	private $lang;

	/**
	 * @var string
	 */
	private $submapClass;

	/**
	 * @var self
	 */
	private static $self;

	/**
	 * @param \Bot\Telegram\Data
	 * @return void
	 */
	private function __construct(Data $data, $lang)
	{
		$this->fallback = "en";
		$this->lang = $lang;
		$this->data = $data;
		$this->namespace = __NAMESPACE__ . Map::$map[$this->lang];
		$this->submapClass = __NAMESPACE__ . Map::$map[$this->lang] . "SubMap";
	}

	public static function init(Data $data, $lang = "en")
	{
		self::$self = new self($data, $lang);
	}

	public static function getInstance()
	{
		return self::$self;
	}

	/**
	 * @param string $key
	 * @param array  $bind
	 * @param bool   $noBind
	 */
	public static function get(string $key, $bind = [], $noBind = false): string
	{
		$ins = self::getInstance();
		if (array_key_exists($key, $ins->submapClass::$subMap)) {
			$r = ($ins->namespace."Fx\\".$ins->submapClass::$subMap[$key]);
			$key = explode(".", $key);
			return str_replace(array_keys($bind), array_values($bind), ($noBind ? $r::$map[$key[1]] : self::bind($r::$map[$key[1]])));
		} else {
			throw new \Error("");
		}
	}

	public static function namelink($userId, $name)
	{
		return "<a href=\"tg://user?id={$userId}\">".htmlspecialchars($name, ENT_QUOTES, "UTF-8")."</a>";
	}

	public static function bind(string $str): string
	{
		$ins = self::getInstance();
		$ins->replacer($str);

		return $str;
	}

	private function replacer(string $str): string
	{
		$r1 = [

		];

		$r2 = [

		];

		return str_replace($r1, $r2, $str);
	}
}
