<?php
/*
* Usergroup Ranks v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_Criteria {
	public static function criteriaUser($rule, array $data, array $user, &$returnValue) {
		// Get our DSPCache
		$dspCache = XenForo_Model::create('XenForo_Model_DataRegistry')->get('3ps_dspCache');
		// User usergroups
		$secondaryUgs = explode(",", $user["secondary_group_ids"]);
		$primaryUg = $user['user_group_id'];
		$allUgs = array_merge((array)$primaryUg, $secondaryUgs);
		// Usergroup Rank usergroups
		$rank_allUgs = explode(",", $user['ugrInfo']['rank_usergroup']);
		switch ($rule) {
			// Is/Is Not primary usergroup
			case '3ps_usergroup_ranks_is_primary_ug':
			case '3ps_usergroup_ranks_is_not_primary_ug':
				$match = in_array($primaryUg, $rank_allUgs);
				$returnValue = ($rule == "3ps_usergroup_ranks_is_primary_ug") ? $match : !$match;
				break;
			// Is/Is not in secondary usergroup - any
			case '3ps_usergroup_ranks_is_in_secondary_ug_any':
			case '3ps_usergroup_ranks_is_not_in_secondary_ug_any':
				$match = false;
				foreach ($secondaryUgs as $ug) {
					if (in_array($ug, $rank_allUgs)) {
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
				$secUgsCount = count($secondaryUgs);
				foreach ($secondaryUgs as $ug) {
					if (in_array($ug, $rank_allUgs)) {
						$matchList++;
					}
				}
				$match = ($matchList == $secUgsCount);
				$returnValue = ($rule == "3ps_usergroup_ranks_is_in_secondary_ug_all") ? $match : !$match;
				break;
			// Display Style Priority - All Usergroups
			case '3ps_usergroup_ranks_allugs_dsp_lower':
				foreach ($allUgs as $ug) {
					if ($dspCache[$ug] > $data['3ps_usergroup_ranks_allugs_dsp_lower']) { // Bigger? Reject
						$returnValue = false;
						return;
					}
				}
				break;
			case '3ps_usergroup_ranks_allugs_dsp_higher':
				foreach ($allUgs as $ug) {
					if ($dspCache[$ug] < $data['3ps_usergroup_ranks_allugs_dsp_higher']) { // Smaller? Reject
						$returnValue = false;
						return;
					}
				}
				break;
			// Display Style Priority - Primary Usergroups only
			case '3ps_usergroup_ranks_priug_dsp_lower':
				if ($dspCache[$primaryUg] > $data['3ps_usergroup_ranks_priug_dsp_lower']) { // Bigger? Reject
					$returnValue = false;
					return;
				}
				break;
			case '3ps_usergroup_ranks_priug_dsp_higher':
				if ($dspCache[$primaryUg] < $data['3ps_usergroup_ranks_priug_dsp_higher']) { // Smaller? Reject
					$returnValue = false;
					return;
				}
				break;
			// Display Style Priority - Secondary Usergroups only
			case '3ps_usergroup_ranks_secugs_dsp_lower':
				foreach ($secondaryUgs as $ug) {
					if ($dspCache[$ug] > $data['3ps_usergroup_ranks_secugs_dsp_lower']) { // Bigger? Reject
						$returnValue = false;
						return;
					}
				}
				break;
			case '3ps_usergroup_ranks_secugs_dsp_higher':
				foreach ($secondaryUgs as $ug) {
					if ($dspCache[$ug] < $data['3ps_usergroup_ranks_secugs_dsp_higher']) { // Smaller? Reject
						$returnValue = false;
						return;
					}
				}
				break;
			// Display Style Priority - Any usergroup
			case '3ps_usergroup_ranks_anyugs_dsp_lower':
				foreach ($secondaryUgs as $ug) {
					if ($dspCache[$ug] < $data['3ps_usergroup_ranks_anyugs_dsp_lower']) { // Smaller? Good!
						$returnValue = true;
						return;
					}
				}
				$returnValue = false;
				break;
			case '3ps_usergroup_ranks_anyugs_dsp_higher':
				foreach ($secondaryUgs as $ug) {
					if ($dspCache[$ug] > $data['3ps_usergroup_ranks_anyugs_dsp_higher']) { // Bigger? Good!
						$returnValue = true;
						return;
					}
				}
				$returnValue = false;
				break;
		}
	}
}