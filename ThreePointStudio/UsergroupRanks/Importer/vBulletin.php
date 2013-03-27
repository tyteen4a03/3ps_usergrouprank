<?php
/*
* Usergroup Ranks v1.5.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Importer_vBulletin extends XFCP_ThreePointStudio_UsergroupRanks_Importer_vBulletin {
	public function getSteps() {
		return array_merge(parent::getSteps(), array('usergroupRanks' => array('title' => 'Import Usergroup Ranks', 'depends' => array('userGroups'))));
	}

	public function stepUsergroupRanks($start, array $options) {
		$sDb = $this->_sourceDb;
		$prefix = $this->_prefix; 
		$model = $this->_importModel;
		$model->retainableKeys[] = 'rid';

		$ranks = $sDb->fetchAll('
			SELECT `rankid`, `minposts`, `rankimg`, `usergroupid`, `type`, `display`
			FROM ' . $prefix . 'ranks
			ORDER BY `rankid` ASC'
		);

		if (!$ranks) { // No rank to import
			return true;
		}
		$ugMap = $model->getImportContentMap('userGroup');

		XenForo_Db::beginTransaction();
		$total = isset($options['total']) ? $options['total'] : 0;
		foreach ($ranks as $rank) {
			$input = array(
				'rank_type' => $rank['type'],
				'rank_usergroup' => $this->_mapLookUp($ugMap, $rank['usergroupid']),
				'rank_active' => 1, // Always active
				'rank_content' => $rank["rankimg"],
				'rank_postreq' => ($rank['minposts'] > 0) ? 1 : 0, // If minposts has something, must be higher than
				'rank_postreq_amount' => $rank['minposts'],
				'rank_display_condition' => $rank['display'],
				'rank_style_priority_limit' => 0
			);
			$dw = XenForo_DataWriter::create('ThreePointStudio_UsergroupRanks_DataWriter_UsergroupRanks');
			$dw->setImportMode(true);
			$dw->bulkSet($input);
			$dw->save();

			$total++;
		}
		XenForo_Db::commit();

		$this->_session->incrementStepImportTotal($total);

		return true;
	}
}