<?php
/*
* Usergroup Ranks v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_Listener {
	public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
		if ($hookName == "message_user_info_avatar" or $hookName == "message_user_info_text" or $hookName == "") {
			$options = XenForo_Application::get('options');
			$ugrModel = XenForo_Model::create('ThreePointStudio_UserGroupRanks_Model_UserGroupRanks');
			$userGroupRanks = $ugrModel->getAllActiveUserGroupRanks();
			$newUGRList = array();
			foreach ($userGroupRanks as $key => &$userGroupRank) {
				$ugIds = explode(',', $userGroupRank['rank_usergroup']);
				var_dump($ugIds);
				// To keep or not to keep.
				switch ($userGroupRank["rank_display_condition"]) {
					case 1: // Drop this rank if we're not at highest priority
						if (!in_array($hookParams['user']['display_style_group_id'], $ugIds)) {
							unset($userGroupRanks[$key]);
						}
						break;
					case 2: // Drop this rank if rank is not primary group
						if (!in_array($hookParams['user']['user_group_id'], $ugIds)) {
							unset($userGroupRanks[$key]);
						}
						break;
					case 3: // Drop this rank if rank is not primary group and we're not at highest priority
						if (!in_array($hookParams['user']['display_style_group_id'], $ugIds) and !in_array($hookParams['user']['user_group_id'], $ugIds)) {
							unset($userGroupRanks[$key]);
						}
						break;
					case 4: // Drop this rank if rank is not primary group or we're not at highest priority
						if (!in_array($hookParams['user']['display_style_group_id'], $ugIds) or !in_array($hookParams['user']['user_group_id'], $ugIds)) {
							unset($userGroupRanks[$key]);
						}
						break;
					default: // Nothing to do
						break;
				}
				$userGroupRank["content_render"] = ($userGroupRank['rank_type'] === 0) ? '<img src="' . $userGroupRank["rank_content"] . '" />' : $userGroupRank["rank_content"];
			}
			$rankTemplate = $template->create("3ps_usergroup_ranks_displaybit", $template->getParams());
			switch ($options->get('3ps_usergroup_ranks_display_style')) {
				case 0:
					$posPad = "Bottom";
					break;
				case 1:
				case 2:
				case 3:
					$posPad = "Top";
					break;
			}
			$rt_viewParams = array(
				'userGroupRanks' => $userGroupRanks,
				'ugrList_posPad' => $posPad,
			);
			$rankTemplate->setParams($rt_viewParams);
			$renderHTML = $rankTemplate->render();
			switch ($options->get('3ps_usergroup_ranks_display_style')) {
				// Over Avatar
				case 0:
					$search = '<div class="avatarHolder">';
					$replace = '<div class="avatarHolder">' . $renderHTML;
					break;
				// Under Avatar
				case 1:
					$search = '<!-- slot: message_user_info_avatar -->';
					$replace = '<!-- slot: message_user_info_avatar -->' . $renderHTML;
					break;
				// Above Username
				case 2:
					$search = '<h3 class="userText">';
					$replace = $renderHTML . '<h3 class="userText">';
					break;
				// Below UserTitle
				case 3:
					$search = '<!-- slot: message_user_info_text -->';
					$replace = '<!-- slot: message_user_info_text -->' . $renderHTML;
					break;
			}
			$contents = str_replace($search, $replace, $contents);
		}
	}

	public static function template_create($templateName, array &$params, XenForo_Template_Abstract $template) {
		if ($templateName == 'message_user_info') {
			$template->preloadTemplate('3ps_usergroup_ranks_displaybit');
		}
	}
}