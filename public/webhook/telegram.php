<?php

require __DIR__."/../../config/telegram/log_dir";

shell_exec(
	"nohup ".
	"/usr/bin/php ".
	__DIR__."/../../connector/telegram.php \"".
	rawurlencode(file_get_contents("php://input"))."\" >> \"{$logDir}/bg.log\" 2>&1 &"
);
