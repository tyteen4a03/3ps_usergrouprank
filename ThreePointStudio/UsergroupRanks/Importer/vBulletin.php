<?php
/*
* Usergroup Ranks v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Importer_vBulletin extends XFCP_ThreePointStudio_UsergroupRanks_Importer_vBulletin {
	public function getSteps() {
		return array_merge(parent::getSteps(), array('usergroupRanks' => array('title' => 'Import Usergroup Ranks', 'depends' => array('userGroups'))));
	}

	public function stepUsergroupRanks($start, array $options) {
		$options = array_merge(array(
				'limit' => 50,
				'max' => false
			), $options
		);

		$sDb = $this->_sourceDb;
		$prefix = $this->_prefix; 
		$model = $this->_importModel;
		$model->retainableKeys[] = 'rid';

		$options['max'] = $sDb->fetchOne('SELECT MAX(rankid) FROM ' . $prefix . 'ranks');

		$ranks = $sDb->fetchAll($sDb->limit('
				SELECT `rankid`, `minposts`, `rankimg`, `usergroupid`, `type`, `display`
				FROM ' . $prefix . 'usergroup
                WHERE `rankid` > ?
				ORDER BY `rankid` ASC
			', $options['limit']
			), $start
		);

		if (!$ranks) { // No rank to import
			return true;
		}
		$ugIds = array();
		foreach($ranks as $rank) {
			$ugIds[] = $rank['usergroupid'];
		}

		$ugMap = $model->getImportContentMap('userGroup', $ugIds);

		XenForo_Db::beginTransaction();
		foreach ($ranks as $rank) {
			$next = max($next, $rank['rankid']);
			$newUsergroupID = $this->_mapLookUp($ugMap, $rank['rankid']);
			if (empty($newUsergroupID)) {
				// Usergroup not found
				continue;
			}

			$dw = XenForo_DataWriter::create('ThreePointStudio_UsergroupRanks_DataWriter_UsergroupRanks');
			$dw->setImportMode(true);
			$dw->set();
		}
	}
}