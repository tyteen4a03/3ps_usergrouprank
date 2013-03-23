<?php

class ThreePointStudio_UsergroupRanks_Model_Import extends XFCP_ThreePointStudio_UsergroupRanks_Model_Import {
	/**
	* Imports a usergroup rank
	*
	* @param integer Source ID
	* @param array Data to import
	*
	* @return integer Imported usergroup rank ID
	*/
	public function importUsergroupRanks($oldId, array $info)
	{
		return $this->_importData($oldId, 'ThreePointStudio_UsergroupRanks_DataWriter_UsergroupRanks', 'usergroupRanks', 'rid', $info, false, false);
	}
}