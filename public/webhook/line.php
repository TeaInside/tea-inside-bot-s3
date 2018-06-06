<?php

require __DIR__."/../../config/line/log_dir";

shell_exec(
	"nohup ".
	"/usr/bin/php ".
	__DIR__."/../../connector/line.php \"".
	rawurlencode(file_get_contents("php://input"))."\" >> \"{$logDir}/bg.log\" 2>&1 &"
);
