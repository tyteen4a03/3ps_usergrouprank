<?php
/*
* Usergroup Ranks v1.1.0 written by tyteen4a03@3.studIo.
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
	* @param	array	$userGroupId
	*
	* @return	array	Format: [user group id] => info
	*/
	public function getUserGroupRankById($userGroupId) {
		return $this->_getDb()->fetchRow('SELECT * FROM 3ps_usergroup_ranks WHERE rid = ?', $userGroupId);
	}

	/**
	* Inserts or Updates a usergroup rank.
	*
	* @param	array	$input	1D array with row content.
	*
	* @return	int	Usergroup Rank ID
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
	 * Gets the list of possible extra user groups in "option" format.
	 *
	 * @param	string|array	$groupIds List of existing extra group IDs; may be serialized.
	 *
	 * @return	array List of	user group options (keys: label, value, selected)
	 */
	public function getUserGroupOptions($groupIds) {
		return $this->getModelFromCache('XenForo_Model_UserGroup')->getUserGroupOptions($groupIds);
	}
}