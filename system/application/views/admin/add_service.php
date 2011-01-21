			<form method="post">
				<h2>Services &raquo; <span class="current_page">Add a Service</span></h2>
				<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
				<input type="text" name="big_idea" placeholder="Big Idea" class="primary" /><br />
				<input type="text" name="series_title" placeholder="Series Title" /><br />
				<input type="text" name="twitter_hash" placeholder="Hash Tag" /><br />
				<select name="status">
					<option value="published">Published</option>
					<option value="draft">Draft</option>
				</select><br />
				<fieldset>
					<legend>Time &amp; Date</legend>
					<select name="schedule_id">
						<?php foreach($schedule->result() as $row):?>
							<?php
								$row->time = $row->time / 60;
								$hours     = floor($row->time / 60);
								$minutes   = $row->time - (floor($row->time / 60) * 60);
								$row->time = $hours.($minutes < 10 ? '0' : '').$minutes;
								
								preg_match('/([0-9]?[0-9])([0-9]{2})/', $row->time, $matches);
								if ($matches[1] > 11) {
									$time = ($matches[1] != 12 ? $matches[1] - 12 : $matches[1]).$matches[2].' PM';
								}
								else {
									$time = $matches[1].$matches[2].' AM';
								}
								$time = preg_replace('/([0-9]?[0-9])([0-9]{2})/', '$1:$2', $time);
								
								switch ($row->day_of_week) {
									case 0:
										$day = 'Sunday';
										break;
									case 1:
										$day = 'Monday';
										break;
									case 2:
										$day = 'Tuesday';
										break;
									case 3:
										$day = 'Wednesday';
										break;
									case 4:
										$day = 'Thursday';
										break;
									case 5:
										$day = 'Friday';
										break;
									case 6:
										$day = 'Saturday';
										break;
								}
							?>
							<option value="<?=$row->id?>"><?=$time?> on <?=$day?></option>
						<?php endforeach; ?>
					</select>
					<input type="text" name="date" placeholder="Day" class="secondary" />
				</fieldset>
				<fieldset id="schedule">
					<legend>Video Schedule</legend>
					<ol>
						<li rel="max(1) type(greeting)">
							<h3>Greeting <span class="note">(select 1 video)</span></h3>
							<ul>
								<!--<li><span class="time misc" rel="63"></span> <span class="title">February 1st - Welcome</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>//-->
								<li class="add_video"><a href="#">add video</a><br /></li>
							</ul>
						</li>
						<li rel="max(3) type(music)">
							<h3>Music <span class="note">(select 2-3 videos)</span></h3>
							<ul>
								<!--<li><span class="time music" rel="189"></span> <span class="title">Song 1</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>
								<li><span class="time music" rel="302"></span> <span class="title">Song 2</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>//-->
								<li class="add_video"><a href="#">add video</a><br /></li>
							</ul>
						</li>
						<li rel="max(1) type(misc)">
							<h3>Announcements <span class="note">(select none-1 video)</span></h3>
							<ul>
								<!--<li><span class="time music" rel="189"></span> <span class="title">Song 1</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>
								<li><span class="time music" rel="302"></span> <span class="title">Song 2</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>//-->
								<li class="add_video"><a href="#">add video</a><br /></li>
							</ul>
						</li>
						<li rel="max(1) type(teaching)">
							<h3>Teaching <span class="note">(select 1 video)</span></h3>
							<ul>
								<!--<li><span class="time music" rel="189"></span> <span class="title">Song 1</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>
								<li><span class="time music" rel="302"></span> <span class="title">Song 2</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>//-->
								<li class="add_video"><a href="#">add video</a><br /></li>
							</ul>
						</li>
						<li rel="max(1) type(communion)">
							<h3>Communion <span class="note">(select 1 video)</span></h3>
							<ul>
								<!--<li><span class="time misc" rel="137"></span> <span class="title">February 1st - Communion</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>//-->
								<li class="add_video"><a href="#">add video</a><br /></li>
							</ul>
						</li>
						<li rel="max(1) type(gbtg)">
							<h3>Giving Back to God <span class="note">(select 1 video)</span></h3>
							<ul>
								<!--<li><span class="time music" rel="201"></span> <span class="title">Song 3</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>//-->
								<li class="add_video"><a href="#">add video</a><br /></li>
							</ul>
						</li>
						<li rel="max(1) type(music)">
							<h3>Music <span class="note">(select none-1 video)</span></h3>
							<ul>
								<!--<li><span class="time music" rel="201"></span> <span class="title">Song 3</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>//-->
								<li class="add_video"><a href="#">add video</a><br /></li>
							</ul>
						</li>
						<li rel="max(1) type(dismissal)">
							<h3>Dismissal <span class="note">(select 1 video)</span></h3>
							<ul>
								<!--<li><span class="time teaching" rel="1658"></span> <span class="title">February 1st - Teaching</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><br /></li>//-->
								<li class="add_video"><a href="#">add video</a><br /></li>
							</ul>
						</li>
					</ol>
				</fieldset>
				<fieldset id="dynamic_content">
					<legend>Dynamic Content</legend>
					<ol>
						<!--<li>
							<ul>
								<li><span class="time music" rel="start(68) end(130)">01:08 - 2:10</span> <span><strong>During "Song 1"</strong></span> <a href="#" class="edit">edit</a> <a href="#" class="remove">remove</a><br /></li>
							</ul>
						</li>//-->
					</ol>
					<a href="#" class="add_dc">add content</a>
				</fieldset>
				<fieldset id="save_form">
					<input type="submit" value="Save Service" /> or <a href="<?=site_url('admin/services')?>">cancel</a>
				</fieldset>
			</form>
			<div id="sidebar">
				<h2>Navigate Services</h2>
				<ul>
					<li><a href="<?=site_url('admin')?>" class="selected">Add Service</a></li>
					<li><a href="<?=site_url('admin/services')?>">View Service List</a></li>
				</ul>
			</div>