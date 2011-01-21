<div>
	
	<div id="scroller">
		<div class="container collapse_big_ideas"><a class="collapse_big_ideas">Uncollapse</a></div>
		<div id="ideas">
			<div class="big_idea <?=$adult_idea->category?>" style="border-bottom: 1px solid <?=$adult_idea->border_color?>; background: <?=$adult_idea->background_color?>;">
				<div class="container" style="background-image: url(<?=base_url()?>/resources/series/<?=$adult_idea->image?>)">
					<div class="service_info"><?=$adult_idea->series_title?></div>
					<div class="description" style="color: <?=$adult_idea->text_color?>; left:<?=$adult_idea->description_x;?>px; top:<?=$adult_idea->description_y;?>px"><?=$adult_idea->long_description;?><div class="invite_and_download"><a href="mailto:example@sample.com?subject=Join Me!&body=Join me at Community Christian Church! <?=urlencode(base_url())?>" class="invite">Invite a Friend</a>
						<?php
							$files = $this->files_model->items($adult_idea->id, 'big_idea_file');
						?>
						<?php if ($files->num_rows()): ?>
						<a href="#" class="download">Download Files</a>
						<ul class="downloads">
							<?php
								foreach ($files->result() as $file) {
									echo '<li><a href="/user_images/big_idea/'.$file->path.'">'.$file->path.'</a> <span class="size">'.(ceil((filesize('user_images/big_idea/'.$file) / 1024) * pow(10, 2)) / pow(10, 2)).'kb</span><br /></li>';
								}
							?>
						</ul>
						<?php endif; ?>
					</div></div>
					<div class="videos" style="left: <?=$adult_idea->videos_x?>px; top: <?=$adult_idea->videos_y?>px">
					<h3 style="color: <?=$adult_idea->text_color?>;">Recent Videos:</h3>
						<ul>
							<?php foreach($adult_videos->result() as $video): ?>
								<li><a href="http://communitychristian.org/pages/administrator/components/com_threecvideo/video/<?=$video->file?>"><img src="http://communitychristian.org/pages/administrator/components/com_threecvideo/thumb/<?=$video->thumbnail?>" width="109" height="61" /><img src="<?=base_url()?>resources/images/big_idea_video_overlay.png" class="overlay" /></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
			<div class="big_idea <?=$student_idea->category?>" style="border-bottom: 1px solid <?=$student_idea->border_color?>; background: <?=$student_idea->background_color?>;">
				<div class="container" style="background-image: url(<?=base_url()?>/resources/series/<?=$student_idea->image?>)">
					<div class="service_info"><?=$student_idea->series_title?></div>
					<div class="description" style="color: <?=$student_idea->text_color?>; left:<?=$student_idea->description_x;?>px; top:<?=$student_idea->description_y;?>px"><?=$student_idea->long_description;?><div class="invite_and_download"><a href="mailto:example@sample.com?subject=Join Me!&body=Join me at Community Christian Church! <?=urlencode(base_url())?>" class="invite">Invite a Friend</a>
						<?php
							$files = $this->files_model->items($student_idea->id, 'big_idea_file');
						?>
						<?php if ($files->num_rows()): ?>
						<a href="#" class="download">Download Files</a>
						<ul class="downloads">
							<?php
								foreach ($files->result() as $file) {
									echo '<li><a href="/user_images/big_idea/'.$file->path.'">'.$file->path.'</a> <span class="size">'.(ceil((filesize('user_images/big_idea/'.$file) / 1024) * pow(10, 2)) / pow(10, 2)).'kb</span><br /></li>';
								}
							?>
						</ul>
						<?php endif; ?></div></div>
					<div class="videos" style="left: <?=$student_idea->videos_x?>px; top: <?=$student_idea->videos_y?>px">
					<h3 style="color: <?=$student_idea->text_color?>;">Recent Videos:</h3>
						<ul>
							<?php foreach($student_videos->result() as $video): ?>
								<li><a href="http://communitychristian.org/pages/administrator/components/com_threecvideo/video/<?=$video->file?>"><img src="http://communitychristian.org/pages/administrator/components/com_threecvideo/thumb/<?=$video->thumbnail?>" width="109" height="61" /><img src="<?=base_url()?>resources/images/big_idea_video_overlay.png" class="overlay" /></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>		
			<div class="big_idea <?=$kid_idea->category?>" style="border-bottom: 1px solid <?=$kid_idea->border_color?>; background: <?=$kid_idea->background_color?>;">
				<div class="container" style="background-image: url(<?=base_url()?>/resources/series/<?=$kid_idea->image?>)">
					<div class="service_info"><?=$kid_idea->series_title?></div>
					<div class="description" style="color: <?=$kid_idea->text_color?>; left:<?=$kid_idea->description_x;?>px; top:<?=$kid_idea->description_y;?>px"><?=$kid_idea->long_description;?><div class="invite_and_download"><a href="mailto:example@sample.com?subject=Join Me!&body=Join me at Community Christian Church! <?=urlencode(base_url())?>" class="invite">Invite a Friend</a>
						<?php
							$files = $this->files_model->items($kid_idea->id, 'big_idea_file');
						?>
						<?php if ($files->num_rows()): ?>
						<a href="#" class="download">Download Files</a>
						<ul class="downloads">
							<?php
								foreach ($files->result() as $file) {
									echo '<li><a href="/user_images/big_idea/'.$file->path.'">'.$file->path.'</a> <span class="size">'.(ceil((filesize('user_images/big_idea/'.$file) / 1024) * pow(10, 2)) / pow(10, 2)).'kb</span><br /></li>';
								}
							?>
						</ul>
						<?php endif; ?></div></div>
					<div class="videos" style="left: <?=$adult_idea->videos_x?>px; top: <?=$adult_idea->videos_y?>px">
					<h3 style="color: <?=$kid_idea->text_color?>;">Recent Videos:</h3>
						<ul>
							<?php foreach($kid_videos->result() as $video): ?>
								<li><a href="http://communitychristian.org/pages/administrator/components/com_threecvideo/video/<?=$video->file?>"><img src="http://communitychristian.org/pages/administrator/components/com_threecvideo/thumb/<?=$video->thumbnail?>" width="109" height="61" /><img src="<?=base_url()?>resources/images/big_idea_video_overlay.png" class="overlay" /></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<ul id="big_idea_toggler">
		<li class="title">The Big Idea:</li>
		<li><a id="show_adults" href="#">Adults</a></li>
		<li><a id="show_students" href="#">Students</a></li>
		<li><a id="show_kids" href="#">Kids</a></li>
		<li class="collapse"><a id="collapse_big_ideas">collapse</a></li>
	</ul>
	
	<div id="group_page" class="container">
		<?php $this->load->view('connect/group/stream.php'); ?>
		<div id="sidebar">
		<a href="/groups/news/p/308-news-2010_annual_report" border="0"><img src="/user_images/files/AnnualRepHomePage.jpg" alt="Annual Report" width="250" height="100" /></a>
			<?php if ($account === null): ?>
			<a href="/pages/aboutus" class="status_in_group join home_new">
		<strong>New? Start Here</strong>
	</a>
			<ul class="pages_navigation">
				<li><a href="/pages/aboutus/beliefs">What We Believe</a></li>
				<li><a href="/pages/aboutus/3cs">The 3Cs</a></li>
				<li><a href="/pages/aboutus/bigidea">The Big Idea</a></li>
				<li><a href="/pages/aboutus/stories">Stories of Life Change</a></li>
				<li><a href="<?=site_url('locations')?>">Service Times &amp; Locations</a></li>
				<li><a href="/pages/aboutus">Video FAQs</a></li>
				<li><a href="/pages/prayer">Prayer Requests</a></li>
				<li><a href="/contact">Contact Us</a></li>
			</ul>
			<?php else: ?>
			
			<ul class="pages_navigation">
				<li><a href="/groups/news">All-Church News</a></li>
				<li><a href="<?=site_url('locations')?>">Service Times &amp; Locations</a></li>
				<li><a href="/pages/aboutus/beliefs">About Us</a></li>
				<li><a href="/pages/prayer">Prayer Requests</a></li>
				<li><a href="/contact">Contact Us</a></li>
			</ul>
			
			<div class="sidebar_stream">
				<h2>Discussions &amp; Prayers</h2>
				<?php if ($sidebar_streams->num_rows()): ?>
				<ul>
					<?php foreach($sidebar_streams->result() as $stream): ?>
						<li class="<?=$stream->type?>">
							<a href="<?=site_url($this->groups_model->get_url($stream->group_id).'/p/'.$stream->id)?>" class="link"><?=($stream->type == 'prayer' ? 'Prayer' : $stream->subject)?></a>
							<span class="group">posted in <a href="<?=site_url($this->groups_model->get_url($stream->group_id))?>"><?=$this->groups_model->item($stream->group_id)->name?></a></span>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php else: ?>
					<p>There are no discussions or prayer requests in the groups you belong to yet. You could be the first to start a discussion or post a prayer request! Visit your group page and choose "Make a Post" to start interacting with other group members now.</p>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
		<br />
	</div>
</div>