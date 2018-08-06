<?php

namespace Bot\Telegram;

use ArrayAccess;
use JsonSerializable;
use Bot\Telegram\Exceptions\InvalidJsonDataException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
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
	 * @throws \Bot\Telegram\Exceptions\InvalidJsonDataException
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
		$this["entities"] = isset($this->in["message"]["entities"]) ? $this->in["message"]["entities"] : null;
		$this["reply_to"] = isset($this->in["message"]["reply_to_message"]) ? $this->in["message"]["reply_to_message"] : null;
		if (isset($this->in["message"]["new_chat_members"])) {
			
			$this["msg_type"] = "new_chat_members";
			$this["new_chat_members"] = $this->in["message"]["new_chat_members"];
			$this["update_id"] = $this->in["update_id"];
			$this["msg_id"] = $this->in["message"]["message_id"];
			$this["chat_id"] = $this->in["message"]["chat"]["id"];
			$this["is_bot"] = $this->in["message"]["from"]["is_bot"];

		} else if (isset($this->in["message"]["text"])) {

			$this["msg_type"] = "text";
			if ($this->in["message"]["chat"]["type"] === "private") {
				$this["chat_type"] = "private";
			} else {
				$this["chat_type"] = "group";
				$this["group_id"] = $this->in["message"]["chat"]["id"];
				$this["group_name"] = $this->in["message"]["chat"]["title"];
				$this["group_type"] = $this->in["message"]["chat"]["type"];
				$this["group_username"] = isset($this->in["message"]["chat"]["username"]) ? $this->in["message"]["chat"]["username"] : null;
			}
			$this["update_id"] = $this->in["update_id"];
			$this["msg_id"] = $this->in["message"]["message_id"];
			$this["chat_id"] = $this->in["message"]["chat"]["id"];
			$this["user_id"] = $this->in["message"]["from"]["id"];
			$this["first_name"] = $this->in["message"]["from"]["first_name"];
			$this["last_name"] = (
				isset($this->in["message"]["from"]["last_name"]) ? 
					$this->in["message"]["from"]["last_name"] : null
			);
			$this["name"] = (
				$this["first_name"] . (
					isset($this["last_name"]) ?
						" ".$this["last_name"] :
							""
				)
			);
			$this["username"] = (
				isset($this->in["message"]["from"]["username"]) ?
					$this->in["message"]["from"]["username"] : null
			);
			$this["language_code"] = (
				isset($this->in["message"]["from"]["language_code"]) ?
					$this->in["message"]["from"]["language_code"] : null
			);
			$this["is_bot"] = $this->in["message"]["from"]["is_bot"];
			$this["text"] = $this->in["message"]["text"];
			$this["date"] = $this->in["message"]["date"];
			$this["entities"] = (
				isset($this->in["message"]["entities"]) ?
					$this->in["message"]["entities"] : null
			);

		} else if (isset($this->in["message"]["photo"])) {

			$this["msg_type"] = "photo";
			$this["photo"] = $this->in["message"]["photo"];
			if ($this->in["message"]["chat"]["type"] === "private") {
				$this["chat_type"] = "private";
			} else {
				$this["chat_type"] = "group";
				$this["group_id"] = $this->in["message"]["chat"]["id"];
				$this["group_name"] = $this->in["message"]["chat"]["title"];
				$this["group_type"] = $this->in["message"]["chat"]["type"];
				$this["group_username"] = isset($this->in["message"]["chat"]["username"]) ? $this->in["message"]["chat"]["username"] : null;
			}
			$this["update_id"] = $this->in["update_id"];
			$this["msg_id"] = $this->in["message"]["message_id"];
			$this["chat_id"] = $this->in["message"]["chat"]["id"];
			$this["user_id"] = $this->in["message"]["from"]["id"];
			$this["first_name"] = $this->in["message"]["from"]["first_name"];
			$this["last_name"] = (
				isset($this->in["message"]["from"]["last_name"]) ? 
					$this->in["message"]["from"]["last_name"] : null
			);
			$this["name"] = (
				$this["first_name"] . (
					isset($this["last_name"]) ?
						" ".$this["last_name"] :
							""
				)
			);
			$this["username"] = (
				isset($this->in["message"]["from"]["username"]) ?
					$this->in["message"]["from"]["username"] : null
			);
			$this["language_code"] = (
				isset($this->in["message"]["from"]["language_code"]) ?
					$this->in["message"]["from"]["language_code"] : null
			);
			$this["is_bot"] = $this->in["message"]["from"]["is_bot"];
			$this["text"] = isset($this->in["message"]["caption"]) ? $this->in["message"]["caption"] : null;
			$this["date"] = $this->in["message"]["date"];
			$this["entities"] = (
				isset($this->in["message"]["entities"]) ?
					$this->in["message"]["entities"] : null
			);

		} else if(isset($this->in["message"]["sticker"])) {

			$this["msg_type"] = "sticker";
			$this["sticker"] = $this->in["message"]["sticker"];
			$this["text"] = $this["sticker"]["emoji"]." (".$this["sticker"]["set_name"].")";
			if ($this->in["message"]["chat"]["type"] === "private") {
				$this["chat_type"] = "private";
			} else {
				$this["chat_type"] = "group";
				$this["group_id"] = $this->in["message"]["chat"]["id"];
				$this["group_name"] = $this->in["message"]["chat"]["title"];
				$this["group_type"] = $this->in["message"]["chat"]["type"];
				$this["group_username"] = isset($this->in["message"]["chat"]["username"]) ? $this->in["message"]["chat"]["username"] : null;
			}
				$this["update_id"] = $this->in["update_id"];
			$this["msg_id"] = $this->in["message"]["message_id"];
			$this["chat_id"] = $this->in["message"]["chat"]["id"];
			$this["user_id"] = $this->in["message"]["from"]["id"];
			$this["first_name"] = $this->in["message"]["from"]["first_name"];
			$this["last_name"] = (
				isset($this->in["message"]["from"]["last_name"]) ? 
					$this->in["message"]["from"]["last_name"] : null
			);
			$this["name"] = (
				$this["first_name"] . (
					isset($this["last_name"]) ?
						" ".$this["last_name"] :
							""
				)
			);
			$this["username"] = (
				isset($this->in["message"]["from"]["username"]) ?
					$this->in["message"]["from"]["username"] : null
			);
			$this["language_code"] = (
				isset($this->in["message"]["from"]["language_code"]) ?
					$this->in["message"]["from"]["language_code"] : null
			);
			$this["is_bot"] = $this->in["message"]["from"]["is_bot"];
			$this["date"] = $this->in["message"]["date"];

		} else if (isset($this->in["message"]["voice"])) {
			$this["msg_type"] = "voice";
			$this["voice"] = $this->in["message"]["voice"];
			$this["text"] = null;
			if ($this->in["message"]["chat"]["type"] === "private") {
				$this["chat_type"] = "private";
			} else {
				$this["chat_type"] = "group";
				$this["group_id"] = $this->in["message"]["chat"]["id"];
				$this["group_name"] = $this->in["message"]["chat"]["title"];
				$this["group_type"] = $this->in["message"]["chat"]["type"];
				$this["group_username"] = isset($this->in["message"]["chat"]["username"]) ? $this->in["message"]["chat"]["username"] : null;
			}
				$this["update_id"] = $this->in["update_id"];
			$this["msg_id"] = $this->in["message"]["message_id"];
			$this["chat_id"] = $this->in["message"]["chat"]["id"];
			$this["user_id"] = $this->in["message"]["from"]["id"];
			$this["first_name"] = $this->in["message"]["from"]["first_name"];
			$this["last_name"] = (
				isset($this->in["message"]["from"]["last_name"]) ? 
					$this->in["message"]["from"]["last_name"] : null
			);
			$this["name"] = (
				$this["first_name"] . (
					isset($this["last_name"]) ?
						" ".$this["last_name"] :
							""
				)
			);
			$this["username"] = (
				isset($this->in["message"]["from"]["username"]) ?
					$this->in["message"]["from"]["username"] : null
			);
			$this["language_code"] = (
				isset($this->in["message"]["from"]["language_code"]) ?
					$this->in["message"]["from"]["language_code"] : null
			);
			$this["is_bot"] = $this->in["message"]["from"]["is_bot"];
			$this["date"] = $this->in["message"]["date"];
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
		return array_key_exists($offset, $this->container) && !is_null($this->container[$offset]);
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
