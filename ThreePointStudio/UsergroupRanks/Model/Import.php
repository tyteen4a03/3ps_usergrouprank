<?php
/*
* Usergroup Ranks v1.5.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Model_Import extends XFCP_ThreePointStudio_UsergroupRanks_Model_Import {
	/**
	* Imports a usergroup rank
	*
	* @param integer $oldId Source ID
	* @param array $info Data to import
	*
	* @return integer Imported usergroup rank ID
	*/
	public function importUsergroupRanks($oldId, array $info) {
		return $this->_importData($oldId, 'ThreePointStudio_UsergroupRanks_DataWriter_UsergroupRanks', 'usergroupRanks', 'rid', $info, false, false);
	}
}