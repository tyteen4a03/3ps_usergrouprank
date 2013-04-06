<?php
/*
* Usergroup Ranks v1.5.5 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

/*
* Note on some values:
* rank_type: Defines how the addon should treat the content in rank_content
* 0 - image (default), 1 - text
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

	protected function _postSave() {
		if (XenForo_Application::get("options")->get("3ps_usergroup_ranks_caching_level") > 0) {
			$this->_getUsergroupRanksModel()->rebuildRankDefinitionCache();
		}
	}


}