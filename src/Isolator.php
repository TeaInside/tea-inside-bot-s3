<?php

use Exceptions\IsolatorException;
use Contracts\Isolator as IsolatorContract;

defined("ISOLATOR") or die("ISOLATOR is not defined yet!\n");
defined("ISOLATOR_TMP") or die("ISOLATOR_TMP is not defined yet!\n");
defined("ISOLATOR_ETC") or die("ISOLATOR_ETC is not defined yet!\n");
defined("ISOLATOR_HOME") or die("ISOLATOR_HOME is not defined yet!\n");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 * @since 0.0.1
 */
final class Isolator implements IsolatorContract
{
	/**
	 * @var string
	 */
	private $cmd;

	/**
	 * @var int
	 */
	private $boxId = 0;

	/**
	 * @var string
	 */
	private $user = "www-data";

	/**
	 * @var string
	 */
	private $boxDir = "/var/local/lib/isolate/0/box";

	/**
	 * @var int
	 */
	private $maxWallTime;

	/**
	 * @var int
	 */
	private $extraTime;

	/**
	 * @var int
	 */
	private $maxProcesses = 1;

	/**
	 * @var int
	 */
	private $maxExecutionTime = 30;

	/**
	 * @var int
	 */
	private $memoryLimit = 102400;

	/**
	 * @var bool
	 */
	private $isExecuted = false;

	/**
	 * @var int
	 */
	private $userId;

	/**
	 * @var string
	 */
	private $homePath;

	/**
	 * @var string
	 */
	private $tmpPath;

	/**
	 * @var string
	 */
	private $etcPath;

	/**
	 * @param string $cmd
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct(string $userId)
	{
		$this->userId = $userId;

		if (! is_dir($this->homePath = ISOLATOR_HOME."/".$userId)) {
			mkdir($this->homePath);
			mkdir($this->homePath."/u".$userId);
			shell_exec("sudo chmod -R 775 ".$this->homePath);
			shell_exec("sudo chown -R 6000{$userId}:www-data ".$this->homePath);
		}

		if (! is_dir($this->tmpPath = ISOLATOR_TMP."/".$userId)) {
			mkdir($this->tmpPath);
			shell_exec("sudo chmod -R 777 ".$this->tmpPath);
		}

		if (! is_dir($this->etcPath = ISOLATOR_ETC."/".$userId)) {
			mkdir($this->etcPath);
		}

		$this->setBoxId($userId);

	}

	/**
	 * @param string $unique
	 * @return int
	 */
	public static function generateUserId($unique)
	{
		if (! file_exists($f = ISOLATOR."/id_map")) {
			$data = [
				"count" => 1,
				"data" => [
					$unique => 0
				]
			];

			$r = 0;
		} else {
			$data = json_decode(file_get_contents(ISOLATOR."/id_map"), true);
			if (isset($data["count"], $data["data"]) && is_array($data["data"])) {
				if (isset($data["data"][$unique])) {
					$r = $data["data"][$unique];
				} else {
					$data["data"][$unique] = $r = $data["count"]++;
				}
			} else {
				$data = [
					"count" => 1,
					"data" => [
						$unique => 0
					]
				];

				$r = 0;
			}
		}


		file_put_contents(ISOLATOR."/id_map", json_encode($data));

		return $r;
	}
 
	/**
	 * @param string $user
	 * @return void
	 *
	 * Set user.
	 */
	public function setUser(string $user)
	{
		$this->user = $user;
	}

	/**
	 * @param int $max
	 * @return void
	 * 
	 * Limit address space of the program to size kilobytes. 
	 * If more processes are allowed, this applies to each of them separately. 
	 */
	public function setMemoryLimit(int $max)
	{
		$this->memoryLimit = $max;
	}

	/**
	 * @param int $max
	 * @return void
	 *
	 * Limit run time of the program to time seconds. Fractional numbers are allowed. 
	 * Time in which the OS assigns the processor to different tasks is not counted. 
	 */
	public function setMaxExecutionTime(int $max)
	{
		$this->maxExecutionTime = $max;
	}

	/**
	 * @param int $max
	 * @return void
	 *
	 * Limit wall-clock time to time seconds. 
	 * Fractional values are allowed. 
	 * This clock measures the time from the start of the program to its exit, 
	 * so it does not stop when the program has lost the CPU or when it is waiting for an external event. 
	 * We recommend to use --time as the main limit, 
	 * but set --wall-time to a much higher value as a precaution against sleeping programs. 
	 */
	public function setMaxWallTime(int $max)
	{
		$this->maxWallTime = $max;
	}

	/**
	 * @param int $max
	 * @return void
	 *
	 * When a time limit is exceeded,
	 * wait for extra time seconds before killing the program.
	 * This has the advantage that the real execution time is reported,
	 * even though it slightly exceeds the limit. Fractional numbers are again allowed.
	 */
	public function setExtraTime(int $max)
	{
		$this->extraTime = $max;
	}

