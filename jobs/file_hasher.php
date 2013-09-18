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
			foreach (FileHasherModel::getEnabledHashes() as $value) {
				$r = $db->Execute('select Files.fID from Files left join FileSearchIndexAttributes fsia on Files.fID = fsia.fID where (ak_file_hasher_'.$value.' is null or ak_file_hasher_'.$value.' = \'\')');
				while ($row = $r->FetchRow()) {
					$q->send($row['fID'].'|*|'.$value);//I really hope a hash never uses |*|
				}
			}
		}
	}

	public function finish(Zend_Queue $q) {
		$db = Loader::db();
		$total = $db->GetOne('select count(*) from FileSearchIndexAttributes');
		return t('Hashes updated. %s files hashed.', $total);
	}

	public function processQueueItem(Zend_Queue_Message $msg) {
		$info = explode('|*|', $msg->body);
		$c = File::getByID($info[0], 'ACTIVE');
		$cv = $c->getFile();
		if (is_object($cv)) {
			$hash = hash_file($info[1], $cv->getPath());
			$c->setAttribute('file_hasher_'.$info[1], $hash);
			
		}
	}


}