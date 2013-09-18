<?php defined('C5_EXECUTE') or die("Access Denied.");

class FileHasherModel {

	public static function getAvailableHashes() {
		return hash_algos();
	}

	public static function getEnabledHashes() {
		$pkg = Package::getByHandle('file_hasher');
		$json = Loader::helper('json');
		$ret = $json->decode($pkg->config('enabled_hashes'));
		if(!is_array($ret)) {
			$ret = array();
		}
		return $ret;
	}

	public static function saveEnabledHashes($arr = array()) {
		$pkg = Package::getByHandle('file_hasher');
		self::removeAllHashes(false);
		if(count($arr) > 0) {
			foreach($arr as $item) {
				if(in_array($item, self::getAvailableHashes()) && !is_object(FileAttributeKey::getByHandle('file_hasher_'.$item))) {
					FileAttributeKey::add('text', array('akHandle' => 'file_hasher_'.$item, 'akName' => t($item), 'akIsSearchable' => true), $pkg);
				}
			}
		}
		$json = Loader::helper('json');
		$pkg = Package::getByHandle('file_hasher');
		$pkg->saveConfig('enabled_hashes', $json->encode($arr));


	}

	public static function removeAllHashes($run = true) {
		$list = AttributeKey::getListByPackage(Package::getByHandle('file_hasher'));
		if($run) {
			self::saveEnabledHashes();//clear out the enabled
		}
		if(count($list) > 0) {
			foreach($list as $item) {
				if (is_object($item) && !in_array(substr($item->getAttributeKeyHandle(), 12), self::getEnabledHashes())) {
					$item->delete();
				}
			}
		}
	}


}