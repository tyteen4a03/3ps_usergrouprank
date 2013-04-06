<?php
/*
* Usergroup Ranks v1.5.5 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Installer {
	public static final function install($installedAddon) {
		$db = XenForo_Application::getDb();
		$version = is_array($installedAddon) ? $installedAddon['version_id'] : 0;
		if ($version == 0) {
			$db->query('CREATE TABLE IF NOT EXISTS `3ps_usergroup_ranks` (
					  `rid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					  `rank_type` mediumint(8) unsigned NOT NULL,
					  `rank_active` tinyint(1) unsigned NOT NULL,
					  `rank_content` text COLLATE utf8_unicode_ci NOT NULL,
					  `rank_user_criteria` MEDIUMBLOB NOT NULL,
					  `rank_styling_class` LONGTEXT NOT NULL,
					  UNIQUE KEY `rid` (`rid`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
		}
		if ($version > 0) { // Upgrade section only
			if ($version < 2) { // 1.0.0 -> 1.5.0
				$db->query('ALTER TABLE  `3ps_usergroup_ranks` CHANGE  `rank_type`  `rank_type` TINYINT UNSIGNED NOT NULL,
							DROP COLUMN `rank_usergroup`, DROP COLUMN `rank_display_condition`, DROP COLUMN `rank_style_priority_limit`,
							ADD COLUMN `rank_user_criteria` MEDIUMBLOB NOT NULL AFTER `rank_content`,
							ADD COLUMN `rank_styling_class` LONGTEXT NOT NULL AFTER `rank_user_criteria`;');
			}
		}
	}

	public static final function uninstall() {
		// Invalidate all caches
		XenForo_Model::create("ThreePointStudio_UsergroupRanks_Model_UsergroupRanks")->invalidateCache(0);
		$db = XenForo_Application::getDb();
		$db->query("DROP TABLE IF EXISTS `3ps_usergroup_ranks`");
	}
}