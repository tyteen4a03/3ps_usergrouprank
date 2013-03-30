<?php
/*
* Usergroup Ranks v1.5.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_DataWriter_UserGroup extends XFCP_ThreePointStudio_UsergroupRanks_DataWriter_UserGroup {
	protected function _postSave() {
		parent::_postSave();
		$this->_getUsergroupRanksModelFromCache()->rebuildDisplayStylePriorityCache();
	}

	protected function _postDelete() {
		parent::_postDelete();
		$this->_getUsergroupRanksModelFromCache()->rebuildDisplayStylePriorityCache();
	}

	public function _getUsergroupRanksModelFromCache() {
		return $this->getModelFromCache("ThreePointStudio_UsergroupRanks_Model_UsergroupRanks");
	}
}