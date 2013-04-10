<?php
/*
* Usergroup Ranks v1.5.6 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_Template {

	public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
		$options = XenForo_Application::get('options');
		if ($hookName == "message_user_info_avatar" or $hookName == "message_user_info_text") {
			if (!$options->get('3ps_usergroup_ranks_system_active')) { // Don't show usergroup ranks
				return;
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
				'userGroupRanks' => XenForo_Model::create("ThreePointStudio_UsergroupRanks_Model_UsergroupRanks")->buildUserGroupRanksListForUser($hookParams["user"]),
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
		} elseif ($hookName == "footer") {
			$creditNotice = ($options->get("3ps_usergroup_ranks_display_credit_notice")) ? new XenForo_Phrase("3ps_usergroup_ranks_credit_notice") : '';
			$copyrightText = new XenForo_Phrase("xenforo_copyright");
			$search = '<div id="copyright">' . $copyrightText;
			$replace = '<div id="copyright">' . $copyrightText . '<br />' . '<div id="3ps_usergroup_ranks_credit_notice">' . $creditNotice . '<!-- This forum uses [3.studIo] Usergroup Ranks, licensed under the BSD 2-Clause Modified License. DO NOT REMOVE THIS NOTICE! --></div>';
			$contents = str_replace($search, $replace, $contents);
		}
	}

	public static function templateCreate($templateName, array &$params, XenForo_Template_Abstract $template) {
		if ($templateName == 'message_user_info') {
			$template->preloadTemplate('3ps_usergroup_ranks_displaybit');
		}
	}

	public static function templatePostRender($templateName, &$content, array &$containerData, XenForo_Template_Abstract $template) {
		if ($templateName == 'tools_rebuild') {
			// Add our rebuild
			$content .= $template->create('3ps_usergroup_ranks_tools_rebuild', $template->getParams())->render();
		}
	}
}