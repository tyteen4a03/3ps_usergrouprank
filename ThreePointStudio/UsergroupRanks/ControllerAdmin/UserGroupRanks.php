<?php

class ThreePointStudio_UsergroupRanks_ControllerAdmin_UserGroupRanks extends XenForo_ControllerAdmin_Abstract {
	/**
	 * Displays a list of usergroup ranks.
	 *
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionIndex() {
		$viewParams = array(
			'userGroupRanks' => $this->_getCustomUserGroupRankModel()->getAllUserGroupRanks()
		);

		return $this->responseView('ThreePointStudio_UsergroupRank_ViewAdmin_UsergroupRank_List', '3ps_usergroupranks_list', $viewParams);
	}

	/**
	* Displays a form to add a user group.
	*
	* @return XenForo_ControllerResponse_Abstract
	*/
	public function actionEdit() {
		$userGroupRankId = $this->_input->filterSingle('ug_rid', XenForo_Input::UINT);
		$userGroupRank = $this->_getCustomUserGroupRankModelOrError($userGroupRankId);

		$viewParams = array(
			'userGroupRankInfo' => $userGroupRank,
		);

		return $this->responseView('ThreePointStudio_UsergroupRank_ViewAdmin_UsergroupRank_Edit', '3ps_usergroupranks_edit', $viewParams);
	}

	public function actionAdd() {
		$viewParams = array();
		return $this->responseView('ThreePointStudio_UsergroupRank_ViewAdmin_UsergroupRank_Add', '3ps_usergroupranks_add', $viewParams);
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
				'rank_usergroup' => XenForo_Input::UINT,
				'rank_active' => XenForo_Input::BINARY,
				'rank_content' => XenForo_Input::STRING,
				'rank_display_condition' => XenForo_Input::UINT,
				'rank_styling_priority_limit' => XenForo_Input::UINT,
		));

		$userGroupRankId = $input['rid'];

		$dw = XenForo_DataWriter::create('ThreePointStudio_UsergroupRanks_DataWriter_UserGroupRanks');
		$dw->setExistingData($userGroupRankId);
		$dw->set('rank_usergroup', $input['rank_usergroup']);
		$dw->set('rank_active', $input['rank_active']);
		$dw->set('rank_content', $input['rank_content']);
		$dw->set('rank_display_condition', $input['rank_display_condition']);
		$dw->set('rank_styling_priority_limit', $input['rank_styling_priority_limit']);
		$dw->save();

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildAdminLink('usergroup-ranks') . $this->getLastHash($input['rid'])
		);
	}


	/**
	 * Selectively enables or disables specified add-ons
	 *
	 * @return XenForo_ControllerResponse_Abstract
	 */
	/*public function actionToggle() {
		return $this->_getToggleResponse(
					$this->_getUserGroupModel()->getAllUserGroups(),
					'XenForo_DataWriter_UserGroup',
					'joinable-user-groups',
					'joinable');
	}*/

	/**
	* Gets the user group model.
	*
	* @return XenForo_Model_UserGroup
	*/
	protected function _getCustomUserGroupRankModel()
	{
		return $this->getModelFromCache('ThreePointStudio_UserGroupRanks_Model_UserGroupRanks');
	}

	/**
	* Gets the specified user group or throws an error.
	*
	* @param integer $usergroupRankId
	*
	* @return array
	*/
	protected function _getCustomUserGroupRankModelOrError($userGroupRankId) {
		$userGroup = $this->getModelFromCache()->getUserGroupRankById($userGroupRankId);
		if (!$userGroup) {
			throw $this->responseException($this->responseError(new XenForo_Phrase('requested_usergroup_rank_not_found'), 404));
		}

		return $userGroup;
	}

}