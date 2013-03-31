<?php
/*
* Usergroup Ranks v1.5.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_Init {
	public static function initDependencies(XenForo_Dependencies_Abstract $dependencies, array $data) {
		XenForo_CacheRebuilder_Abstract::$builders += array('usergroupRanks' => 'ThreePointStudio_UsergroupRanks_CacheRebuilder_UserRankAssociation');
		$cachingOption = XenForo_Application::get("options")->get("3ps_usergroup_ranks_caching_level");
		if ($cachingOption > 0) {
			$model = XenForo_Model::create('XenForo_Model_DataRegistry');
			// Check if we already have DSP cached
			if (!$model->get("3ps_ugr_dspCache")) {
				// Caching time!
				XenForo_Model::create("ThreePointStudio_UsergroupRanks_Model_UsergroupRanks")->rebuildDisplayStylePriorityCache();
			}
			// Check if we already have rank definitions cached
			if (!$model->get("3ps_ugr_rankDef")) {
				// Caching time!
				$model->set("3ps_ugr_rankDef", XenForo_Model::create("ThreePointStudio_UsergroupRanks_Model_UsergroupRanks")->getAllUserGroupRanks());
			}
		}
	}
}