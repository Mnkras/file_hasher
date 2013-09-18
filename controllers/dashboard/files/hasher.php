<?php defined('C5_EXECUTE') or die('Access Denied');

Loader::model('file_hasher', 'file_hasher');

class DashboardFilesHasherController extends Controller {

	public function view($saved = false) {
		if($saved) {
			$this->set('success', t('Settings Successfully Saved.'));
		}
		$avail = FileHasherModel::getAvailableHashes();
		$this->set('available', $avail);

		$enabled = FileHasherModel::getEnabledHashes();
		$this->set('enabled', $enabled);

	}

	public function save() {
		FileHasherModel::saveEnabledHashes($this->post('hashes'));
		$this->redirect('/dashboard/files/hasher', '1');
	}

}