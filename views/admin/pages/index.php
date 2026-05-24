<?php
	$stateLabels = array(
		'current'      => 'Installed and up to date',
		'outdated'     => 'Installed but outdated - update available',
		'legacy'       => 'Older unmarked version present - update available',
		'missing'      => 'Not installed',
		'missing_file' => 'Main controller file not found',
	);
?>

<?php echo text_output($title, 'h1', 'page-head');?>


<?php /* ---------- Status ---------- */ ?>

<?php echo text_output('Status', 'h3', 'page-subhead');?>

<table class="table100 zebra">
	<tbody>
		<tr>
			<td class="cell-label">Contact &amp; Join form code</td>
			<td class="cell-spacer"></td>
			<td><?php echo $stateLabels[$controller_state];?></td>
		</tr>
	</tbody>
</table>

<br>


<?php /* ---------- Controller code ---------- */ ?>

<?php if ($controller_state === 'current'): ?>
	<?php /* nothing to do */ ?>

<?php elseif ($controller_state === 'missing_file'): ?>
	<?php echo text_output('Controller code', 'h3', 'page-subhead');?>
	<p>
		<code>application/controllers/Main.php</code> was not found. Restore the file from your Nova install before continuing.
	</p>
	<br>

<?php else: ?>
	<?php echo text_output('Controller code', 'h3', 'page-subhead');?>
	<p>
		<?php if ($controller_state === 'outdated'): ?>
			The injected contact/join code in <code>application/controllers/Main.php</code> is out of date and will be replaced.
		<?php elseif ($controller_state === 'legacy'): ?>
			An older, unmarked version of <code>contact()</code> / <code>join()</code> is present in
			<code>application/controllers/Main.php</code> and will be replaced with the current shims.
		<?php else: ?>
			Inject the anti-spam shims into <code>application/controllers/Main.php</code> so the contact and join forms
			verify the security answer before processing.
		<?php endif; ?>
	</p>
	<?php echo form_open('extensions/nova_ext_anti_spam_questions/Manage/index/');?>
		<button name="action" type="submit" class="button-main" value="install_controller_code">
			<span><?php echo ($controller_state === 'missing') ? 'Install Controller Code' : 'Update Controller Code';?></span>
		</button>
	<?php echo form_close();?>
	<br>
<?php endif; ?>


<?php /* ---------- Questions ---------- */ ?>

<?php echo text_output('Questions', 'h3', 'page-subhead');?>

<p>
	<?php echo anchor('extensions/nova_ext_anti_spam_questions/Manage/create', img($images['add']).' '.'Add Question', array('class' => 'image'));?>
</p>

<?php if ( ! empty($models)): ?>
	<table class="table100 zebra">
		<tbody>
		<?php foreach ($models as $model): ?>
			<?php $jsonDecode = json_decode($model->setting_value, true); ?>
			<tr class="alt">
				<td>
					<strong><?php echo htmlspecialchars(isset($jsonDecode['question']) ? $jsonDecode['question'] : '', ENT_QUOTES);?></strong><br />
					<span class="gray fontSmall">
						<?php echo htmlspecialchars(implode(', ', isset($jsonDecode['answer']) ? $jsonDecode['answer'] : array()), ENT_QUOTES);?>
					</span>
				</td>
				<td class="col_75 align_right">
					<a href="#" myAction="delete" myID="<?php echo $model->setting_id;?>" rel="facebox" class="image"><?php echo img($images['delete']);?></a>
					<?php echo anchor('extensions/nova_ext_anti_spam_questions/Manage/edit/'.$model->setting_id, img($images['edit']), array('class' => 'image'));?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
<?php else: ?>
	<?php echo text_output('No questions yet. Add one above.', 'h3', 'orange');?>
<?php endif;?>
