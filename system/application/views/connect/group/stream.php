<div id="stream">
	<?php if (($account === null || (isset($group->id) ? !$this->groups_model->belongs_to_group($group->id, $account->id) : false)) && isset($group) && ($group->type === 'small group' || $group->type === 'ministry') && $group->description): ?>
	<div id="group_info">
		<?=$group->description?>
	</div>
	<?php endif; ?>
	<!-- sticky post //-->
	<?php if ($sticky): ?>
		<?php
			$stream = $sticky;
			$streamURL = site_url($this->groups_model->get_url($stream->group_id).'/p/'.($stream->slug ? $stream->slug : $stream->id));
			if (base_url() == 'https://communitychristian.org/' || base_url() == 'http://communitychristian.org/') {
				if (!$stream->bitly)
				{
					$bitly = $this->bitly->shorten($streamURL);
					$this->db->where('id', $stream->id)->update('stream_posts', array(
						'bitly' => $bitly
					));
					$stream->bitly = $bitly;
				}
			}
		?>
		<?php if ($stream->type !== 'prayer' || ($stream->type === 'prayer' && isset($group) && $account !== null && $this->groups_model->belongs_to_group($group->id, $account->id))): ?>
		<div class="sticky_item">
		<div class="stream_item <?php echo $stream->type; ?>" id="post-<?=$stream->id?>" data-type="<?=$stream->type?>" data-id="<?=$stream->id?>" data-url="<?=$streamURL?>" data-bitly="<?=$stream->bitly?>">
			<div class="item">
				<?php
					if ( $stream->image )
					{
						$path = 'user_images/post_images/'.$stream->image;
						
						if ( file_exists($path) )
						{
							$display_width = 125;
						
							list($width, $height) = getimagesize($path);
								
							$display_height = round(($height * $display_width) / $width);	
													
							$image_url = site_url(array('images', 'rect', $display_width, $display_height, 'post_image', $stream->image));							
							echo '<img src="'.$image_url.'" class="image" alt="Post Image" />';
						}
					}
					else if ($stream->type == 'event'):?>
						<div class="calendar">
							<span class="month"><?=date('F', $stream->event_date)?></span> <span class="date"><?=date('d', $stream->event_date)?></span>
						</div>					
					<?php endif; ?>
				<?php if ( $stream->type != 'prayer' ): ?>
				<h3 class="post_title"><a href="<?=$streamURL?>"><?php echo $stream->subject; ?></a></h3>
				<?php endif; ?>			
				<?php
					$content = $stream->content;
					
					// Convert line breaks to brs
					$content = str_replace('<br />', '<br class="in_post" />', nl2br($content));
					
					// Remove Control Characters
					$content = remove_control_characters($content);
	
					// Auto link into anchors
					$content = auto_link($content, 'url');
	
					// Strip strong and em tags
					$content = strip_tags($content, '<strong><em><a><br>');
					
					// Includes ellipsis and read more link if content is truncated.
					$readMore = '&hellip; <a href="'.$streamURL.'">Read More</a>';
	
					// Truncates the html content down to 1000 characters					
					$content = html_truncate($content, 750, $readMore);
				?>
				<p><?=$content?></p>
				<?php
					$content_data = $stream->content;
								
					$content_data = find_url_and_return_data($content_data);
					if ($content_data !== null)
					{
						if ($content_data['type'] === 'youtube')
						{
							$embed_code = $content_data['data']['video']['embed'];
							preg_match('/width="([0-9]*)" height="([0-9]*)"/', $embed_code, $matches);
							$wh = $matches[0];
							$w = $matches[1];
							$h = $matches[2];
							if ($w > 480)
							{
								$h = $h * 480 / $w;
								$w = 480;
								$embed_code = str_replace($wh, 'width="'.$w.'" height="'.$h.'"', $embed_code);
							}
							echo '<div class="embedded_content video">'.$embed_code.'<div class="embedded_footer">'.$content_data['data']['video']['title'].'</div></div>';
						}
						elseif ($content_data['type'] === 'vimeo')
						{
							$embed_code = $content_data['data']['video']['embed'];
							preg_match('/width="([0-9]*)" height="([0-9]*)"/', $embed_code, $matches);
							$wh = $matches[0];
							$w = $matches[1];
							$h = $matches[2];
							if ($w > 480)
							{
								$h = $h * 480 / $w;
								$w = 480;
								$embed_code = str_replace($wh, 'width="'.$w.'" height="'.$h.'"', $embed_code);
							}
							echo '<div class="embedded_content video">'.$embed_code.'<div class="embedded_footer">'.$content_data['data']['video']['title'].'</div></div>';
						}
						elseif ($content_data['type'] === 'flickr')
						{
							if (isset($content_data['data']['photos']))
							{
								echo '<div class="embedded_content photos"><ul>';
								foreach($content_data['data']['photos'] as $photo)
								{
									echo '<li><a href="'.$photo['link'].'"><img src="'.$photo['square'].'" title="'.$photo['title'].'" /></a></li>';
								}
								echo '</ul><br /></div>';
								//print_r($content_data['data']['photos']);
							}
						}
					}
				?>
				<div class="footer" <?php echo $stream->type == 'event' ? 'style="background-image: url('.base_url().'resources/images/icon_events_'.date('j', $stream->event_date).'.png);"' : NULL;?>>
					<span class="post_info"><?php echo $stream->type; ?> posted by <span class="user"><?php echo $this->users->fullname($stream->user_id); ?></span> <a href="<?=$streamURL?>" class="date_link"><?php echo ago($stream->created_at); ?></a><?php if ($account !== null && isset($group) && $group->id === $stream->group_id && $this->groups_model->belongs_to_group($group->id, $account->id) === true && $stream->type == 'event' && $this->stream_posts_model->attending_event($stream->id, $account->id) === false): ?> &bull; <a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/attending')?>">I'm attending!</a><?php elseif ($account !== null && isset($group) && $group->id === $stream->group_id && $this->groups_model->belongs_to_group($group->id, $account->id) === true && $stream->type == 'event' && $this->stream_posts_model->attending_event($stream->id, $account->id) === true): ?> &bull; <a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/notattending')?>">I'm not gonna make it.</a><?php endif; ?></span>
					<span class="post_actions<?=($account !== null && (($stream->user_id === $account->id || $this->users->is_role($account->id, $group->id, 'facilitator'))) ? ' with_actions' : '')?>">
						<?php if($group && $group->is_public): ?><a href="#" class="share_post">Share</a><?php endif; ?>
						<?php if ($account !== null && $group): ?>
							<?php if($stream->user_id === $account->id || $this->users->is_role($account->id, $group->id, 'facilitator')): ?>
								<span class="stream_actions">
									<a href="#" class="actions">actions</a>
									<ul>
										<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/edit')?>" class="edit">Edit</a></li>
										<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/delete')?>" class="delete">Delete</a></li>
										<?php if($this->users->is_role($account->id, $group->id, 'facilitator') || $account->id == $stream->shared_by_id): ?>
											<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/unstick')?>" class="sticky">Remove Sticky</a></li>
										<?php endif; ?>
									</ul>
								</span>
							<?php endif; ?>
						<?php endif; ?>
					</span>
					<br />
				</div>
				<?php if (isset($group) && $group->id === $stream->group_id && $stream->type === 'event'): ?>
					<?php
						$whos_attending = $this->stream_posts_model->whos_attending($stream->id);
						$i = 0;
					?>
					<?php if ($whos_attending->num_rows()): ?>
					<div class="event_footer">
						<?php foreach($whos_attending->result() as $attender): ?><?php $i++; ?><? if($i !== 1 && $i < $whos_attending->num_rows()):?>,<?php endif; ?> <?php if($i > 1 && $i === $whos_attending->num_rows()): ?>and <?php endif; ?><span class="user"><?=$this->users->fullname($attender->user_id)?></span><?php endforeach; ?> <?php if($i > 1): ?>are<?php endif; ?><?php if($i === 1): ?>is<?php endif; ?> attending this event.
					</div>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (isset($stream->shared_by_id) && $stream->shared_by_id): ?>
					<?php
						$shared_to = $this->groups_model->item($stream->shared_group_id);
					?>
					<div class="shared_footer">This post was shared by <span class="user"><?=$this->users->fullname($stream->shared_by_id)?></span> <?=ago($stream->created_at)?><?=(!isset($group) ? ' on <a href="'.site_url($this->groups_model->get_url($shared_to->id)).'">'.$shared_to->name.'</a>' : '')?>.</div>
				<?php endif; ?>
			</div>
			<?php if ((isset($group) && $stream->reply_count > 0) || (isset($show_replies) && $show_replies === true && $stream->reply_count > 0)): ?>
			<div class="responses">
				<?php
					$replies = $this->stream_replies_model->items($stream->id, 5);
				?>
				<h4>Responses to <?php echo $stream->subject ? $stream->subject : 'prayer'; ?><?php if($stream->reply_count > count($replies)): ?> &mdash; <span class="show_more">showing <?=count($replies)?> of <?=$stream->reply_count?> (<a href="<?=$streamURL?>">see all responses</a>)</span><?php endif; ?></h4>
				<?php foreach($replies as $response):?>
				<div class="response">
					<p><?=nl2br(preg_replace("/http:\/\/[^\/]+[^\s]*/", "<a href='$0'>$0</a>", strip_tags($response->content, '<strong><em>')))?></p>
					<div class="footer">
						<span class="post_info"><span class="user"><?php echo $this->users->fullname($response->user_id); ?></span> replied <?php echo ago($response->created_at); ?></span>
						<span class="post_actions<?=($account !== null || ($account !== null && ($stream->user_id === $account->id || $this->users->is_role($account->id, $group->id, 'facilitator'))) ? ' with_actions' : '')?>">
							<?php if ($account !== null): ?>
								<?php if($response->user_id === $account->id || $this->users->is_role($account->id, $group->id, 'facilitator')): ?>
									<span class="stream_actions">
										<a href="#" class="actions">actions</a>
										<ul>
											<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/r/'.$response->id.'/delete')?>" class="delete">Delete</a></li>
										</ul>
									</span>
								<?php endif; ?>
							<?php endif; ?>
						</span>
					</div>
				</div>
				<?php endforeach; ?>
	
			</div>
			<?php endif; ?>
			<?php if ($account !== null && isset($group) && $group->id === $stream->group_id && $this->groups_model->belongs_to_group($group->id, $account->id) === true): ?>
			<div class="reply_box">
				<form action="" method="post">
					<input type="hidden" name="stream_post_id" value="<?=$stream->id?>" />
					<textarea name="content"></textarea><br />
					<input type="submit" value="Post" />
					<br />
				</form>
			</div>
			<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
	<?php endif; ?>
	<!-- end sticky post //-->
	<?php if (!isset($group) || ($account === null && isset($group) && $group->is_public) || ($account !== null && isset($group) && ($this->groups_model->belongs_to_group($group->id, $account->id) || $group->is_public))): ?>
		<?php if ($account !== null && isset($group) && $this->groups_model->belongs_to_group($group->id, $account->id)): ?>
		<!--<form action="<?=site_url('groups/'.$this->uri->segment(2).'/'.$this->uri->segment(3))?>" method="post" id="post_to_stream">
			<h2>Post to <strong><?=$group->name?></strong></h2>
			<input type="text" name="subject" placeholder="Subject" />
			<textarea name="content" cols="85" rows="5"></textarea>
			<select name="type">
				<? if ($this->users->is_facilitator($account->id, $group->id, true)): ?>
				<option value="news">News</option>
				<option value="event">Event</option>
				<option value="contribution">Contribution</option>
				<? endif; ?>
				<option value="discussion" selected="selected">Discussion</option>
				<option value="prayer">Prayer</option>
				<option value="qna">Q &amp; A</option>
			</select>
			<input type="submit" value="Post" />
		</form>//-->
		<?php if ($this->users->is_facilitator($account->id, $group->id, true) || (in_array('discussion', $types) || in_array('prayer', $types) || in_array('qna', $types))): ?>
		<form action="<?=site_url($this->groups_model->get_url($group->id))?>" method="post" id="post_to_stream" enctype="multipart/form-data">
			<h2>
				<span class="verb">Post</span>
				<select name="type">
					<?php if ($this->users->is_facilitator($account->id, $group->id, true)): ?>
					<?php if (in_array('news', $types)): ?><option value="news" data-verb="Post" data-preposition="to">News</option><?php endif; ?>
					<?php if (in_array('event', $types)): ?><option value="event" data-verb="Announce" data-preposition="to">Event</option><?php endif; ?>
					<?php if (in_array('contribution', $types)): ?><option value="contribution" data-verb="Invite to" data-preposition="with">Contribute</option><?php endif; ?>
					<?php endif; ?>
					<?php if (in_array('discussion', $types)): ?><option value="discussion" data-verb="Start" data-preposition="on">Discussion</option><?php endif; ?>
					<?php if (in_array('prayer', $types)): ?><option value="prayer" data-verb="Share" data-preposition="with">Prayer</option><?php endif; ?>
					<?php if (in_array('qna', $types)): ?><option value="qna" data-verb="Ask" data-preposition="on">Question</option><?php endif; ?>
				</select>
				<span class="preposition">to</span>
				<strong><?=$group->name?></strong>
			</h2>
			<div id="subject_set">
				<label for="subject_field">Subject</label>
				<input type="text" name="subject" id="subject_field" />
			</div>
			<textarea name="content"></textarea>
			<div id="file_set">
				<label for="file_field">Image</label>
				<input type="file" name="file" id="file_field" />
			</div>
			<input type="submit" value="Publish" />
			<br />
		</form>
		<?php endif; ?>
		<?php endif; ?>
		<?php if (count($streams) === 0): ?>
			<em>There are no posts yet for this group/ministry. Are you the leader of this group/ministry? If so, login to begin posting content.</em>
		<?php endif; ?>
		<?php foreach($streams as $stream):?>
		<?php
			$streamURL = site_url($this->groups_model->get_url($stream->group_id).'/p/'.($stream->slug ? $stream->slug : $stream->id));
			if (base_url() == 'https://communitychristian.org/' || base_url() == 'http://communitychristian.org/') {
				if (!$stream->bitly)
				{
					$bitly = $this->bitly->shorten($streamURL);
					$this->db->where('id', $stream->id)->update('stream_posts', array(
						'bitly' => $bitly
					));
					$stream->bitly = $bitly;
				}
			}
		?>
		<?php if ($stream->type !== 'prayer' || ($stream->type === 'prayer' && isset($group) && $account !== null && $this->groups_model->belongs_to_group($group->id, $account->id))): ?>
		<div class="stream_item <?php echo $stream->type; ?>" id="post-<?=$stream->id?>" data-type="<?=$stream->type?>" data-id="<?=$stream->id?>" data-url="<?=$streamURL?>" data-bitly="<?=$stream->bitly?>">
			<div class="item">
				<?php
					if ( $stream->image )
					{
						$path = 'user_images/post_images/'.$stream->image;
						
						if ( file_exists($path) )
						{
							$display_width = 125;
						
							list($width, $height) = getimagesize($path);
								
							$display_height = round(($height * $display_width) / $width);	
													
							$image_url = site_url(array('images', 'rect', $display_width, $display_height, 'post_image', $stream->image));							
							echo '<img src="'.$image_url.'" class="image" alt="Post Image" />';
						}
					}
					else if ($stream->type == 'event'):?>
						<div class="calendar">
							<span class="month"><?=date('F', $stream->event_date)?></span> <span class="date"><?=date('d', $stream->event_date)?></span>
						</div>					
					<?php endif; ?>
				<?php if ( $stream->type != 'prayer' ): ?>
				<h3 class="post_title"><a href="<?=$streamURL?>"><?php echo $stream->subject; ?></a></h3>
				<?php endif; ?>			
				<?php
					$content = $stream->content;
					
					// Convert line breaks to brs
					$content = str_replace('<br />', '<br class="in_post" />', nl2br($content));
					
					// Remove Control Characters
					$content = remove_control_characters($content);

					// Auto link into anchors
					$content = auto_link($content, 'url');

					// Strip strong and em tags
					$content = strip_tags($content, '<strong><em><a><br>');
					
					// Includes ellipsis and read more link if content is truncated.
					$readMore = '&hellip; <a href="'.$streamURL.'">Read More</a>';

					// Truncates the html content down to 1000 characters					
					$content = html_truncate($content, 750, $readMore);
				?>
				<p><?=$content?></p>
				<?php
					$content_data = $stream->content;
								
					$content_data = find_url_and_return_data($content_data);
					if ($content_data !== null)
					{
						if ($content_data['type'] === 'youtube')
						{
							$embed_code = $content_data['data']['video']['embed'];
							preg_match('/width="([0-9]*)" height="([0-9]*)"/', $embed_code, $matches);
							$wh = $matches[0];
							$w = $matches[1];
							$h = $matches[2];
							if ($w > 480)
							{
								$h = $h * 480 / $w;
								$w = 480;
								$embed_code = str_replace($wh, 'width="'.$w.'" height="'.$h.'"', $embed_code);
							}
							echo '<div class="embedded_content video">'.$embed_code.'<div class="embedded_footer">'.$content_data['data']['video']['title'].'</div></div>';
						}
						elseif ($content_data['type'] === 'vimeo')
						{
							$embed_code = $content_data['data']['video']['embed'];
							preg_match('/width="([0-9]*)" height="([0-9]*)"/', $embed_code, $matches);
							$wh = $matches[0];
							$w = $matches[1];
							$h = $matches[2];
							if ($w > 480)
							{
								$h = $h * 480 / $w;
								$w = 480;
								$embed_code = str_replace($wh, 'width="'.$w.'" height="'.$h.'"', $embed_code);
							}
							echo '<div class="embedded_content video">'.$embed_code.'<div class="embedded_footer">'.$content_data['data']['video']['title'].'</div></div>';
						}
						elseif ($content_data['type'] === 'flickr')
						{
							if (isset($content_data['data']['photos']))
							{
								echo '<div class="embedded_content photos"><ul>';
								foreach($content_data['data']['photos'] as $photo)
								{
									echo '<li><a href="'.$photo['link'].'"><img src="'.$photo['square'].'" title="'.$photo['title'].'" /></a></li>';
								}
								echo '</ul><br /></div>';
								//print_r($content_data['data']['photos']);
							}
						}
					}
				?>
				<div class="footer" <?php echo $stream->type == 'event' ? 'style="background-image: url('.base_url().'resources/images/icon_events_'.date('j', $stream->event_date).'.png);"' : NULL;?>>
					<span class="post_info"><?php echo $stream->type; ?> posted by <span class="user"><?php echo $this->users->fullname($stream->user_id); ?></span><?php if (!isset($group) || $group->id !== $stream->group_id): ?> on <a href="<?=site_url($this->groups_model->get_url($stream->group_id))?>"><?=$this->groups_model->item($stream->group_id)->name?></a><?php endif; ?> <a href="<?=$streamURL?>" class="date_link"><?php echo ago(isset($stream->originally_posted_at) && $stream->originally_posted_at ? $stream->originally_posted_at : $stream->created_at); ?></a><?php if ($account !== null && isset($group) && $group->id === $stream->group_id && $this->groups_model->belongs_to_group($group->id, $account->id) === true && $stream->type == 'event' && $this->stream_posts_model->attending_event($stream->id, $account->id) === false): ?> &bull; <a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/attending')?>">I'm attending!</a><?php elseif ($account !== null && isset($group) && $group->id === $stream->group_id && $this->groups_model->belongs_to_group($group->id, $account->id) === true && $stream->type == 'event' && $this->stream_posts_model->attending_event($stream->id, $account->id) === true): ?> &bull; <a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/notattending')?>">I'm not gonna make it.</a><?php endif; ?></span>
					<span class="post_actions<?=($account !== null && (($stream->user_id === $account->id || $this->users->is_role($account->id, $group->id, 'facilitator'))) ? ' with_actions' : '')?>">
						<?php if($group && $group->is_public && $stream->group_id == $group->id): ?><a href="#" class="share_post">Share</a><?php endif; ?>
						<?php if ($account !== null && $group && $stream->group_id == $group->id): ?>
							<?php if($stream->user_id === $account->id || $this->users->is_role($account->id, $group->id, 'facilitator')): ?>
								<span class="stream_actions">
									<a href="#" class="actions">actions</a>
									<ul>
										<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/edit')?>" class="edit">Edit</a></li>
										<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/delete')?>" class="delete">Delete</a></li>
										<?php if($this->users->is_role($account->id, $group->id, 'facilitator')): ?>
											<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/stick')?>" class="sticky">Make Sticky</a></li>
										<?php endif; ?>
									</ul>
								</span>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ($stream->group_id != $group->id && $this->users->is_role($account->id, $group->id, 'facilitator')): ?>
							<span class="stream_actions">
								<a href="#" class="actions">actions</a>
								<ul>
									<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/s/'.($stream->slug ? $stream->slug : $stream->id).'/remove')?>" class="delete">Remove Shared Item</a></li>
								</ul>
							</span>
						<?php endif; ?>
					</span>
					<br />
				</div>
				<?php if (isset($group) && $group->id === $stream->group_id && $stream->type === 'event'): ?>
					<?php
						$whos_attending = $this->stream_posts_model->whos_attending($stream->id);
						$i = 0;
					?>
					<?php if ($whos_attending->num_rows()): ?>
					<div class="event_footer">
						<?php foreach($whos_attending->result() as $attender): ?><?php $i++; ?><? if($i !== 1 && $i < $whos_attending->num_rows()):?>,<?php endif; ?> <?php if($i > 1 && $i === $whos_attending->num_rows()): ?>and <?php endif; ?><span class="user"><?=$this->users->fullname($attender->user_id)?></span><?php endforeach; ?> <?php if($i > 1): ?>are<?php endif; ?><?php if($i === 1): ?>is<?php endif; ?> attending this event.
					</div>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (isset($stream->shared_by_id) && $stream->shared_by_id): ?>
					<?php
						$shared_to = $this->groups_model->item($stream->shared_group_id);
					?>
					<div class="shared_footer">This post was shared by <span class="user"><?=$this->users->fullname($stream->shared_by_id)?></span> <?=ago($stream->created_at)?><?=(!isset($group) ? ' on <a href="'.site_url($this->groups_model->get_url($shared_to->id)).'">'.$shared_to->name.'</a>' : '')?>.</div>
				<?php endif; ?>
			</div>
			<?php if ((isset($group) && $group->id === $stream->group_id && $stream->reply_count > 0) || (isset($show_replies) && $show_replies === true && $stream->reply_count > 0)): ?>
			<div class="responses">
				<?php
					$replies = $this->stream_replies_model->items($stream->id, 5);
				?>
				<h4>Responses to <?php echo $stream->subject ? $stream->subject : 'prayer'; ?><?php if($stream->reply_count > count($replies)): ?> &mdash; <span class="show_more">showing <?=count($replies)?> of <?=$stream->reply_count?> (<a href="<?=$streamURL?>">see all responses</a>)</span><?php endif; ?></h4>
				<?php foreach($replies as $response):?>
				<div class="response">
					<p><?=nl2br(preg_replace("/http:\/\/[^\/]+[^\s]*/", "<a href='$0'>$0</a>", strip_tags($response->content, '<strong><em>')))?></p>
					<div class="footer">
						<span class="post_info"><span class="user"><?php echo $this->users->fullname($response->user_id); ?></span> replied <?php echo ago($response->created_at); ?></span>
						<span class="post_actions<?=($account !== null || ($account !== null && ($stream->user_id === $account->id || $this->users->is_role($account->id, $group->id, 'facilitator'))) ? ' with_actions' : '')?>">
							<?php if ($account !== null): ?>
								<?php if($response->user_id === $account->id || $this->users->is_role($account->id, $group->id, 'facilitator')): ?>
									<span class="stream_actions">
										<a href="#" class="actions">actions</a>
										<ul>
											<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/r/'.$response->id.'/delete')?>" class="delete">Delete</a></li>
										</ul>
									</span>
								<?php endif; ?>
							<?php endif; ?>
						</span>
					</div>
				</div>
				<?php endforeach; ?>
	
			</div>
			<?php endif; ?>
			<?php if ($account !== null && isset($group) && $group->id === $stream->group_id && $this->groups_model->belongs_to_group($group->id, $account->id) === true): ?>
			<div class="reply_box">
				<form action="" method="post">
					<input type="hidden" name="stream_post_id" value="<?=$stream->id?>" />
					<textarea name="content"></textarea><br />
					<input type="submit" value="Post" />
					<br />
				</form>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php endforeach; ?>
		<?php if (isset($pages_count) && $pages_count > 1): ?>
		<div id="pagination">
			<ul>
				<?php for ($i=0; $i<$pages_count; $i++): ?>
					<li><a href="<?=site_url($this->groups_model->get_url($group->id).'?page='.($i+1))?>"><?=($i+1)?></a></li>
				<?php endfor; ?>
			</ul>
		</div>
		<?php endif; ?>
	<?php else: ?>
		<em>You must be a member of this group to see the content on the page. Feel free to join the group by clicking "Join" to the right.</em>
	<?php endif; ?>
</div>