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
		//Loader::model('file_hasher', 'file_hasher');
		//FileHasherModel::saveEnabledHashes();
		//FileAttributeKey::add('text', array('akHandle' => 'file_hasher_md5', 'akName' => t('MD5'), 'akIsSearchable' => true), $pkg);
		Job::installByPackage('file_hasher', $pkg);

		$p = SinglePage::add('/dashboard/files/hasher',$pkg);
		$p->update(array('cName'=>t('Hasher')));
		$p->setAttribute('icon_dashboard', 'icon-filter');
		$json = Loader::helper('json');
		$pkg->saveConfig('enabled_hashes', $json->encode(array()));
	}
}