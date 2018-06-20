<?php

namespace Bot\Telegram;

use Countable;
use ArrayAccess;
use Serializable;
use JsonSerializable;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
final class Session implements Countable, ArrayAccess, Serializable, JsonSerializable
{
	/**
	 * @var null
	 */
	private $__;

	/**
	 * @var array
	 */
	private $container = [];

	/**
	 * @var string
	 */
	private $sessionName;

	/**
	 * @var string
	 */
	private $sessionFile;

	/**
	 * @param string $sessionName
	 *
	 * Constructor.
	 */
	public function __construct(string $sessionName)
	{
		$this->sessionName = $sessionName;
		if (file_exists($this->sessionFile = SESSION_PATH."/{$this->sessionName}.ses")) {
			$this->container = deserialize(file_get_contents($this->sessionFile));
			if (! is_array($this->container)) {
				$this->container = [];
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
	 *
	 */
	public function serialize()
	{
		return $this;
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->container);
	}

	/**
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->container;
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		file_put_contents($this->sessionFile, serialize($this->container));
	}
}
