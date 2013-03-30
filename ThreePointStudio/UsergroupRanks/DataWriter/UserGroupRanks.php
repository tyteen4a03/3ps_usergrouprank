<?php
/*
* Usergroup Ranks v1.5.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

/*
* Note on some values:
* rank_type: Defines how the addon should treat the content in rank_content
* 0 - image (default), 1 - text
*
* rank_postreq: Defines post amount conditions (temporary for 1.1.0, will be replaced by something more solid in 1.2)
* 0 - Ignore this criteria, 1 - Higher than, 2 - Lower than, 3 - Equal
*
* rank_display_condition: Defines when the usergroup rank should be displayed
* 0 - at all times (default)
* 1 - if usergroup style priority is the highest
* 2 - if usergroup is primary usergroup
* 3 - if usergroup is primary usergroup or usergroup style priority is the highest
* 4 - if usergroup is primary usergroup and usergroup style priority is the highest
* 5 - if usergroup style priority is higher than a defined value (not implemented yet)
* 6 - if usergroup style priority is lower than a defined value (not implemented yet)
*/

class ThreePointStudio_UsergroupRanks_DataWriter_UsergroupRanks extends XenForo_DataWriter {

	var $fields = array(
		"3ps_usergroup_ranks" => array(
			"rid" => array(
				'type' => self::TYPE_UINT,
				'autoIncrement' => true
			),
			"rank_type" => array(
				'type' => self::TYPE_UINT,
				'default' => 0
			),
			"rank_active" => array(
				'type' => self::TYPE_BOOLEAN,
				'default' => 1
			),
			"rank_content" => array(
				'type' => self::TYPE_STRING,
				'default' => ''
			),
			'rank_user_criteria' => array(
				'type' => self::TYPE_UNKNOWN,
				'required' => true,
				'verification' => array('$this', '_verifyCriteria')
			),
			'rank_styling_class' => array(
				'type' => self::TYPE_STRING,
				'default' => ''
			)
		)
	);

	/**
	* Gets the fields that are defined for the table. See parent for explanation.
	*
	* @return array
	*/
	protected function _getFields() {
		return $this->fields;
	}

	protected function _getExistingData($data) {
		if (!$id = $this->_getExistingPrimaryKey($data, 'rid')) {
			return false;
		}
		return array('3ps_usergroup_ranks' => $this->_getUsergroupRanksModel()->getUsergroupRankById($id));
	}

	protected function _getUpdateCondition($tableName) {
		return 'rid = ' . $this->_db->quote($this->getExisting('rid'));
	}

	protected function _getUsergroupRanksModel() {
		return $this->getModelFromCache('ThreePointStudio_UsergroupRanks_Model_UsergroupRanks');
	}

	protected function _verifyCriteria(&$criteria) {
		$criteriaFiltered = XenForo_Helper_Criteria::prepareCriteriaForSave($criteria);
		$criteria = serialize($criteriaFiltered);
		return true;
	}

	public function _postSave() {
		$this->_getUsergroupRanksModel()->rebuildRankDefinitionCache();
	}
}