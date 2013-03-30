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
	public function getUserGroupRankById($userGroupId) {
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
		return $dw->get('rid');
	}

	/**
	 * Invalidates all caches. Used for upgrades only.
	 * @param $option Invalidation option.
	 */
	public function invalidateCache($option) {
		// 0 = All cache. 1 = Display Style Priority Cache, 2 = Usergroup Ranks definition, 3 = User/Rank association
		$dr = XenForo_Model::create("XenForo_Model_DataRegistry");
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
}