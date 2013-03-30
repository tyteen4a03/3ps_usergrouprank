<?php
/*
* Usergroup Ranks v1.5.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_Importer {
	public static function loadClassImporter($class, array &$extend) {
		switch ($class) {
			case 'XenForo_Importer_vBulletin36x':
			case 'XenForo_Importer_vBulletin':
			case 'XenForo_Importer_myvBulletin4':
				$extend[] = 'ThreePointStudio_UsergroupRanks_Importer_VBulletin';
				break;
			case 'XenForo_Importer_IPBoard32x':
			case 'XenForo_Importer_IPBoard':
				//$extend[] = 'ThreePointStudio_UsergroupRanks_Importer_IPBoard'; // Coming soon!
				break;
			case 'XenForo_Importer_PhpBb3':
				//$extend[] = 'ThreePointStudio_UsergroupRanks_Importer_PhpBb3'; // Coming soon!
				break
		}
	}
}