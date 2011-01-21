<script>
	var groups = new Array();
<?php
	if ($groups_for_user)
	{
		foreach ($groups_for_user->result() as $group_for_user)
		{
			$is_facilitator = has_role('facilitator', $account->id, $group_for_user->id);
			echo 'groups.push({id:'.$group_for_user->id.', type: \''.($group_for_user->type == 'small group' ? 'small_group' : $group_for_user->type).'\', name:\''.addslashes($group_for_user->name).'\''.($group_for_user->id === $group->id ? ', open: true' : ', open: false').', is_facilitator: '.($is_facilitator ? 'true' : 'false').'});'."\n";
		}
	}
?>
</script>
<div id="group_page" class="container post_page" rel="<?=$group->latitude?>,<?=$group->longitude?>">
	<?php
		$group_type = 'Church';
		switch ($group->type)
		{
			case 'campus':
				$group_type = 'Church Campus';
				break;
			case 'small group':
				$group_type = 'Small Group';
				break;
			case 'ministry':
				$group_type = 'Ministry';
				break;
		}
	?>
	<div id="group_header">
		<a href="<?=site_url($this->groups_model->get_url($group->id))?>">&laquo; Back to <?=$group->name?></a>
		<!--<form id="search_stream" action="" method="post">
			<input type="search" placeholder="Search this <?=$group_type?>" />
		</form>//-->
		<br />
	</div>
	<div id="stream">
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
				<h3 class="post_title"><?php echo $stream->subject; ?></h3>
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
				?>
				<p><?=$content?></p>
				<?php
					$content_data = find_url_and_return_data($stream->content);
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
					<span class="post_info"><?php echo $stream->type; ?> posted by <span class="user"><?php echo $this->users->fullname($stream->user_id); ?></span> <?php echo ago($stream->created_at); ?><?php if ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id) === true && $stream->type == 'event' && $this->stream_posts_model->attending_event($stream->id, $account->id) === false): ?> &bull; <a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.$stream->id.'/attending')?>">I'm attending!</a><?php elseif ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id) === true && $stream->type == 'event' && $this->stream_posts_model->attending_event($stream->id, $account->id) === true): ?> &bull; <a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.$stream->id.'/notattending')?>">I'm not gonna make it.</a><?php endif; ?></span>
					<span class="post_actions<?=($account !== null || ($account !== null && ($stream->user_id === $account->id || $this->users->is_role($account->id, $group->id, 'facilitator'))) ? ' with_actions' : '')?>">
						<?php if($group->is_public): ?><a href="#" class="share_post">Share</a><?php endif; ?>
						<?php if ($account !== null): ?>
							<?php if($stream->user_id === $account->id || $this->users->is_role($account->id, $group->id, 'facilitator')): ?>
								<span class="stream_actions">
									<a href="#" class="actions">actions</a>
									<ul>
										<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/edit')?>" class="edit">Edit</a></li>
										<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id).'/delete')?>" class="delete">Delete</a></li>
									</ul>
								</span>
							<?php endif; ?>
						<?php endif; ?>
					</span>
					<br />
				</div>
				<?php if ($stream->type === 'event'): ?>
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
			</div>
			<?php if ($stream->reply_count > 0): ?>
			<div class="responses">
				<h4>Responses to <?php echo $stream->subject ? $stream->subject : 'prayer'; ?></h4>
				<?php foreach($this->stream_replies_model->items($stream->id, 500) as $response):?>
				<div class="response">
					<p><?=nl2br(preg_replace("/http:\/\/[^\/]+[^\s]*/", "<a href='$0'>$0</a>", strip_tags($response->content, '<strong><em>')))?></p>
					<div class="footer">
						<span class="user"><?php echo $this->users->fullname($response->user_id); ?></span> replied <?php echo ago($response->created_at); ?>
					</div>
				</div>
				<?php endforeach; ?>
	
			</div>
			<?php endif; ?>
			<?php if ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id) === true): ?>
			<div class="reply_box">
				<form action="" method="post">
					<input type="hidden" name="stream_post_id" value="<?=$stream->id?>" />
					<textarea name="content"></textarea><br />
					<input type="submit" value="Post" />
				</form>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<!--<div id="sidebar">
		<?php if ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id) === false): ?>
		<div class="status_in_group join">
			You are not a member of this <?=$group->type !== 'master' ? ($group->type !== 'small group' ? $group->type : 'group') : 'church'?>.<br />
			<strong>Do you wish to <a href="<?=site_url('connect/'.$this->uri->segment(2).'/'.$group->slug.'/join')?>">join</a>?</strong>
		</div>
		<?php endif; ?>
		<?php if ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id)): ?>
		<div class="status_in_group">
			Welcome back, <?=$this->users->fullname($account->id)?>!
		</div>
		<?php endif; ?>
		<?php
			/*if ($group->type === 'campus')
			{
				echo '<div class="belongs_to">This Campus belongs to <strong><a href="'.site_url('connect/church/'.$church->slug).'">'.$church->name.'</a></strong></div>';
			}*/
			if ($campuses !== null)
			{
				echo '<div class="group_list"><h2>';
				if ($group->type === 'master')
				{
					echo 'Campuses at <strong>'.$group->name.'</strong>';
				}
				else
				{
					echo 'Other Campuses at <strong>'.$church->name.'</strong>';
				}
				?></h2>
				<ul><?php
				foreach ($campuses->result() as $campus)
				{
					?><li><a href="<?=site_url('connect/campus/'.$campus->slug)?>"><?=$campus->name?></a></li><?php
				}
				?></ul></div><?php
			}
			if ($small_groups !== null)
			{
				echo '<div class="group_list">';
				?><h2>Small Groups at <strong><?=$group->name?></strong></h2>
				<ul><?php
				foreach ($small_groups->result() as $small_group)
				{
					?><li><a href="<?=site_url('connect/small_group/'.$small_group->slug)?>"><?=$small_group->name?></a></li><?php
				}
				?></ul></div><?php
			}
			if ($group->type === 'small group')
			{
				echo '<div class="belongs_to">This Small Group belongs to <strong><a href="'.site_url('connect/campus/'.$campus->slug).'">'.$campus->name.'</a></strong></div>';
			}
		?>
	</div>//-->
	<br />
	<?php if ($stream->bitly): ?>
	<div class="shortURL">
		<span class="title">short url for this page:</span><br />
		<?=$stream->bitly?>
	</div>
	<?php endif; ?>
</div>

<script type="text/javascript">
	$(function () {
		connect.display_images();
	});
</script>