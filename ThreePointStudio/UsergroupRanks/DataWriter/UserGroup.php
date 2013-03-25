<?php
/*
* Usergroup Ranks v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_DataWriter_UserGroup extends XFCP_ThreePointStudio_UsergroupRanks_DataWriter_UserGroup {
	protected function _postSave() {
		parent::_postSave();
		self::rebuildDisplayStylePriorityCache();
	}

	protected function _postDelete() {
		parent::_postDelete();
		self::rebuildDisplayStylePriorityCache();
	}

	public function rebuildDisplayStylePriorityCache() {
		XenForo_Model::create('XenForo_Model_DataRegistry')->set('3ps_dspCache', XenForo_Application::getDb()->fetchPairs('SELECT user_group_id, display_style_priority FROM xf_user_group ORDER BY user_group_id ASC'));
	}
}