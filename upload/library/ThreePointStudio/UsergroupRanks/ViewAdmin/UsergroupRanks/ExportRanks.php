<?php

class ThreePointStudio_UsergroupRanks_ViewAdmin_UsergroupRanks_ExportRanks extends XenForo_ViewAdmin_Base {
	/**
	 * Render the exported ranks to XML.
	 *
	 * @return string
	 */
	public function renderXml() {
		$this->setDownloadFileName('3ps-ugr-ranks-' . time() . '.xml');
		return $this->_params['xml']->saveXml();
	}
}