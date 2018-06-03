<?php

namespace Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter;

use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter;

defined("VIRTUALIZOR_STORAGE_PHP") or die("VIRTUALIZOR_STORAGE_PHP is not defined!");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter
 * @license MIT
 * @since 0.0.1
 */
class PHP extends Interpreter
{
	/**
	 * @var string
	 */
	protected $code;

	/**
	 * @var string
	 */
	public $user;

	/**
	 * @param string $code
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct($code)
	{
		$this->code = $code;
		$this->user = trim(shell_exec("whoami"));
	}

	/**
	 * @return string
	 */
	public function run()
	{
		if (! is_dir(VIRTUALIZOR_STORAGE_PHP)) {
			mkdir(VIRTUALIZOR_STORAGE_PHP);
		}
		$filename	= VIRTUALIZOR_STORAGE_PHP."/".($shortName = $this->generateFilename());
		if (! file_exists($filename)) {
			$handle 	= fopen($filename,"w");
			fwrite($handle, $this->code);
			fflush($handle);
			fclose($handle);
		}
		$exe = shell_exec("sudo -u ".$this->user." ".PHP_BINARY." ".$filename." 2>&1");
		return str_replace(realpath($filename), "/tmp/".$shortName, $exe);
	}

	/**
	 * @return string
	 */
	private function generateFilename()
	{
		return substr(sha1(sha1($this->code).md5($this->code)), 0, 5).".php";
	}
}
