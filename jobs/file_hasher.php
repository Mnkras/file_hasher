<?php defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('file_hasher', 'file_hasher');

class FileHasher extends QueueableJob {

	public $jSupportsQueue = true;

	public $randomSeperator = '|*|';

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
				$r = $db->Execute('select Files.fID from Files left join FileSearchIndexAttributes fsia on Files.fID = fsia.fID where (ak_file_hasher_'.$value.' is null or ak_file_hasher_'.$value.' = \'\') and (ak_file_hasher_exclude_file is null or ak_file_hasher_exclude_file = false)');
				while ($row = $r->FetchRow()) {
					$q->send($row['fID'].$this->randomSeperator.$value);//I really hope a hash never uses |*|
				}
			}
		}
	}

	public function finish(Zend_Queue $q) {
		if(count(FileHasherModel::getEnabledHashes()) > 0) {
			$db = Loader::db();
			$total = $db->GetOne('select count(*) from FileSearchIndexAttributes where (ak_file_hasher_exclude_file is null or ak_file_hasher_exclude_file = false)');
			$enabled = FileHasherModel::getEnabledHashes();
			if(version_compare(APP_VERSION, '5.6.2.2', '>=')) {
				$enabled = implode(', ', $enabled);
				return t('Hashes updated. %s files hashed with %s.', $total, $enabled);
			} else {
				return t('Hashes updated. %s files hashed with %s hashes.', $total, count($enabled));
			}
		}
		throw new Exception(t('Please choose at least one hash type before running this job!'));
	}

	public function processQueueItem(Zend_Queue_Message $msg) {
		$info = explode($this->randomSeperator, $msg->body);
		$c = File::getByID($info[0], 'ACTIVE');
		$cv = $c->getFile();
		if (is_object($cv) && $info[1]) {
			$hash = hash_file($info[1], $cv->getPath());
			$c->setAttribute('file_hasher_'.$info[1], $hash);
			
		}
	}


}