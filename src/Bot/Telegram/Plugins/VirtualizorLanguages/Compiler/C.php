<?php

namespace Bot\Telegram\Plugins\VirtualizorLanguages\Compiler;

use Bot\Telegram\Plugins\VirtualizorLanguages\Compiler;

defined("VIRTUALIZOR_STORAGE_C") or die("VIRTUALIZOR_STORAGE_C is not defined!");
defined("VIRTUALIZOR_BINARY_C") or die("VIRTUALIZOR_BINARY_C is not defined!");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Bot\Telegram\Plugins\VirtualizorLanguages\Compiler
 * @license MIT
 * @since 0.0.1
 */
class C extends Compiler
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
	 * @var string
	 */
	protected $filename;

	/**
	 * @var string
	 */
	protected $binName;

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
		$this->version = "*";
	}

	/**
	 * @return bool
	 */
	protected function compile()
	{
		$this->filename = $filename = VIRTUALIZOR_STORAGE_C."/code/".($this->binName = $this->generateFilename()).".c";
		if (! file_exists(VIRTUALIZOR_STORAGE_C."/code/".$this->binName)) {
			is_dir(VIRTUALIZOR_STORAGE_C) or mkdir(VIRTUALIZOR_STORAGE_C);
			is_dir(VIRTUALIZOR_STORAGE_C."/bin") or mkdir(VIRTUALIZOR_STORAGE_C."/bin");
			is_dir(VIRTUALIZOR_STORAGE_C."/code") or mkdir(VIRTUALIZOR_STORAGE_C."/code");
			if (! file_exists($filename)) {
				$handle = fopen($filename, "w");
				fwrite($handle, $this->code);
				fflush($handle);
				fclose($handle);
			}
			$compile = shell_exec(("sudo ".VIRTUALIZOR_BINARY_C[$this->version])." ".$filename." -o ".VIRTUALIZOR_STORAGE_C."/code/".$this->binName." 2>&1 && echo compiled_successfully");
			return (bool) preg_match("/compiled_successfully/", $compile);
		}
		return true;
	}

	/**
	 * @return string
	 */
	public function run()
	{
		if ($this->compile()) {
			return str_replace(
				realpath(VIRTUALIZOR_STORAGE_C), 
				"/tmp", 
				shell_exec("sudo -u ".$this->user." ".VIRTUALIZOR_STORAGE_C."/code/".$this->binName." 2>&1")
			);
		} else {
			return "Error!";
		}
	}

	/**
	 * @return string
	 */
	private function generateFilename()
	{
		return sha1($this->code);
	}
}
