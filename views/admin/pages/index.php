<?php echo text_output($title, 'h1', 'page-head');?>
<p>
<?php echo anchor('extensions/nova_ext_anti_spam_questions/Manage/create', img($images['add']) .' '. 'Add Question', array('class' => 'image'));?>
</p>




<?php if(empty($write)){ ?>

	<?php echo form_open('extensions/nova_ext_anti_spam_questions/Manage/index/');?>
	<br>
	
     
	<button name="submit" type="submit" class="button-main" value="write"><span>Update Controller Configuration</span></button>

    
	<?php echo form_close(); ?>
<?php } else { ?>
   <div class="email-message"><br>Contact & Join Configuration located, and up to date.</div>
<?php } ?>
<br>
<?php if (!empty($models)): ?>
	
	
	<table class="table100 zebra">
		<tbody>
		<?php foreach ($models as $model): ?>
			<tr class="alt"> <?php $jsonDecode=json_decode($model->setting_value,true)?>
				<td>
					<strong><?=$jsonDecode['question']?></strong><br />
					<span class="gray fontSmall">
						<strong><?=implode(',', $jsonDecode['answer'])?></strong>
					</span>
				</td>
				<td class="col_75 align_right">
					<a href="#" myAction="delete" myID="<?php echo $model->setting_id;?>" rel="facebox" class="image"><?php echo img($images['delete']);?></a>
					 
					<?php echo anchor('extensions/nova_ext_anti_spam_questions/Manage/edit/'. $model->setting_id, img($images['edit']), array('class' => 'image'));?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
<?php else: ?>
	<?php echo text_output('No question found', 'h3', 'orange');?>
<?php endif;?>
