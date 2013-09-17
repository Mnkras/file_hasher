<?php    defined('C5_EXECUTE') or die("Access Denied.");

class FileHasherPackage extends Package {

	protected $pkgHandle = 'file_hasher';
	protected $appVersionRequired = '5.6.2';
	protected $pkgVersion = '.1.3.3.7';
	
	public function getPackageDescription() {
		return t("Generate Hashes for files");
	}
	
	public function getPackageName() {
		return t("File Hasher");
	}
	
	public function install() {
		$pkg = parent::install();

		FileAttributeKey::add('text', array('akHandle' => 'file_hasher_md5', 'akName' => t('MD5'), 'akIsSearchable' => true), $pkg);
		Job::installByPackage('file_hasher', $pkg);
	}
}