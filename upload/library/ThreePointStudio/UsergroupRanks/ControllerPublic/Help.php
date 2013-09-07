<?php
/*
* Usergroup Ranks v1.6.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_ControllerPublic_Help extends XFCP_ThreePointStudio_UsergroupRanks_ControllerPublic_Help {
	public function actionUsergroupRanks() {
		$options = XenForo_Application::get("options");
		if (!intval($options->get("3ps_usergroup_ranks_enable_ugr_listing"))) {
			throw $this->responseException($this->responseError(new XenForo_Phrase('requested_page_not_found'), 404));
		}
		$visitor = XenForo_Visitor::getInstance();
		if (!$visitor->hasPermission("3ps_ugr", "canViewUGRListing")) {
			throw $this->getNoPermissionResponseException();
		}
		$ugrModel = $this->getModelFromCache("ThreePointStudio_UsergroupRanks_Model_UsergroupRanks");
		$displayOption = intval($options->get("3ps_usergroup_ranks_ugr_listing_display_options"));
		if ($displayOption == 1) {
			$allUgrs = $ugrModel->buildUserGroupRanksListForUser($visitor->toArray());
		} else {
			$allUgrs = $ugrModel->getAllActiveUsergroupRanks();
		}
		$ugModel = $this->getModelFromCache("XenForo_Model_UserGroup");
		$ugTitles = $ugModel->getAllUserGroupTitles();
		foreach ($allUgrs as $key => &$ugr) {
			if ($ugr["rank_disabled_from_listing"]) { // Don't display this rank for listing
				unset($allUgrs[$key]);
				continue;
			}
			$relations = ThreePointStudio_UsergroupRanks_Model_UsergroupRanks::getUsergroupRankRelation($ugr);
			die(var_dump($relations));
			// Put usergroup titles in place
			$ugr["ugList"] = array();
			foreach ($relations as $value) {
				$ugr["ugList"][] = $ugTitles[$value];
			}
		}
		$viewParams = array(
			"userGroupRanks" => $allUgrs
		);
		return $this->_getWrapper('usergroupRanks', $this->responseView('XenForo_ViewPublic_Help_UsergroupRanks', '3ps_usergroup_ranks_help_usergroup_ranks_listing', $viewParams));
	}
}