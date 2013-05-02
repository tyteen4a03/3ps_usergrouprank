<?php
/*
* Usergroup Ranks v1.6.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_UsergroupRanks_Model_DataRegistryPlus extends XenForo_Model_DataRegistry {
	var $namespacePrefix = "3ps_ugr_";
	var $cacheProperties = array(
		"hasCache" => false,
		"hasTags" => false,
		"cachingClass" => '',
		"mirroring" => true,
	);
	static $_hasTagsSupport = array(
		'Zend_Cache_Backend_File', 'Zend_Cache_Backend_Sqlite', 'Zend_Cache_Backend_ZendPlatform',
		'Zend_Cache_Backend_TwoLevels' // Tags are supported via the slow backend
	);

	public function __construct() {
		// Populate $cacheProperties
		$this->cacheProperties["hasCache"] = $this->hasCache();
		if ($this->cacheProperties["hasCache"]) {
			$this->cacheProperties["cachingClass"] = $this->_cache->getBackend();
			// Check if the caching backend supports tags
			$this->cacheProperties["hasTags"] = in_array($this->cacheProperties["cachingClass"], self::$_hasTagsSupport);
		}
	}

	public function setMirroring($option) {
		$this->cacheProperties["mirroring"] = $option;
	}

	public function hasCache() {
		return ($this->_cache != false);
	}

	public function getIds() {
		if ($this->cacheProperties["hasCache"]) {
			return $this->_cache->getIds();
		} else {
			return $this->_getDb()->fetchCol("SELECT data_key FROM xf_data_registry ORDER BY data_key ASC");
		}
	}

	public function getIdsMatchingAnyTags($tags = array()) {
		if ($this->cacheProperties["hasCache"]) {
			return $this->_cache->getIdsMatchingAnyTags($tags);
		} else {

		}
	}

	public function getIdsMatchingTags($tags = array()) {
		if ($this->cacheProperties["hasCache"]) {
			return $this->_cache->getIdsMatchingTags($tags);
		} else {

		}
	}

	public function getIdsNotMatchingTags($tags = array()) {
		if ($this->cacheProperties["hasCache"]) {
			return $this->_cache->getIdsNotMatchingTags($tags);
		} else {

		}
	}

	public function getTags() {
		if ($this->cacheProperties["hasCache"]) {
			if ($this->cacheProperties["hasTags"]) {
				return $this->_cache->getTags();
			} else {
				return $this->getInNamespace("tags");
			}
		} else {
			return $this->_getDb()->fetchCol("SELECT tag_name FROM xf_data_registry_tags ORDER BY tag_name ASC");
		}
	}

	/**
	 * Gets the named item.
	 *
	 * @param string $itemName
	 *
	 * @return mixed|null Value of the entry or null if it couldn't be found
	 */
	public function get($itemName) {
		// Try to load it from the caching mechanism first
		if ($this->cacheProperties["hasCache"]) {
			$cacheData = $this->_cache->load($itemName);
			if ($cacheData != false) {
				return unserialize($cacheData);
			}
			if (!$this->cacheProperties["mirroring"]) {
				return null; // Cache data doesn't exist
			}
		}
		$cacheData = $this->_getDb()->fetchOne("SELECT data_value FROM xf_data_registry WHERE data_key = ?", $itemName);
		if ($this->cacheProperties["hasCache"] and $this->cacheProperties["mirroring"] and $cacheData != '') {
			// Save the value back into the cache
			$this->_cache->save($cacheData, $itemName);
		}
		return ($cacheData != '') ? unserialize($cacheData) : null;
	}

	public function getInNamespace($id) {
		return $this->get($this->namespacePrefix . $id);
	}

	/**
	 * Sets a data registry value into the DB and updates the cache object.
	 *
	 * @param string $itemName
	 * @param mixed $value
	 * @param array $tags A list of tags to mark the data with
	 */
	public function set($itemName, $value, $tags = array()) {
		$serialized = serialize($value);
		$implodedTags = implode(",", $tags);
		if ($this->cacheProperties["hasCache"]) {
			if ($this->cacheProperties["hasTags"]) {
				// Let the caching mechanism worry about tags
				$this->_cache->save($value, $itemName, $tags);
			} else {
				// Try getting the tag, if the entry already exists
				$cacheTags = $this->getInNamespace("tags_association");
				foreach ($tags AS $tag) {
					if (array_key_exists($tag, $cacheTags)) {
						// Store the association
						$explodedAssoc = explode(",", $cacheTags[$tag]);
						if (in_array($itemName, $explodedAssoc)) {
							// Already in list, ignore
							continue;
						} else {
							$explodedAssoc[] = $itemName;
							$cacheTags[$tag] = implode(",", $explodedAssoc);
						}
					} else {
						// Create new key
						$cacheTags[$tag] = strval($itemName);
					}
				}
				$this->setInNamespace("tags_association", $cacheTags);
				// Now save the data
				$this->_cache->save($serialized, $itemName);
			}
		}
		// Save to database as well, if we need to
		if ($this->cacheProperties["mirroring"] or !$this->cacheProperties["hasCache"]) {
			$this->_getDb()->query("INSERT INTO xf_data_registry (data_key, data_value, tags) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE data_value = VALUES(data_value)", $itemName, $value, $implodedTags);
		}
	}

	public function setInNamespace($itemName, $value, $tags = array()) {
		$this->set($this->namespacePrefix . $itemName, $value, $tags);
	}

	public function clean($mode = 'all', $tags = array()) {

	}

	public function delete($id) {
		if ($this->cacheProperties["hasCache"]) {
			$this->_cache->remove($id);
		}
		if ($this->cacheProperties["mirroring"] or !$this->cacheProperties["hasCache"]) {
			$db = $this->_getDb();
			$db->delete('xf_data_registry', 'data_key = ' . $db->quote($id));
		}
	}
}