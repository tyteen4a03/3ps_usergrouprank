<?php
/*
* Usergroup Ranks v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Installer {
	public static final function install($installedAddon) {
		$db = XenForo_Application::getDb();
		$version = is_array($installedAddon) ? $installedAddon['version_id'] : 0;
		if ($version < 1) {
			$db->query('CREATE TABLE IF NOT EXISTS `3ps_usergroup_ranks` (
					  `rid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					  `rank_type` mediumint(8) unsigned NOT NULL,
					  `rank_usergroup` text CHARACTER SET latin1 NOT NULL,
					  `rank_active` tinyint(1) unsigned NOT NULL,
					  `rank_content` text COLLATE utf8_unicode_ci NOT NULL,
					  `rank_display_condition` tinyint(3) unsigned NOT NULL,
					  `rank_style_priority_limit` int(10) unsigned NOT NULL,
					  UNIQUE KEY `rid` (`rid`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
		}
		if ($version < 2) {
			// TODO: Old code
			$db->query('ALTER TABLE `3ps_usergroup_ranks` ADD `rank_postreq` TINYINT UNSIGNED NOT NULL AFTER `rank_content`,
						ADD `rank_postreq_amount` INT UNSIGNED NOT NULL AFTER `rank_postreq`');
		}
	}

	public static final function uninstall() {
		$db = XenForo_Application::getDb();
		$db->query("DROP TABLE IF EXISTS `3ps_usergroup_ranks`");
	}
}

?>