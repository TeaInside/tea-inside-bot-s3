<?php

namespace Bot\Telegram\Api;

use PDO;
use Bot\Telegram\Api;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
class GetChatApi extends Api
{
	
	/**
	 * @param string $groupId
	 * @param int    $limit
	 * @return array
	 */
	public function getNewChat(string $groupId, int $limit = 25, int $offset = 0) {
		$limit = (int) $limit;
		$offset = (int) $offset;
		$st = $this->pdo->prepare(
			"SELECT 
				`b`.`text`,`b`.`file`,`b`.`msg_type`,`a`.`created_at`,`c`.`first_name`,
				`c`.`last_name`,`c`.`username`,`c`.`id`,`d`.`sha1_checksum`,`d`.`md5_checksum`,
				`d`.`info`,`d`.`filesize`,`d`.`type`
			FROM `group_messages` AS `a`
			INNER JOIN `group_messages_data` AS `b` ON `a`.`id`=`b`.`message_id`
			INNER JOIN `users` AS `c` ON `a`.`user_id` = `c`.`id`
			INNER JOIN `groups` AS `e` ON `a`.`group_id` = `e`.`id`
			LEFT JOIN `files` AS `d` ON `b`.`file` = `d`.`id`
			WHERE `e`.`username` LIKE :group_username OR `e`.`id`=:group_username
			ORDER BY `a`.`tmsg_id` DESC LIMIT {$limit} OFFSET {$offset};"
		);

		$st->execute(
			[
				":group_username" => $groupId
			]
		);

		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * @param string $groupId
	 * @return bool
	 */
	public function groupExists(string $groupId)
	{
		$st = $this->pdo->prepare(
			"SELECT `id` FROM `groups` WHERE `username` LIKE :g OR `id` = :g LIMIT 1;"
		);
		$st->execute([":g" => $groupId]);
		return (bool) ($st->fetch(PDO::FETCH_NUM));
	}
}
