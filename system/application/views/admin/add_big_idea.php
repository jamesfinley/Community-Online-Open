			<?php echo form_open_multipart('admin/big_idea/add');?>
				<h2>Big Idea &raquo; <span class="current_page">Add a Big Idea</span></h2>
				<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
				<label for="">Series Title</label>
				<input type="text" name="series_title" placeholder="Series Title" /><br />
				<label for="">Category:</label>
				<select name="category">
					<option value="adults">Adults</option>
					<option value="students">Students</option>
					<option value="kids">Kids</option>
				</select><br />
				<fieldset>
					<legend>Times</legend>
					<input type="text" name="begin_at" placeholder="Begin On" class="secondary" value="<?=date('m/d/Y')?>" /><br />
				</fieldset>
				<fieldset>
					<legend>Description</legend>
					<label for="">Long</label>
					<textarea name="long_description" class="long" rows="10"></textarea>
					<label for="">Short</label>
					<textarea name="short_description" class="short" rows="3"></textarea>
				</fieldset>
				<fieldset>
					<legend>Style</legend>
					<label for="">X Position of Description</label>
					<input type="text" name="description_x" placeholder="Description X Position" class="secondary" /><br />
					<label for="">Y Position of Description</label>
					<input type="text" name="description_y" placeholder="Description Y Position" class="secondary" /><br />
					<label for="">X Position of Videos</label>
					<input type="text" name="videos_x" placeholder="Videos X Position" class="secondary" /><br />
					<label for="">Y Position of Videos</label>
					<input type="text" name="videos_y" placeholder="Videos Y Position" class="secondary" /><br />
					<label for="">Background Color</label>
					<input type="text" name="background_color" placeholder="Background Color" class="secondary" /><br />
					<label for="">Border Color</label>
					<input type="text" name="border_color" placeholder="Border Color" class="secondary" /><br />
					<label for="">Text Color</label>
					<input type="text" name="text_color" placeholder="Text Color" class="secondary" /><br />
					<label for="">Background Image</label><br />
					<input type="file" name="background_image" /><br />
				</fieldset>
				<fieldset>
					<label for="">File Downloads</label><br />
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
					<li><a href="<?=site_url('admin')?>" class="selected">Add Big Idea</a></li>
					<li><a href="<?=site_url('admin/big_idea')?>">View Big Idea List</a></li>
				</ul>
			</div>