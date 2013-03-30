<?php
/*
* Usergroup Ranks v1.5.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Model_UsergroupRanks extends XenForo_Model {
	/**
	* Gets all usergroup ranks.
	*
	* @return	array	Format: [user group id] => info
	*/
	public function getAllUserGroupRanks() {
		return $this->fetchAllKeyed('SELECT * FROM 3ps_usergroup_ranks ORDER BY rid', 'rid');
	}

	public function getAllActiveUserGroupRanks() {
		return $this->fetchAllKeyed('SELECT * FROM 3ps_usergroup_ranks WHERE rank_active = 1 ORDER BY rid', 'rid');
	}

	/**
	* Gets the named usergroup rank.
	*
	* @param array $userGroupId
	*
	* @return array Format: [user group id] => info
	*/
	public function getUsergroupRankById($userGroupId) {
		return $this->_getDb()->fetchRow('SELECT * FROM 3ps_usergroup_ranks WHERE rid = ?', $userGroupId);
	}

	/**
	* Inserts or Updates a usergroup rank.
	*
	* @param array $input 1D array with row content.
	*
	* @return int	Usergroup Rank ID
	*/
	public function insertOrUpdateUserGroupRank($input) {
		$dw = XenForo_DataWriter::create('ThreePointStudio_UsergroupRanks_DataWriter_UsergroupRanks');
		if ($input['rid'] and $input['rid'] > 0) {
			$dw->setExistingData($input['rid']);
		}
		$dw->bulkSet($input);
		$dw->save();
		$this->rebuildRankDefinitionCache();
		return $dw->get('rid');
	}

	/**
	 * Invalidates all caches. Used for upgrades only.
	 * @param $option Invalidation option.
	 */
	public function invalidateCache($option) {
		// 0 = All cache. 1 = Display Style Priority Cache, 2 = Usergroup Ranks definition, 3 = User/Rank association
		$dr = $this->_getDataRegistryModelFromCache();
		$db = XenForo_Application::getDb();
		switch ($option) {
			case 0: // Hack baby
			case 1:
				$dr->delete("3ps_ugr_dspCache");
				if ($option > 0) break;
			case 2:
				$dr->delete("3ps_ugr_rankDef");
				if ($option > 0) break;
			case 3:
				// Go around DataRegistry because we need LIKE
				$db->query("DELETE FROM xf_data_registry WHERE data_key LIKE '3ps_ugr_ura_%'");
				break;
		}
	}

	public function rebuildRankDefinitionCache() {
		XenForo_Model::create("XenForo_Model_DataRegistry")->set("3ps_ugr_rankDef", $this->getAllUserGroupRanks());
	}

	public function processRanks($ugrList, $user) {
		foreach ($ugrList as $key => $ugr) {
			// Is this rank even active?
			if (!$ugr['rank_active']) {
				unset($ugrList[$key]);
				continue;
			}
			// To keep or not to keep.
			$match = XenForo_Helper_Criteria::userMatchesCriteria($ugr['rank_user_criteria'], $user);
			if (!$match) {
				unset($ugrList[$key]);
				continue;
			}
		}
		return $ugrList;
	}

	public function getUsergroupRanksDefFromCache() {
		$dr = XenForo_Model::create("XenForo_Model_DataRegistry");
		$cacheLevel = XenForo_Application::get('options')->get('3ps_usergroup_ranks_caching_level');
		$userGroupRanks = ($cacheLevel > 0) ? $dr->get('3ps_ugr_rankDef') : null;
		if ($userGroupRanks == null) {
			// Get them from the database
			$userGroupRanks = $this->getAllUserGroupRanks();
			// Store them back into the cache
			if ($cacheLevel > 0) $dr->set('3ps_ugr_rankDef', $userGroupRanks);
		}
		return $userGroupRanks;
	}

	public function buildUserRankAssociation($ugrList, $user, $ignoreCache = false) {
		// Get the association
		$dr = XenForo_Model::create("XenForo_Model_DataRegistry");
		$urAssoc = (!$ignoreCache) ? $dr->get("3ps_ugr_ura_" . $user["user_id"]) : null;
		if ($urAssoc == null) {
			// Doesn't exist in database, calculate and store them back into the database
			$ugrList = $this->processRanks($ugrList, $user);
			$dr->set(("3ps_ugr_ura_" . $user["user_id"]), implode(",", array_keys($ugrList)));
		} else {
			$newRankDef = array();
			// Copy the ranks over
			foreach (explode(",", $urAssoc) as $ugrId) {
				$newRankDef[$ugrId] = $ugrList[$ugrId];
			}
			$ugrList = $newRankDef;
		}
		return $ugrList;
	}

	public function buildUserGroupRanksListForUser($user) {
		$options = XenForo_Application::get('options');
		$cacheLevel = $options->get('3ps_usergroup_ranks_caching_level');
		$userGroupRanks = $this->getUsergroupRanksDefFromCache();
		if ($cacheLevel == 2) { // User/Rank association is cached
			$userGroupRanks = $this->buildUserRankAssociation($userGroupRanks, $user);
		} else {
			$userGroupRanks = $this->processRanks($userGroupRanks, $user);
		}
		return $userGroupRanks;
	}
}