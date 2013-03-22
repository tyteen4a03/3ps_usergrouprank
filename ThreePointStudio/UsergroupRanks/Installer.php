<?php
/*
* Usergroup Ranks v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Installer {
	var $dbQueries = array(
		'createQuery' => 'CREATE TABLE IF NOT EXISTS `3ps_usergroup_ranks` (
  `rid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rank_type` mediumint(8) unsigned NOT NULL,
  `rank_usergroup` text CHARACTER SET latin1 NOT NULL,
  `rank_active` tinyint(1) unsigned NOT NULL,
  `rank_content` text COLLATE utf8_unicode_ci NOT NULL,
  `rank_display_condition` tinyint(3) unsigned NOT NULL,
  `rank_style_priority_limit` int(10) unsigned NOT NULL,
  UNIQUE KEY `rid` (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;',
		'dropQuery' => 'DROP TABLE IF EXISTS `3ps_usergroup_ranks`',
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