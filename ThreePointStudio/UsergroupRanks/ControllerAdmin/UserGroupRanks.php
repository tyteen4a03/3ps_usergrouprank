<?php
/*
* Usergroup Ranks v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

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

		return $this->responseView('ThreePointStudio_UsergroupRank_ViewAdmin_UsergroupRank_List', '3ps_usergroup_ranks_list', $viewParams);
	}

	/**
	* Displays a form to add a user group.
	*
	* @return XenForo_ControllerResponse_Abstract
	*/
	public function actionEdit() {
		$userGroupRankId = $this->_input->filterSingle('rid', XenForo_Input::UINT);
		$userGroupRank = $this->_getCustomUserGroupRankModelOrError($userGroupRankId);

		$viewParams = array(
			'userGroupRank' => $userGroupRank,
		);

		return $this->responseView('ThreePointStudio_UsergroupRank_ViewAdmin_UsergroupRank_Edit', '3ps_usergroup_ranks_edit', $viewParams);
	}

	public function actionAdd() {
		$viewParams = array();
		return $this->responseView('ThreePointStudio_UsergroupRank_ViewAdmin_UsergroupRank_Edit', '3ps_usergroup_ranks_edit', $viewParams);
	}

	public function actionDelete() {
		$viewParams = array(
			'userGroupRank' => $userGroupRank,
		);
		return $this->responseView('ThreePointStudio_UsergroupRank_ViewAdmin_UsergroupRank_Add', '3ps_usergroup_ranks_delete', $viewParams);
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
				'rank_usergroup' => XenForo_Input::STRING,
				'rank_active' => XenForo_Input::BINARY,
				'rank_content' => XenForo_Input::STRING,
				'rank_display_condition' => XenForo_Input::UINT,
				'rank_styling_priority_limit' => XenForo_Input::UINT,
				'rid' => XenForo_Input::UINT,
		));

		$userGroupRankId = $input['rid'];

		$this->getModelFromCache('ThreePointStudio_UserGroupRanks_Model_UserGroupRanks')->insertNewUserGroupRank($input);

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildAdminLink('3ps-usergroup-ranks') . $this->getLastHash($input['rid'])
		);
	}

	/**
	* Gets the user group model.
	*
	* @return XenForo_Model_UserGroup
	*/
	protected function _getCustomUserGroupRankModel() {
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
		$userGroup = $this->getModelFromCache('ThreePointStudio_UserGroupRanks_Model_UserGroupRanks')->getUserGroupRankById($userGroupRankId);
		if (!$userGroup) {
			throw $this->responseException($this->responseError(new XenForo_Phrase('requested_usergroup_rank_not_found'), 404));
		}

		return $userGroup;
	}
}