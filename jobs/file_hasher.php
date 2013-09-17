<?php defined('C5_EXECUTE') or die("Access Denied.");
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

		$db = Loader::db();
		$r = $db->Execute('select Files.fID from Files left join FileSearchIndexAttributes fsia on Files.fID = fsia.fID where (ak_file_hasher_md5 is null or ak_file_hasher_md5 = \'\')');
		while ($row = $r->FetchRow()) {
			$q->send($row['fID']);
		}
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
			$hash = hash_file($this->type, $cv->getPath());
			$c->setAttribute('file_hasher_'.$this->type, $hash);
		}
	}


}