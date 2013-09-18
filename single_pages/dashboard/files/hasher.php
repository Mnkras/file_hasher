<?php defined('C5_EXECUTE') or die('Access Denied');

$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form');
?>
<?php echo $h->getDashboardPaneHeaderWrapper(t('File Hasher'), false, false, false);?>
<form id="file-hasher-form" action="<?php echo $this->action('save')?>" method="post">
	<?php //echo $this->controller->token->output('file_hasher')?>

<div class="ccm-pane-body">
	<div class="clearfix">
		<?php echo $form->label('hashes', t('Hash Types'))?>
		<div class="input">
			<ul class="inputs-list">
				<?foreach($available as $item):?>
				<li>
					<label>
						<?php
						$check = false;
						if(count($enabled) > 0 && in_array($item, $enabled)) {
							$check = true;
						}
						echo $form->checkbox('hashes[]', $item, $check)?>
						<span><?php echo $item?></span>
					</label>
				</li>
				<?endforeach?>
			</ul>
			<span class="help-block"><?php echo t('Un-checking a box and saving will remove the associated attribute and all data. There is no way to undo this.')?></span>
		</div>
	</div>
</div>
<div class="ccm-pane-footer">
<?php
	$submit = $ih->submit( t('Save'), 'file-hasher-form', 'right', 'primary');
	print $submit;
?>
</div>

</form>
<?php echo $h->getDashboardPaneFooterWrapper(false);?>