	/**
	 * @param int $boxId
	 * @return void
	 *
	 * When you run multiple sandboxes in parallel, 
	 * you have to assign each unique IDs to them by this option. 
	 * See the discussion on UIDs in the INSTALLATION section. 
	 * The ID defaults to 0. 
	 */
	private function setBoxId(int $boxId)
	{
		$this->boxId = $boxId;
	}

	/**
	 * @param int $max
	 * @return void
	 *
	 * Permit the program to create up to max processes and/or threads. 
	 * Please keep in mind that time and memory limit do not work with multiple 
	 * processes unless you enable the control group mode. 
	 * If max is not given, an arbitrary number of processes can be run. 
	 * By default, only one process is permitted. 
	 */
	public function setMaxProcesses(int $max)
	{
		$this->maxProcesses = $max;
	}

	/**
	 * @return string
	 */
	public function getCmd(): string
	{
		return $this->cmd;
	}

	/**
	 * @param string $cmd
	 * @return void
	 */
	public function run(string $cmd)
	{
		$this->cmd = 
			"sudo -u ".$this->user." ".
			"/usr/local/bin/isolate ".
			$this->param("dir").
			$this->param("memoryLimit").
			$this->param("maxExecutionTime").
			$this->param("maxWallTime").
			$this->param("extraTime").
			$this->param("boxId").
			$this->param("maxProcesses").
			$this->param("stdout").
			$this->param("stderr").
			"--run -- ".$cmd.
			" 2>&1";

		// print "\n\n".$this->cmd."\n\n";

		shell_exec($this->cmd);
		$this->isExecuted = true;
	}

	/**
	 * @param string $r
	 * @return string
	 */
	private function param(string $r): string
	{
		$param = "";
		switch ($r) {
			case "dir":
				$param = "--dir=/home=".$this->homePath.":rw ";
				$param.= "--dir=/tmp=".$this->tmpPath.":rw ";
				$param.= "--dir=/etc=".$this->etcPath.":rw ";
				$param.= "--dir=/etc/alternatives=/etc/alternatives";
				break;
			case "memoryLimit":
				$param = isset($this->memoryLimit) ? "--mem=".$this->memoryLimit : "";
				break;
			case "maxExecutionTime":
				$param = isset($this->maxExecutionTime) ? "--time=".$this->maxExecutionTime : "";
				break;
			case "maxWallTime":
				$param = isset($this->maxWallTime) ? "--wall-time=".$this->maxWallTime : "";
				break;
			case "extraTime":
				$param = isset($this->extraTime) ? "--extra-time=".$this->extraTime : "";
				break;
			case "boxId":
				if (isset($this->boxId)) {

					if (! is_dir(
						$this->boxDir = $d = "/var/local/lib/isolate/".$this->boxId."/box"
					)) {
						shell_exec("/usr/local/bin/isolate --box-id=".$this->boxId." --init");
						shell_exec("sudo mkdir -p ".$d);
					}

					if (! is_dir($d = "/var/local/lib/isolate/".$this->boxId."/root")) {
						shell_exec("sudo mkdir -p ".$d);
					}

					if (! file_exists($d = $this->etcPath."/passwd")) {
						$uid = "6000".$this->boxId;
						file_put_contents($d, 
							"root:x:0:0:root:/root:/bin/bash\n".
							"daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin\n".
							"bin:x:2:2:bin:/bin:/usr/sbin/nologin\n".
							"u".$this->boxId.":x:{$uid}:{$uid}:u".$this->boxId.":/home/u".$this->boxId.":/bin/bash"
						);
					}

					$param = "--box-id=".$this->boxId;
				}
				break;
			case "stdout":
				$param = "--stdout=stdout";
				break;
			case "stderr":
				$param = "--stderr=stderr";
				break;
			case "maxProcesses":
				$param = isset($this->maxProcesses) ? "--processes=".$this->maxProcesses : "";
				break;
			default:
				break;
		}

		return $param === "" ? "" : $param." ";
	}

	/**
	 * @return string
	 */
	public function getStdout(): string
	{
		if (! $this->isExecuted) {
			throw new IsolatorException("The command is not executed yet!");
		}

		if (! file_exists($this->boxDir."/stdout")) {
			throw new IsolatorException("stdout file does not exists!");
		}

		return file_get_contents($this->boxDir."/stdout");
	}

	/**
	 * @return string
	 */
	public function getStderr(): string
	{
		if (! $this->isExecuted) {
			throw new IsolatorException("The command is not executed yet!");
		}

		if (! file_exists($this->boxDir."/stdout")) {
			throw new IsolatorException("stderr file does not exists!");
		}

		return file_get_contents($this->boxDir."/stderr");
	}
}
