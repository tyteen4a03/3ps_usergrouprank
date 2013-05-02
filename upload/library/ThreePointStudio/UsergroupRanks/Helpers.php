<?php
/*
* Usergroup Ranks v1.6.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Helpers {
	/**
	 * Array to cache model objects
	 *
	 * @var array
	 */
	protected static $_modelCache = array();

	/**
	 * @param $rank array The usergroup rank array.
	 * @param $returnTag str The tag name to return as. Defaults to li
	 * @param $ignoreCache bool Whether to reference from DataRegistry or not. Default to false
	 * @return string The rendered HTML.
	 */
	public static function helperRenderRank($rank, $returnTag = "li", $ignoreCache = false) {
		$dr = self::_getModelFromCache("XenForo_Model_DataRegistry");
		$cachingOption = XenForo_Application::getConfig()->get("3ps_usergroup_ranks_caching_level");
		$class = $style = $content = '';
		switch ($rank["rank_type"]) {
			case 0:
				// Image
				$content = '<img src="' . $rank["rank_content"] . '" />';
				break;
			case 1:
				// Text
				$content = $rank["rank_content"];
				break;
			case 2:
				// CSS Sprite
				$spriteParams = unserialize($rank["rank_sprite_params"]);
				// Do we want to generate the style properties?
				if ($spriteParams["use_style"] or $ignoreCache) {
					$style = sprintf("background:url('%s') no-repeat top left;background-position:%dpx %dpx;width:%dpx;height:%dpx;", $rank["rank_content"], $spriteParams["x"], $spriteParams["y"], $spriteParams["w"], $spriteParams["h"]);
				} else {
					// Fetch them from DataRegistry and reference it instead
					if ($rank["_useCSSSpriteSheetId"]) { // Are we given this key (from processRanks)?
						$bgId = $rank["_useCSSSpriteSheetId"];
					} else { // Nope
						$cache = $dr->get("3ps_ugr_spriteBgAssoc");
						$bgId = $cache[$rank["rid"]];
					}
					$rankSpriteClass = "3ps_ugr_rankSpriteBg" . $bgId . " 3ps_ugr_rankSpriteContent" . $rank["rid"];
				}
				break;
		}
		$finalStyle = ($style) ? sprintf(' style="%s"', $style) : "";
		// Any class to apply?
		$class = ($rank["rank_styling_class"] or $rankSpriteClass) ? sprintf(' class="%s %s"', $rankSpriteClass, $rank["rank_styling_class"]) : '';
		// Assemble time!
		return '<' . $returnTag . $class . $finalStyle . '>' . $content . '</' . $returnTag . '>';
	}

	public static function _intvalItems(&$item, $key) {
		$item = intval($item);
	}

	/**
	 * Fetches a model object from the local cache
	 *
	 * @param string $modelName
	 *
	 * @return XenForo_Model
	 */
	protected static function _getModelFromCache($modelName) {
		if (!isset(self::$_modelCache[$modelName])) {
			self::$_modelCache[$modelName] = XenForo_Model::create($modelName);
		}
		return self::$_modelCache[$modelName];
	}
}