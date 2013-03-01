<?php
// Usergroup Rank v1.0.0
// Made by tyteen4a03 at 3.studIo
// All rights reserved.

class ThreePointStudio_UsergroupRanks_Installer {
	var $dbQueries = array(
		'createQuery' => '',
		'dropQuery' => '',
	);

	protected static function install() {
		$db = XenForo_Application::get('db');
		$db->query(self::$dbQueries['createQuery']);
	}

	protected static function uninstall() {
		$db = XenForo_Application::get('db');
		$db->query(self::$dbQueries['dropQuery']);
	}
}

?>