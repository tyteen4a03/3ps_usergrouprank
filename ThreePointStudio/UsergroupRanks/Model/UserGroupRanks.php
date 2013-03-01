<?php

/**
 * Usergroup Rank Model.
 *
 * @package XenForo_UserGroups
 */
class ThreePointStudio_UserGroupRank_Model_UserGroupRank extends XenForo_Model {

	public function getUserGroupRanksByIds($userGroupIds) {
		if (!$userGroupIds) {
			return array();
		}

		return $this->fetchAllKeyed('SELECT * FROM 3ps_usergroup_ranks WHERE rank_usergroup IN (' . $this->_getDb()->quote($userGroupIds) . ') ORDER BY rid', 'rid');
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

	public function insertNewUserGroupRank($rankType, $userGroupId, $active, $content, $displayCondition, $stylingPriorityLimit) {
		$dw = XenForo_DataWriter::create('ThreePointStudio_UsergroupRank_DataWriter_UserGroupRanks');
		$dw->set('rank_type', $rankType);
		$dw->set('rank_usergroup', $userGroupId);
		$dw->set('rank_active', $active);
		$dw->set('rank_content', $content);
		$dw->set('rank_display_condition', $displayCondition);
		$dw->set('rank_styling_priority_limit', $stylingPriorityLimit);
		$dw->save();
	}
}