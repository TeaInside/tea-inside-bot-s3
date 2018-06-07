<?php

namespace Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter;

use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter;

defined("VIRTUALIZOR_STORAGE_RUBY") or die("VIRTUALIZOR_STORAGE_RUBY is not defined!");
defined("VIRTUALIZOR_BINARY_RUBY") or die("VIRTUALIZOR_BINARY_RUBY is not defined!");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter
 * @license MIT
 * @since 0.0.1
 */
class Ruby extends Interpreter
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
	 * @var string
	 */
	public $version;

	/**
	 * @param string $code
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct($code)
	{
		$this->code = $code;
		$this->user = "www-data";
		$this->version = "2.3.1";
	}

	/**
	 * @return string
	 */
	public function run()
	{
		if (! is_dir(VIRTUALIZOR_STORAGE_RUBY)) {
			mkdir(VIRTUALIZOR_STORAGE_RUBY);
		}
		$filename	= VIRTUALIZOR_STORAGE_RUBY."/".($shortName = $this->generateFilename());
		if (! file_exists($filename)) {
			$handle = fopen($filename,"w");
			fwrite($handle, $this->code);
			fflush($handle);
			fclose($handle);
		}
		shell_exec("sudo chmod 775 ".$filename);
		$exe = shell_exec("sudo -u ".$this->user." ".(VIRTUALIZOR_BINARY_RUBY[$this->version])." ".$filename." 2>&1");
		return str_replace(realpath(VIRTUALIZOR_STORAGE_RUBY), "/tmp", $exe);
	}

	/**
	 * @return string
	 */
	private function generateFilename()
	{
		return substr(sha1(sha1($this->code).md5($this->code)), 0, 5).".php";
	}
}