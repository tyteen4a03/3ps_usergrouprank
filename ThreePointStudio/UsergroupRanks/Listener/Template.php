<?php
/*
* Usergroup Ranks v1.5.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_Template {

	public static function processRanks(&$userGroupRanks, &$hookParams) {
		foreach ($userGroupRanks as $key => &$userGroupRank) {
			// Is this rank even active?
			if (!$userGroupRank['rank_active']) {
				unset($userGroupRanks[$key]);
				continue;
			}
			// To keep or not to keep.
			$match = XenForo_Helper_Criteria::userMatchesCriteria($userGroupRank['rank_user_criteria'], $hookParams['user']);
			if (!$match) {
				unset($userGroupRanks[$key]);
				continue;
			}
		}
	}

	public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
		if ($hookName == "message_user_info_avatar" or $hookName == "message_user_info_text") {
			$options = XenForo_Application::get('options');
			$cacheLevel = $options->get('3ps_usergroup_ranks_caching_level');
			if (!$options->get('3ps_usergroup_ranks_system_active')) { // Don't show usergroup ranks
				return;
			}
			$dr = XenForo_Model::create("XenForo_Model_DataRegistry");
			$ugrModel = XenForo_Model::create('ThreePointStudio_UsergroupRanks_Model_UsergroupRanks');
			// Try getting rank definition from cache
			$userGroupRanks = ($cacheLevel > 0) ? $dr->get('3ps_ugr_rankDef') : null;
			if ($userGroupRanks == null) {
				// Get them from the database
				$userGroupRanks = $ugrModel->getAllUserGroupRanks();
				// Store them back into the cache
				if ($cacheLevel > 0) $dr->set('3ps_ugr_rankDef', $userGroupRanks);
			}
			if ($cacheLevel == 2) { // User/Rank association is cached
				// Get the association
				$urAssoc = $dr->get("3ps_ugr_ura_" . $hookParams["user"]["user_id"]);
				if ($urAssoc == null) {
					// Doesn't exist in database, calculate and store them back into the database
					self::processRanks($userGroupRanks, $hookParams);
					$dr->set(("3ps_ugr_ura_" . $hookParams["user"]["user_id"]), implode(",", array_keys($userGroupRanks)));
				} else {
					$newRankDef = array();
					foreach (explode(",", $urAssoc) as $ugrId) {
						$newRankDef[$ugrId] = $userGroupRanks[$ugrId];
					}
					$userGroupRanks = $newRankDef;
				}
			} else {
				self::processRanks($userGroupRanks, $hookParams);
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
		} elseif ($hookName == "footer") {
			$copyrightText = new XenForo_Phrase("xenforo_copyright");
			$search = '<div id="copyright">' . $copyrightText;
			$replace = '<div id="copyright" style="text-align: left;">' . $copyrightText . '<br />' . new XenForo_Phrase("3ps_usergroup_ranks_credit_notice");
			$contents = str_replace($search, $replace, $contents);
		}
	}

	public static function templateCreate($templateName, array &$params, XenForo_Template_Abstract $template) {
		if ($templateName == 'message_user_info') {
			$template->preloadTemplate('3ps_usergroup_ranks_displaybit');
		}
	}
}