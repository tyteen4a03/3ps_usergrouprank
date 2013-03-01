<?php

class ThreePointStudio_UsergroupRanks_Listener_Listener {
	public static function loadClassListener($class, &$extend) {
		switch ($class) {
			case 'XenForo_ControllerAdmin_UserGroup':
				$extend[] = 'ThreePointStudio_UsergroupRanks_ControllerAdmin_UserGroup';
				break;
		}
	}

	public static function loadClassModel($class, array &$extend) {
		return;
		switch ($class) {
			case 'XenForo_Model_Template':
				$extend[] = 'ThreePointStudio_UsergroupRanks_Model_Template';
				break;
		}
	}
}