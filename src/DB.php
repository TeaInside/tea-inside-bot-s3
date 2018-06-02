<?php

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @since 0.0.1
 */
final class DB
{
	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->pdo = new PDO(
			"mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME,
			DB_USER,
			DB_PASS
		);
	}

	/**
	 * @return \PDO
	 */
	public static function pdo()
	{
		return self::getInstance()->pdo;
	}

	/**
	 * @return self
	 */
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
