<?php
/*
* Usergroup Ranks v1.5.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_Criteria {
	public static function criteriaUser($rule, array $data, array $user, &$returnValue) {
		// Get our DSPCache
		$cacheLevel = XenForo_Application::get("options")->get("3ps_usergroup_ranks_caching_level");
		if ($cacheLevel > 0) {
			$dspCache = XenForo_Model::create('XenForo_Model_DataRegistry')->get('3ps_ugr_dspCache');
		} else {
			$dspCache = XenForo_Application::getDb()->fetchPairs('SELECT user_group_id, display_style_priority FROM xf_user_group ORDER BY user_group_id ASC');
		}
		// User usergroups
		$secondaryUgs = explode(",", $user["secondary_group_ids"]);
		$secUgsCount = count($secondaryUgs);
		$primaryUg = $user['user_group_id'];
		$displayUg = $user['display_style_group_id'];
		$allUgs = array_merge((array)$primaryUg, $secondaryUgs);
		switch ($rule) {
			// Is/Is Not primary usergroup
			case '3ps_usergroup_ranks_is_primary_ug':
			case '3ps_usergroup_ranks_is_not_primary_ug':
				$match = in_array($primaryUg, $data['user_group_ids']);
				$returnValue = ($rule == "3ps_usergroup_ranks_is_primary_ug") ? $match : !$match;
				break;
			// Is/Is Not display usergroup
			case '3ps_usergroup_ranks_is_display_ug':
			case '3ps_usergroup_ranks_is_not_display_ug':
				$match = in_array($displayUg, $data['user_group_ids']);
				$returnValue = ($rule == "3ps_usergroup_ranks_is_display_ug") ? $match : !$match;
				break;
			// Is/Is not in secondary usergroup - any
			case '3ps_usergroup_ranks_is_in_secondary_ug_any':
			case '3ps_usergroup_ranks_is_not_in_secondary_ug_any':
				$match = false;
				foreach ($secondaryUgs as $ug) {
					if (in_array($ug, $data['user_group_ids'])) {
						$match = true;
						break;
					}
				}
				$returnValue = ($rule == "3ps_usergroup_ranks_is_in_secondary_ug_any") ? $match : !$match;
				break;
			// Is/Is Not in secondary usergroup - all
			case '3ps_usergroup_ranks_is_in_secondary_ug_all':
			case '3ps_usergroup_ranks_is_not_in_secondary_ug_all':
				$matchList = 0;
				foreach ($secondaryUgs as $ug) {
					if (in_array($ug, $data['user_group_ids'])) {
						$matchList++;
					}
				}
				$match = ($matchList == $secUgsCount);
				$returnValue = ($rule == "3ps_usergroup_ranks_is_in_secondary_ug_all") ? $match : !$match;
				break;
			// Is/Is not in all usergroups
			case '3ps_usergroup_ranks_is_in_ugs_all':
			case '3ps_usergroup_ranks_is_not_in_ugs_all':
				$match = true;
				foreach ($secondaryUgs as $ug) {
					if (!in_array($ug, $data['user_group_ids'])) {
						$match = false;
						break;
					}
				}
				$returnValue = ($rule == "3ps_usergroup_ranks_is_in_secondary_ug_any") ? $match : !$match;
				break;
			// Display Group is Primary usergroup
			case '3ps_usergroup_ranks_display_ug_is_primary_ug':
				$returnValue = ($displayUg == $primaryUg);
				break;
			// Display Group in Secondary usergroup
			case '3ps_usergroup_ranks_display_ug_in_secondary_ugs':
				$returnValue = in_array($displayUg, $secondaryUgs);
				break;
			// Display Style Priority - All Usergroups
			case '3ps_usergroup_ranks_allugs_dsp_lower':
				foreach ($allUgs as $ug) {
					if ($dspCache[$ug] > intval($data['dsp'])) { // Bigger? Reject
						$returnValue = false;
						return;
					}
				}
				$returnValue = true;
				break;
			case '3ps_usergroup_ranks_allugs_dsp_higher':
				foreach ($allUgs as $ug) {
					if ($dspCache[$ug] < intval($data['dsp'])) { // Smaller? Reject
						$returnValue = false;
						return;
					}
				}
				$returnValue = true;
				break;
			// Display Style Priority - Primary Usergroup only
			case '3ps_usergroup_ranks_priug_dsp_lower':
				if ($dspCache[$primaryUg] > intval($data['dsp'])) { // Bigger? Reject
					$returnValue = false;
					return;
				}
				$returnValue = true;
				break;
			case '3ps_usergroup_ranks_priug_dsp_higher':
				if ($dspCache[$primaryUg] < intval($data['dsp'])) { // Smaller? Reject
					$returnValue = false;
					return;
				}
				$returnValue = true;
				break;
			// Display Style Priority - Display Usergroup only
			case '3ps_usergroup_ranks_displayug_dsp_lower':
				if ($dspCache[$displayUg] > intval($data['dsp'])) { // Bigger? Reject
					$returnValue = false;
					return;
				}
				$returnValue = true;
				break;
			case '3ps_usergroup_ranks_displayug_dsp_higher':
				if ($dspCache[$displayUg] < intval($data['dsp'])) { // Smaller? Reject
					$returnValue = false;
					return;
				}
				$returnValue = true;
				break;
			// Display Style Priority - Secondary Usergroups (Any)
			case '3ps_usergroup_ranks_secugs_dsp_lower_all':
				$matchList = 0;
				foreach ($secondaryUgs as $ug) {
					if ($dspCache[$ug] < intval($data['dsp'])) { // Smaller? Good!
						$matchList++;
					}
				}
				$returnValue = ($matchList == $secUgsCount);
				break;
			case '3ps_usergroup_ranks_secugs_dsp_higher_all':
				$matchList = 0;
				foreach ($secondaryUgs as $ug) {
					if ($dspCache[$ug] > intval($data['dsp'])) { // Bigger? Good!
						$matchList++;
					}
				}
				$returnValue = ($matchList == $secUgsCount);
				break;
			// Display Style Priority - Secondary Usergroups (All) 
			case '3ps_usergroup_ranks_secugs_dsp_lower_all':
				foreach ($secondaryUgs as $ug) {
					if ($dspCache[$ug] > intval($data['dsp'])) { // Bigger? Reject
						$returnValue = false;
						return;
					}
				}
				$returnValue = true;
				break;
			case '3ps_usergroup_ranks_secugs_dsp_higher_all':
				foreach ($secondaryUgs as $ug) {
					if ($dspCache[$ug] < intval($data['dsp'])) { // Smaller? Reject
						$returnValue = false;
						return;
					}
				}
				$returnValue = true;
				break;
			// Display Style Priority - Any usergroup
			case '3ps_usergroup_ranks_anyugs_dsp_lower':
				foreach ($secondaryUgs as $ug) {
					if ($dspCache[$ug] < intval($data['dsp'])) { // Smaller? Good!
						$returnValue = true;
						return;
					}
				}
				$returnValue = false;
				break;
			case '3ps_usergroup_ranks_anyugs_dsp_higher':
				foreach ($secondaryUgs as $ug) {
					if ($dspCache[$ug] > intval($data['dsp'])) { // Bigger? Good!
						$returnValue = true;
						return;
					}
				}
				$returnValue = false;
				break;
		}
	}
}