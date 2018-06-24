<?php

require __DIR__."/../../config/telegram/log_dir";

$argv = rawurlencode(file_get_contents("php://input"));

shell_exec(
	"nohup ".
	"/usr/bin/php7.2 ".
	__DIR__."/../../connector/telegram.php \"".
	$argv."\" >> \"{$logDir}/bg.log\" 2>&1 &"
);


shell_exec(
	"nohup ".
	"/usr/bin/php7.2 ".
	__DIR__."/../../connector/telegram_logger.php \"".
	$argv."\" >> \"{$logDir}/bg.log\" 2>&1 &"
);

