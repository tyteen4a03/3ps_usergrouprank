<?php
/*
* Usergroup Ranks v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_Listener {
	public static function loadClassListener($class, &$extend) {
		switch ($class) {
			case 'XenForo_ControllerAdmin_UserGroup':
				$extend[] = 'ThreePointStudio_UsergroupRanks_ControllerAdmin_UserGroup';
				break;
		}
	}

	public static function loadClassModel($class, array &$extend) {
		switch ($class) {
			case 'XenForo_Model_Template':
				$extend[] = 'ThreePointStudio_UsergroupRanks_Model_Template';
				break;
		}
	}

	public static function template_hook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
		$options = XenForo_Application::get('options');
		if ($hookName == 'member_view') {
			switch ($options->get('3ps_usergroup_ranks_display_style')) {
				case 0:
					break;
			}
		}
	}

	public static function template_create($templateName, array &$params, XenForo_Template_Abstract $template) {
		if ($templateName == 'member_view') {
			$template->preloadTemplate('3ps_usergroup_ranks_displaybit');
		}
	}
}