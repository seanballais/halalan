<div class="menu">
	<div id="left_menu">
		<ul>
			<li class="active"><img src="<?= base_url(); ?>public/images/user.png" alt="voter" /> VOTES</li>
		</ul>
	</div>
	<div id="right_menu">
		<p>LOGGED IN AS <?= $username; ?> | <?= anchor('gate/logout', 'LOGOUT'); ?></p>
	</div>
	<div class="clear"></div>
</div>
<?php for ($i = 0; $i < count($positions); $i++): ?>
<?php if ($i % 2 == 0): ?>
<div class="body">
	<div class="left_body">
<?php else: ?>
	<div class="right_body">
<?php endif; ?>
		<fieldset>
			<legend><span class="position"><?= $positions[$i]['position']; ?></span> (<?= $positions[$i]['maximum']; ?>)</legend>
			<table cellspacing="2" cellpadding="2">
				<?php if (empty($positions[$i]['candidates'])): ?>
				<tr>
					<td><em><?= e('voter_votes_no_candidates'); ?></em></td>
				</tr>
				<?php else: ?>
				<?php foreach ($positions[$i]['candidates'] as $key=>$candidate): ?>
				<?php
					$name = $candidate['first_name'];
					if (!empty($candidate['alias']))
						$name .= ' "' . $candidate['alias'] . '"';
					$name .= ' ' . $candidate['last_name'];
					$name = quotes_to_entities($name);
				?>
				<?php if ($candidate['voted']): ?>
				<tr>
				<?php else: ?>
				<tr class="not_selected">
				<?php endif; ?>
					<td width="5%" align="center">
					<?php if ($candidate['voted']): ?>
					<?= img('public/images/ok.png'); ?>
					<?php else: ?>
					<?= img('public/images/x.png'); ?>
					<?php endif; ?>
					</td>
					<td width="70%"><?= $name; ?></td>
					<?php if ($settings['show_candidate_details']): ?>
					<td width="20%">
					<?php else: ?>
					<td width="25%">
					<?php endif; ?>
						<?php if (isset($candidate['party']['party']) && !empty($candidate['party']['party'])): ?>
						<?php if (empty($candidate['party']['alias'])): ?>
						<?= $candidate['party']['party']; ?>
						<?php else: ?>
						<?= $candidate['party']['alias']; ?>
						<?php endif; ?>
						<?php endif; ?>
					</td>
					<?php if ($settings['show_candidate_details']): ?>
					<td width="5%"><a href="#" title="Info on <?= $name; ?>" onclick="return makeFalse(domTT_activate(this, event, 'caption', '&lt;span style=&quot;width : 300px;&quot;&gt;&lt;strong&gt;Information&lt;/strong&gt;&lt;/span&gt;', 'content', setContent('<?= $candidate['id']; ?>', '<?= $name; ?>', '<?= $candidate['picture']; ?>', '<?= $candidate['description']; ?>', '<?= (isset($candidate['party']['party']) && !empty($candidate['party']['party'])) ? $candidate['party']['party'] . (!empty($candidate['party']['alias']) ? ' (' . $candidate['party']['alias'] . ')' : '') : 'none'; ?>', '<?= (isset($candidate['party']['logo']) && !empty($candidate['party']['logo'])) ? $candidate['party']['logo'] : ''; ?>',  '<?= base_url(); ?>/public/'), 'type', 'sticky', 'closeLink', '[X]', 'draggable', true));"><img src="<?= base_url(); ?>public/images/info.png" alt="info" /></a></td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
				<?php if ($positions[$i]['abstain'] == TRUE): ?>
				<?php if ($positions[$i]['abstains']): ?>
				<tr>
				<?php else: ?>
				<tr class="not_selected">
				<?php endif; ?>
					<td width="5%" align="center">
					<?php if ($positions[$i]['abstains']): ?>
					<?= img('public/images/ok.png'); ?>
					<?php else: ?>
					<?= img('public/images/x.png'); ?>
					<?php endif; ?>
					</td>
					<td width="95%">ABSTAIN</td>
				</tr>
				<?php endif; ?>
				<?php endif; ?>
			</table>
		</fieldset>
	</div>
<?php if ($i % 2 == 1): ?>
	<div class="clear"></div>
</div>
<?php endif; ?>
<?php endfor; ?>
<?php // in case the number of positions is odd ?>
<?php if (count($positions) % 2 == 1): ?>
	<div class="right_body">
	</div>
	<div class="clear"></div>
</div>
<?php endif; ?>
<div class="menu" id="menu_center">
	<div id="center_menu">
		&nbsp;
	</div>
	<div class="clear"></div>
</div>
</form>