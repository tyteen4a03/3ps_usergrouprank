<?php

class ThreePointStudio_UsergroupRanks_ControllerAdmin_UserGroup extends XenForo_ControllerAdmin_Abstract {

	public function action3psUsergroupRanks() {
		$viewParams = array(
			'userGroupRanks' => $this->getModelFromCache('ThreePointStudio_UserGroupRanks_Model_UserGroupRanks')->getAllUserGroupRanks()
		);

		return $this->responseView('ThreePointStudio_UsergroupRank_ViewAdmin_UsergroupRank_List', '3ps_usergroupranks_list', $viewParams);
	}
}