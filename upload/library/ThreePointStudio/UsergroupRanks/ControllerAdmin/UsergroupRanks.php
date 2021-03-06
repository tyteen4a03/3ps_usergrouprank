<?php
/*
* Usergroup Ranks v1.6.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_ControllerAdmin_UsergroupRanks extends XenForo_ControllerAdmin_Abstract {
	/**
	 * Displays a list of usergroup ranks.
	 *
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionIndex() {
		$userGroupRanks = $this->_getUsergroupRanksModel()->getAllUserGroupRanks();
		foreach ($userGroupRanks as &$userGroupRank) {
			$userGroupRank['content_render'] = ThreePointStudio_UsergroupRanks_Helpers::helperRenderRank($userGroupRank, "span", true);
		}
		$viewParams = array(
			'userGroupRanks' => $userGroupRanks,
		);

		return $this->responseView('ThreePointStudio_UsergroupRanks_ViewAdmin_UsergroupRanks_List', '3ps_usergroup_ranks_list', $viewParams);
	}

	public function actionEdit() {
		$userGroupRankId = $this->_input->filterSingle('rid', XenForo_Input::UINT);
		$userGroupRank = $this->_getUsergroupRankOrError($userGroupRankId);
		$userGroupRank["rank_sprite_params"] = unserialize($userGroupRank["rank_sprite_params"]);
		$viewParams = array(
			'userGroupRank' => $userGroupRank,
			'userCriteria' => XenForo_Helper_Criteria::prepareCriteriaForSelection($userGroupRank['rank_user_criteria']),
			'userCriteriaData' => XenForo_Helper_Criteria::getDataForUserCriteriaSelection(),
			'showInactiveCriteria' => true
		);

		return $this->responseView('ThreePointStudio_UsergroupRanks_ViewAdmin_UsergroupRanks_Edit', '3ps_usergroup_ranks_edit', $viewParams);
	}

	public function actionAdd() {
		$viewParams = array(
			'userGroupRank' => array('rank_active' => 1),
			'userCriteria' => XenForo_Helper_Criteria::prepareCriteriaForSelection(''),
			'userCriteriaData' => XenForo_Helper_Criteria::getDataForUserCriteriaSelection(),
			'showInactiveCriteria' => true
		);
		return $this->responseView('ThreePointStudio_UsergroupRanks_ViewAdmin_UsergroupRanks_Edit', '3ps_usergroup_ranks_edit', $viewParams);
	}

	public function actionDelete() {
		$userGroupRankId = $this->_input->filterSingle('rid', XenForo_Input::UINT);
		if ($this->isConfirmedPost()) {
			$this->_assertPostOnly();
			$dw = XenForo_DataWriter::create('ThreePointStudio_UsergroupRanks_DataWriter_UsergroupRanks');
			$dw->setExistingData($this->_input->filterSingle('rid', XenForo_Input::STRING));
			$dw->delete();
			XenForo_Model::create("ThreePointStudio_UsergroupRanks_Model_UsergroupRanks")->rebuildRankDefinitionCache();
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('3ps-usergroup-ranks'), new XenForo_Phrase('deletion_successful'));
		} else {
			$userGroupRank = $this->_getUsergroupRankOrError($userGroupRankId);
			$viewParams = array(
				'userGroupRankId' => $userGroupRankId,
				'userGroupRankContent' => ThreePointStudio_UsergroupRanks_Helpers::helperRenderRank($userGroupRank, "span", true)
			);
			return $this->responseView('ThreePointStudio_UsergroupRanks_ViewAdmin_UsergroupRanks_Edit', '3ps_usergroup_ranks_delete', $viewParams);
		}
	}

	public function actionExport() {
		$rankIds = $this->_input->filterSingle('ranks_export', XenForo_Input::ARRAY_SIMPLE);
		$ranks = array();
		foreach ($rankIds as $rankId) {
			$rankId = int($rankId);
			$ranks[$rankId] = $this->_getUsergroupRankOrError($rankId);
		}

		$this->_routeMatch->setResponseType('xml');

		$viewParams = array(
			'ranks' => $ranks,
			'xml' => $this->_getUsergroupRanksModel()->getRanksExportXml($ranks)
		);

		return $this->responseView('XenForo_ViewAdmin_AddOn_Export', '', $viewParams);
	}

	/**
	* Inserts a new user group or updates an existing one.
	*
	* @return XenForo_ControllerResponse_Abstract
	*/
	public function actionSave() {
		$this->_assertPostOnly();

		$input = $this->_input->filter(array(
			'rank_type' => XenForo_Input::UINT,
			'rank_active' => XenForo_Input::BINARY,
			//'rank_description' => XenForo_Input::STRING,
			'rank_content' => XenForo_Input::STRING,
			//'rank_disabled_from_listing' => XenForo_Input::BINARY,
			'rank_user_criteria' => XenForo_Input::ARRAY_SIMPLE,
			'rank_styling_class' => XenForo_Input::STRING,
			'rank_sprite_params' => array(XenForo_Input::INT, array('array' => true)),
			'rid' => XenForo_Input::UINT
		));
		$ugrID = $this->_getUsergroupRanksModel()->insertOrUpdateUserGroupRank($input);
		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildAdminLink('3ps-usergroup-ranks') . $this->getLastHash($ugrID)
		);
	}

	/**
	* Gets the user group model.
	*
	* @return XenForo_Model_UserGroup
	*/
	protected function _getUsergroupRanksModel() {
		return $this->getModelFromCache('ThreePointStudio_UsergroupRanks_Model_UsergroupRanks');
	}

	/**
	 * Gets the specified user group or throws an error.
	 *
	 * @param int $userGroupRankId
	 * @throws XenForo_ControllerResponse_Exception
	 *
	 * @return array
	 */
	protected function _getUsergroupRankOrError($userGroupRankId) {
		$userGroup = $this->_getUsergroupRanksModel()->getUsergroupRankById($userGroupRankId);
		if (!$userGroup) {
			throw $this->responseException($this->responseError(new XenForo_Phrase('3ps_usergroup_ranks_requested_usergroup_rank_not_found'), 404));
		}

		return $userGroup;
	}
}