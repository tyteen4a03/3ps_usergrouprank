<?php
/*
* Usergroup Ranks v1.5.5 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

/**
 * Cache rebuilder for libraries.
 *
 * @package XenForo_CacheRebuild
 */

class ThreePointStudio_UsergroupRanks_CacheRebuilder_UserRankAssociation extends XenForo_CacheRebuilder_Abstract {
	/**
	 * Gets rebuild message.
	 */
	public function getRebuildMessage() {
		return new XenForo_Phrase('3ps_usergroup_ranks_usergroup_ranks');
	}

	/**
	 * Shows the exit link.
	 */
	public function showExitLink() {
		return true;
	}

	/**
	 * Rebuilds the data.
	 *
	 * @see XenForo_CacheRebuilder_Abstract::rebuild()
	 */
	public function rebuild($position = 0, array &$options = array(), &$detailedMessage = '') {
		$options['batch'] = max(1, isset($options['batch']) ? $options['batch'] : 250);
		$options['existing_entries_only'] = isset($options['existing_entries_only']) ? $options['existing_entries_only'] : true;

		$db = XenForo_Application::getDb();
		/* @var $userModel XenForo_Model_User */
		$userModel = XenForo_Model::create('XenForo_Model_User');
		/* @var $ugrModel ThreePointStudio_UsergroupRanks_Model_UsergroupRanks */
		$ugrModel = XenForo_Model::create('ThreePointStudio_UsergroupRanks_Model_UsergroupRanks');
		$allUgrs = $ugrModel->getAllUserGroupRanks();
		if ($options["existing_entries_only"]) {
			// Special thanks to Xgc @ Freenode for this SQL - without him I will probably never figure out how to do this efficiently
			$userIds = $db->fetchCol($db->limit(
				"SELECT v1.userid FROM (
				  SELECT CAST(REPLACE(data_key, '3ps_ugr_ura_', '') AS unsigned int) AS userid
				  FROM xf_data_registry
				  WHERE data_key LIKE '3ps\_ugr\_ura\_%'
				) AS v1 WHERE v1.userid > ?",
				$options['batch']), $position);
		} else {
			$userIds = $userModel->getUserIdsInRange($position, $options['batch']);
		}
		if (sizeof($userIds) == 0) {
			return true;
		}
		foreach ($userIds AS $userId) {
			$position = $userId;
			// Get the user info
			$user = $userModel->getUserById($userId);
			$ugrModel->buildUserRankAssociation($allUgrs, $user, true);
		}
		$detailedMessage = XenForo_Locale::numberFormat($position);
		return $position;
	}
}