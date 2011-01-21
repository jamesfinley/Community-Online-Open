			<?php echo form_open_multipart('admin/big_idea/edit/'.$big_idea->id);?>
				<h2>Big Idea &raquo; <span class="current_page">Add a Big Idea</span></h2>
				<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
				<input type="text" name="series_title" placeholder="Series Title"  value="<?=$big_idea->series_title?>"/><br />
				<select name="category">
					<option value="adults" <?php echo $big_idea->category == 'adults' ? 'selected="true"' : FALSE?>>Adults</option>
					<option value="students" <?php echo $big_idea->category == 'students' ? 'selected="true"' : FALSE?>>Students</option>
					<option value="kids" <?php echo $big_idea->category == 'kids' ? 'selected="true"' : FALSE?>>Kids</option>
				</select><br />
				<fieldset>
					<legend>Times</legend>
					<input type="text" name="begin_at" placeholder="Begin On" class="secondary"  value="<?=gmdate('F j, Y', $big_idea->begin_at)?>" /><br />
				</fieldset>
				<fieldset>
					<legend>Description</legend>
					<textarea name="long_description" class="long" rows="10"><?=$big_idea->long_description?></textarea>
					<textarea name="short_description" class="short" rows="3"><?=$big_idea->short_description?></textarea>
				</fieldset>
				<fieldset>
					<legend>Style</legend>
					<input type="text" name="description_x" placeholder="Description X Position" class="secondary"  value="<?=$big_idea->description_x?>" /><br />
					<input type="text" name="description_y" placeholder="Description Y Position" class="secondary"  value="<?=$big_idea->description_y?>" /><br />
					<input type="text" name="videos_x" placeholder="Videos X Position" class="secondary"  value="<?=$big_idea->videos_x?>" /><br />
					<input type="text" name="videos_y" placeholder="Videos Y Position" class="secondary"  value="<?=$big_idea->videos_y?>" /><br />
					<input type="text" name="background_color" placeholder="Background Color" class="secondary"  value="<?=$big_idea->background_color?>" /><br />
					<input type="text" name="border_color" placeholder="Border Color" class="secondary"  value="<?=$big_idea->border_color?>" /><br />
					<input type="text" name="text_color" placeholder="Text Color" class="secondary"  value="<?=$big_idea->text_color?>"/><br />
					<input type="file" name="background_image"/><br />
				</fieldset>
				<fieldset>
					<input type="file" name="file[0]" /><br />
					<input type="file" name="file[1]" /><br />
				</fieldset>
				<fieldset id="save_form">
					<input type="submit" value="Save Big Idea" /> or <a href="<?=site_url('admin/big_idea')?>">cancel</a>
				</fieldset>
			</form>
			<div id="sidebar">
				<h2>Navigate Services</h2>
				<ul>
					<li><a href="<?=site_url('admin/big_idea/add')?>">Add Big Idea</a></li>
					<li><a href="<?=site_url('admin/big_idea')?>" class="selected">View Big Idea List</a></li>
				</ul>
			</div>