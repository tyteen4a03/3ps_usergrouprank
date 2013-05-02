<?php
/*
* Usergroup Ranks v1.6.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Listener_Controller {
	public static function loadClassController($class, array &$extend) {
		switch ($class) {
			case 'XenForo_ControllerPublic_Help':
				$extend[] = 'ThreePointStudio_UsergroupRanks_ControllerPublic_Help';
				break;
		}
	}
}