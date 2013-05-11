<?php
/*
* Usergroup Ranks v1.6.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_ControllerPublic_Help extends XFCP_ThreePointStudio_UsergroupRanks_ControllerPublic_Help {
	public function actionUsergroupRanks() {
		if (!XenForo_Application::get("options")->get("3ps_usergroup_ranks_enable_ugr_listing")) {
			throw $this->responseException($this->responseError(new XenForo_Phrase('requested_page_not_found'), 404));
		}
		$viewParams = array(
			"userGroupRanks" => $this->getModelFromCache("ThreePointStudio_UsergroupRanks_Model_UsergroupRanks")->getAllUsergroupRanks()
		);
		return $this->_getWrapper('usergroupRanks', $this->responseView('XenForo_ViewPublic_Help_UsergroupRanks', '3ps_usergroup_ranks_help_usergroup_ranks_listing', $viewParams));
	}
}