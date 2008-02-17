<?= format_messages($messages, $message_type); ?>
<?php if ($action == 'add'): ?>
	<?= form_open_multipart('admin/do_add_party'); ?>
<?php elseif ($action == 'edit'): ?>
	<?= form_open_multipart('admin/do_edit_party/' . $party['id']); ?>
<?php endif; ?>
<h2><?= e('admin_' . $action . '_party_label'); ?></h2>
<table cellpadding="0" cellspacing="0" border="0" class="form_table">
	<tr>
		<td width="30%" align="right">
			<?= e('admin_party_party'); ?>:
		</td>
		<td width="70%">
			<?= form_input(array('name'=>'party', 'value'=>$party['party'], 'class'=>'text')); ?>
		</td>
	</tr>
	<tr>
		<td width="30%" align="right">
			<?= e('admin_party_alias'); ?>:
		</td>
		<td width="70%">
			<?= form_input(array('name'=>'alias', 'value'=>$party['alias'], 'class'=>'text')); ?>
		</td>
	</tr>
	<tr>
		<td width="30%" align="right">
			<?= e('admin_party_description'); ?>:
		</td>
		<td width="70%">
			<?= form_textarea(array('name'=>'description', 'value'=>$party['description'])); ?>
		</td>
	</tr>
	<tr>
		<td width="30%" align="right">
			<?= e('admin_party_logo'); ?>:
		</td>
		<td width="70%">
			<?= form_upload(array('name'=>'logo')); ?>
		</td>
	</tr>
</table>
<div class="paging">
	<?= anchor('admin/parties', 'GO BACK'); ?>
	|
	<?= form_submit('submit', e('admin_' . $action . '_party_submit')) ?>
</div>
<?= form_close(); ?>