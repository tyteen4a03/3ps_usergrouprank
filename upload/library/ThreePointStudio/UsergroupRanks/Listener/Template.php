<?php
/*
* Usergroup Ranks v1.6.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

/*
0=Over Avatar
1=Under Avatar
2=Above Username
3=Under UserTitle
*/

class ThreePointStudio_UsergroupRanks_Listener_Template {

	public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
		$options = XenForo_Application::get('options');
		$displayOptions = $options->get("3ps_usergroup_ranks_display_positions");
		$maxItems = $options->get("3ps_usergroup_ranks_max_items_per_row");
		switch ($hookName) {
			case "message_user_info_avatar":
			case "message_user_info_text":
				if (!$options->get('3ps_usergroup_ranks_system_active') or !$displayOptions["display"]["posts"]) { // Don't show usergroup ranks here
					return;
				}
				// Check if we want to run the hook at all
				if (($hookName == "message_user_info_avatar" and !in_array($displayOptions["display_position"]["posts"], array(0, 1)))
					or $hookName == "message_user_info_text" and !in_array($displayOptions["display_position"]["posts"], array(2, 3))) {
					return;
				}

				// Hack to reshuffle the array values
				$ugrList = array_values(XenForo_Model::create("ThreePointStudio_UsergroupRanks_Model_UsergroupRanks")->buildUserGroupRanksListForUser($hookParams["user"]));
				// Determine padding location
				switch ($displayOptions["display_position"]["posts"]) {
					case 0:
						$posPad = "Bottom";
						break;
					case 1:
					case 2:
					case 3:
						$posPad = "Top";
						break;
				}
				$rt_baseViewParams = array(
					'posPad' => $posPad,
				);
				$ugrHtml = "";
				if ($maxItems["style"]["posts"] == 0 and $maxItems["value"]["posts"] > 1) { // Fixed - need raw template
					// EXPERIMENTAL
					$rankTemplate = $template->create("3ps_usergroup_ranks_displaybit_raw", $template->getParams());
					$tempRankHtml = "";
					$i = 1;
					foreach ($ugrList as $ugr) {
						$tempRankHtml .= ThreePointStudio_UsergroupRanks_Helpers::helperRenderRank($ugr);
						if ($i % $maxItems["value"]["posts"] == 0) {
							// New row
							$rankTemplate->setParam("userGroupRanks", $tempRankHtml);
							$rankTemplate->setParams($rt_baseViewParams);
							$ugrHtml .= $rankTemplate->render();
							// Clear the previous data
							$tempRankHtml = "";
							$rankTemplate = $template->create("3ps_usergroup_ranks_displaybit_raw", $template->getParams());
						}
						if ($i > 1) {
							$postPad = "Mid";
						}
						$i++;
					}
					$rankTemplate->setParam("userGroupRanks", $tempRankHtml);
					$rankTemplate->setParams($rt_baseViewParams);
					$ugrHtml .= $rankTemplate->render();
				} else {
					$rankTemplate = $template->create("3ps_usergroup_ranks_displaybit", $template->getParams());
					$rankTemplate->setParam("userGroupRanks", $ugrList);
					if ($maxItems["style"]["posts"] == 1) {
						$rankTemplate->setParam("extraStyleProp", "width: " . $maxItems["value"]["posts"] . "px; overflow: visible"); // Dynamic
						$rankTemplate->setParam("extraCSSClasses", "paddedLi"); // Dynamic
					}
					$rankTemplate->setParams($rt_baseViewParams);
					$ugrHtml = $rankTemplate->render();
				}
				$renderHTML = $ugrHtml; // May merge these 2 variables into 1 if we don't need to fiddle with them later on
				switch ($displayOptions["display_position"]["posts"]) {
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
				break;
			case 'help_sidebar_links':
				$linksTemplate = $template->create('3ps_usergroup_ranks_help_sidebar_links', $template->getParams())->render();
				$contents = $contents . $linksTemplate;
				break;
			case "footer":
				$creditNotice = ($options->get("3ps_usergroup_ranks_display_credit_notice")) ? new XenForo_Phrase("3ps_usergroup_ranks_credit_notice") : '';
				$copyrightText = new XenForo_Phrase("xenforo_copyright");
				$search = '<div id="copyright">' . $copyrightText;
				$replace = '<div id="copyright">' . $copyrightText . '<br /><div id="3ps_usergroup_ranks_credit_notice">' . $creditNotice . '<!-- This forum uses [3.studIo] Usergroup Ranks, licensed under the BSD 2-Clause Modified License. DO NOT REMOVE THIS NOTICE! --></div>';
				$contents = str_replace($search, $replace, $contents);
				break;
			case "page_container_head":
				// Grab the cached CSS
				$cssText = XenForo_Model::create("XenForo_Model_DataRegistry")->get("3ps_ugr_spriteCSS");
				// Ouput it
				$contents = str_replace("<!--XenForo_Require:CSS-->", "<!--XenForo_Require:CSS-->" . PHP_EOL . '<style type="text/css">' . $cssText . '</style>', $contents);
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