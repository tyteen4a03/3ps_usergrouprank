<?php
/*
* Usergroup Ranks v1.5.6 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Route_PrefixAdmin_UsergroupRanks implements XenForo_Route_Interface {
	/**
	 * Match a specific route for an already matched prefix.
	 *
	 * @see XenForo_Route_Interface::match()
	 */
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router) {
		$action = $router->resolveActionWithIntegerParam($routePath, $request, 'rid');
		return $router->getRouteMatch('ThreePointStudio_UsergroupRanks_ControllerAdmin_UsergroupRanks', $action, '3ps_usergroupranks');
	}

	/**
	 * Method to build a link to the specified page/action with the provided
	 * data and params.
	 *
	 * @see XenForo_Route_BuilderInterface
	 */
	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams) {
		return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'rid');
	}
}