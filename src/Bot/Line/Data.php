<?php

namespace Bot\Line;

use ArrayAccess;
use JsonSerializable;
use Bot\Line\Exceptions\InvalidJsonDataException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Line
 * @license MIT
 * @since 0.0.1
 */
final class Data implements ArrayAccess, JsonSerializable
{
	private $__;

	/**
	 * @var array
	 */
	public $in = [];

	/**
	 * @var array
	 */
	private $container = [];

	/**
	 * @param string $jsonString
	 * @throws \Bot\Line\Exceptions\InvalidJsonDataException
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct(string $jsonString)
	{
		$this->in = json_decode($jsonString, true);
		if (! is_array($this->in)) {
			throw new InvalidJsonDataException(
				"The result of json_decode must be an array"
			);
		}
		$this->buildContainer();
	}

	/**
	 * @return void
	 */
	private function buildContainer()
	{
		if (isset($this->in["events"])) {
			$this["events"] = $this->in["events"];
		}
	}

	/**
	 * @param array $data
	 * @return void
	 */
	public function fullUpdate($data)
	{
		$this->container = [];
		if (isset($data["type"]) && $data["type"] === "message") {
			$this["type"] = "message";
			if (isset($data["reply_token"])) {
				$this["reply_token"] = $data["reply_token"]; 
			}
			if (isset($data["source"]["type"])) {
				$this["user_id"] = $data["source"]["userId"];
				if ($data["source"]["type"] === "user") {
					$this["chat_id"] = $data["source"]["userId"];
					$this["chat_type"] = "private";
				} else {
					$this["chat_id"] = $data["source"]["groupId"];
					$this["chat_type"] = "group";
				}
			}
			if (isset($data["timestamp"])) {
				$this["timestamp"] = $data["timestamp"];
			}
			if (isset($data["message"]["type"])) {
				if ($data["message"]["type"] === "text") {
					$this["msg_type"] = "text";
					$this["text"] = $data["message"]["text"];
					$this["msg_id"] = $data["message"]["id"];
				}
			}
		}
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->container[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function &offsetGet($offset)
	{
		if ($this->offsetExists($offset)) {
			return $this->container[$offset];
		} else {
			return $this->__;
		}
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->container);
	}

	/**
	 * @param mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->container[$offset]);
	}

	/**
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->container;
	}
}
