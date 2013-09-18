<?php defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('file_hasher', 'file_hasher');

class FileHasher extends QueueableJob {

	public $jSupportsQueue = true;

	public $type = 'md5';

	public function getJobName() {
		return t("File Hasher");
	}

	public function getJobDescription() {
		return t("Generates hashes for uploaded files.");
	}

	public function start(Zend_Queue $q) {
		if(count(FileHasherModel::getEnabledHashes()) > 0) {
			$db = Loader::db();
			//$r = $db->Execute('select Files.fID from Files left join FileSearchIndexAttributes fsia on Files.fID = fsia.fID where (ak_file_hasher_'.$this->type.' is null or ak_file_hasher_'.$this->type.' = \'\')');
			$r = $db->Execute('select Files.fID from Files left join FileSearchIndexAttributes fsia on Files.fID = fsia.fID where '.$this->generateQuery());
			while ($row = $r->FetchRow()) {
				$q->send($row['fID']);
			}
		}
	}

	private function generateQuery() {
		$enabled = FileHasherModel::getEnabledHashes();
		if(count($enabled) == 0) {
			return '1=0';//fail
		}
		$str = '';
		$lastindex = count($enabled) - 1;
		foreach ($enabled as $index => $value) {
			$str .= '(ak_file_hasher_'.$value.' is null or ak_file_hasher_'.$value.' = \'\')';
			if($index != $lastindex) {
				$str .= ' or ';
			}
		}
		return $str;
	}

	public function finish(Zend_Queue $q) {
		$db = Loader::db();
		$total = $db->GetOne('select count(*) from FileSearchIndexAttributes');
		return t('Hashes updated. %s files hashed.', $total);
	}

	public function processQueueItem(Zend_Queue_Message $msg) {
		$c = File::getByID($msg->body, 'ACTIVE');
		$cv = $c->getFile();
		if (is_object($cv)) {
			$enabled = FileHasherModel::getEnabledHashes();
			foreach ($enabled as $value) {
				$hash = hash_file($value, $cv->getPath());
				$c->setAttribute('file_hasher_'.$value, $hash);
			}
			
		}
	}


}