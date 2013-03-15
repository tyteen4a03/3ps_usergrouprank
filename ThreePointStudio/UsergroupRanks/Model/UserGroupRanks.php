<?php
/*
* Usergroup Ranks v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UserGroupRanks_Model_UserGroupRanks extends XenForo_Model {

	public function getUserGroupRanksByIds($userGroupIds) {
		if (!$userGroupIds) {
			return array();
		}

		return $this->fetchAllKeyed('SELECT * FROM 3ps_usergroup_ranks WHERE rank_usergroup IN (' . $this->_getDb()->quote($userGroupIds) . ') ORDER BY rid', 'rid');
	}

	/**
	* Gets all usergroup ranks.
	*
	* @return array Format: [user group id] => info
	*/
	public function getAllUserGroupRanks() {
		return $this->fetchAllKeyed('SELECT * FROM 3ps_usergroup_ranks ORDER BY rid', 'rid');
	}

	/**
	* Gets the named usergroup rank.
	*
	* @param array $userGroupId
	*
	* @return array Format: [user group id] => info
	*/
	public function getUserGroupRankById($userGroupId)
	{
		$usergroupRank = $this->getUserGroupRanksByIds($userGroupId);
		if (isset($usergroupRank[$userGroupId])) {
			return $usergroupRank[$userGroupId];
		} else {
			return;
		}
	}

	public function insertNewUserGroupRank($input) {
		$dw = XenForo_DataWriter::create('ThreePointStudio_UsergroupRanks_DataWriter_UserGroupRanks');
		$dw->setExistingData('rid', $input['rid']);
		$dw->set('rank_type', $input['rank_type']);
		$dw->set('rank_usergroup', $input['rank_usergroup']);
		$dw->set('rank_active', $input['active']);
		$dw->set('rank_content', $input['rank_content']);
		$dw->set('rank_display_condition', $input['rank_display_condition']);
		$dw->set('rank_styling_priority_limit', $input['rank_styling_priority_limit']);
		$dw->save();
	}
}