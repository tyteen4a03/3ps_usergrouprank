<?php
/*
* Usergroup Ranks v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

/*
* Note on some values:
* rank_type: Defines how the addon should treat the content in rank_content
* 0 - image (default), 1 - text (not implemented yet)
*
* rank_display_condition: Defines when the usergroup rank should be displayed
* 0 - at all times (default), 1 - if styling priority is the highest (not implemented yet),
* 2 - if styling priority is higher than a defined value (not implemented yet)
*/

class ThreePointStudio_UsergroupRanks_DataWriter_UserGroupRanks extends XFCP_ThreePointStudio_UsergroupRanks_DataWriter_UserGroupRanks {
	/**
	* Gets the fields that are defined for the table. See parent for explanation.
	*
	* @return array
	*/
	protected function _getFields() {
		$fields = parent::_getFields();
		$fields['3ps_usergroup_ranks']['rid'] = array('type' => self::TYPE_UINT);
		$fields['3ps_usergroup_ranks']['rank_type'] = array('type' => self::TYPE_UINT, 'default' => 0);
		$fields['3ps_usergroup_ranks']['rank_usergroup'] = array('type' => self::TYPE_UINT);
		$fields['3ps_usergroup_ranks']['rank_active'] = array('type' => self::TYPE_UINT, 'default' => 1);
		$fields['3ps_usergroup_ranks']['rank_content'] = array('type' => self::TYPE_STRING, 'default' => "");
		$fields['3ps_usergroup_ranks']['rank_display_condition'] = array('type' => self::TYPE_UINT, 'default' => 0);
		$fields['3ps_usergroup_ranks']['rank_styling_priority_limit'] = array('type' => self::TYPE_UINT, 'default' => 0);
		return $fields;
	}
}