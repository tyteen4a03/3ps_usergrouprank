<?php
/*
* Usergroup Ranks v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_DataWriter {
	public static function loadClassDataWriter($class, array &$extend) {
		switch ($class) {
			case 'XenForo_DataWriter_UserGroup':
				$extend[] = 'ThreePointStudio_UsergroupRanks_DataWriter_UserGroup';
				break;
		}
	}
}