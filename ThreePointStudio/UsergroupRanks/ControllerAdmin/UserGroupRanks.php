<?php
/*
* Usergroup Ranks v1.0.0 written by tyteen4a03@3.studIo.
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
		$userGroupRanks = $this->_getCustomUserGroupRankModel()->getAllUserGroupRanks();
		foreach ($userGroupRanks as &$userGroupRank) {
			$userGroupRank['content_render'] = ($userGroupRank['rank_type'] === 0) ? '<img src="' . $userGroupRank["rank_content"] . '" />' : $userGroupRank["rank_content"];
		}
		$viewParams = array(
			'userGroupRanks' => $userGroupRanks,
		);

		return $this->responseView('ThreePointStudio_UsergroupRanks_ViewAdmin_UsergroupRanks_List', '3ps_usergroup_ranks_list', $viewParams);
	}

	/**
	* Displays a form to add a user group.
	*
	* @return XenForo_ControllerResponse_Abstract
	*/
	public function actionEdit() {
		$userGroupRankId = $this->_input->filterSingle('rid', XenForo_Input::UINT);
		$userGroupRank = $this->_getCustomUserGroupRankOrError($userGroupRankId);
		$userGroupOptions = $this->_getCustomUserGroupRankModel()->getUserGroupOptions($userGroupRank["rank_usergroup"]);
		$viewParams = array(
			'userGroupRank' => $userGroupRank,
			'userGroupOptions' => $userGroupOptions,
		);

		return $this->responseView('ThreePointStudio_UsergroupRanks_ViewAdmin_UsergroupRanks_Edit', '3ps_usergroup_ranks_edit', $viewParams);
	}

	public function actionAdd() {
		$userGroupOptions = $this->_getCustomUserGroupRankModel()->getUserGroupOptions(0);
		$viewParams = array(
			'userGroupOptions' => $userGroupOptions,
			'userGroupRank' => array('rank_active' => 1),
		);
		return $this->responseView('ThreePointStudio_UsergroupRanks_ViewAdmin_UsergroupRanks_Edit', '3ps_usergroup_ranks_edit', $viewParams);
	}

	public function actionDelete() {
		$userGroupRankId = $this->_input->filterSingle('rid', XenForo_Input::UINT);
		if ($this->isConfirmedPost()) {
			return $this->_deleteData('ThreePointStudio_UsergroupRanks_DataWriter_UsergroupRanks', 'rid', XenForo_Link::buildAdminLink('3ps-usergroup-ranks'));
		} else {
			$userGroupRank = $this->_getCustomUserGroupRankOrError($userGroupRankId);
			$userGroupRankContent = ($userGroupRank['rank_type'] === 0) ? '<img src="' . $userGroupRank["rank_content"] . '" />' : $userGroupRank["rank_content"];
			$viewParams = array(
				'userGroupRankId' => $userGroupRankId,
				'userGroupRankContent' => $userGroupRankContent
			);
			return $this->responseView('ThreePointStudio_UsergroupRanks_ViewAdmin_UsergroupRanks_Edit', '3ps_usergroup_ranks_delete', $viewParams);
		}
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
			'rank_usergroup' => array(XenForo_Input::UINT, 'array' => true),
			'rank_active' => XenForo_Input::BINARY,
			'rank_content' => XenForo_Input::STRING,
			'rank_postreq' => XenForo_Input::UINT,
			'rank_postreq_amount' => XenForo_Input::UINT,
			'rank_display_condition' => XenForo_Input::UINT,
			'rank_style_priority_limit' => XenForo_Input::UINT,
			'rid' => XenForo_Input::UINT,
		));

		$ugrID = $this->getModelFromCache('ThreePointStudio_UsergroupRanks_Model_UsergroupRanks')->insertOrUpdateUserGroupRank($input);
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
	protected function _getCustomUserGroupRankModel() {
		return $this->getModelFromCache('ThreePointStudio_UsergroupRanks_Model_UsergroupRanks');
	}

	/**
	* Gets the specified user group or throws an error.
	*
	* @param integer $usergroupRankId
	*
	* @return array
	*/
	protected function _getCustomUserGroupRankOrError($userGroupRankId) {
		$userGroup = $this->_getCustomUserGroupRankModel()->getUserGroupRankById($userGroupRankId);
		if (!$userGroup) {
			throw $this->responseException($this->responseError(new XenForo_Phrase('3ps_usergroup_ranks_requested_usergroup_rank_not_found'), 404));
		}

		return $userGroup;
	}
}