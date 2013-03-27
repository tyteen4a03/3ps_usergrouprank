<?php
/*
* Usergroup Ranks v1.5.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_Init {
	public static function initDependencies(XenForo_Dependencies_Abstract $dependencies, array $data) {
		$model = XenForo_Model::create('XenForo_Model_DataRegistry');
		// Check if we already have DSP cached
		if (!$model->get("3ps_dspCache")) {
			// Caching time!
			XenForo_DataWriter::create("XenForo_DataWriter_UserGroup")->rebuildDisplayStylePriorityCache();
		}
	}
}