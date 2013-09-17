<?php   defined('C5_EXECUTE') or die("Access Denied.");
	$f = $controller->getFileObject();
	$fp = new Permissions($f);
	if ($fp->canViewFile()) { 
		$c = Page::getCurrentPage();
		if($c instanceof Page) {
			$cID = $c->getCollectionID();
		}
		if($f->getAttribute('file_hasher_md5')) {
			$hash = $f->getAttribute('file_hasher_md5');
		} else {
			$hash = t('Unknown');
		}
?>
<a href="<?php echo  View::url('/download_file', $controller->getFileID(),$cID) ?>"><?php echo  stripslashes($controller->getLinkText()) ?></a> <span class="file-hasher file-hasher-md5"><?php echo t('MD5: %s', $hash)?></span>
 
<?php 
}