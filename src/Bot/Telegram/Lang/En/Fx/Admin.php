<?php

namespace Bot\Telegram\Lang\En\Fx;

final class Admin
{
	public static $map = [
		"banned_success" => ":admin banned :banned_user!",
		"kicked_success" => ":admin kicked :kicked_user!",
		"promote_success" => ":new_admin has been promoted to be an administrator by :promotor!",
		"need_reply" => "You need to reply a message to use this command!",
		"need_reply_or_mention" => "You need to reply a message or mention an user to use this command!",
		"sudo_only" => "You must to be a sudoer to promote user!",
		"command_not_allowed" => "You are not allowed to use this command!"
	];
}