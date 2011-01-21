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
		<div class="stream_item edit <?php echo $stream->type; ?>" id="post-<?=$stream->id?>">
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
				<form method="post" action="">
					<?php if ( $stream->type != 'prayer' ): ?>
					<h3>Subject: <input type="text" name="subject" value="<?php echo $stream->subject; ?>" /></h3>
					<?php endif; ?>
					<textarea name="content"><?=$stream->content?></textarea><br />
					<input type="submit" value="Save Post" />
				</form>
				<div class="footer" <?php echo $stream->type == 'event' ? 'style="background-image: url('.base_url().'resources/images/icon_events_'.date('j', $stream->event_date).'.png);"' : NULL;?>>
					<?php echo $stream->type; ?> posted by <span class="user"><?php echo $this->users->fullname($stream->user_id); ?></span> <?php echo ago($stream->created_at); ?><?php if ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id) === true && $stream->type == 'event' && $this->stream_posts_model->attending_event($stream->id, $account->id) === false): ?> &bull; <a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.$stream->id.'/attending')?>">I'm attending!</a><?php elseif ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id) === true && $stream->type == 'event' && $this->stream_posts_model->attending_event($stream->id, $account->id) === true): ?> &bull; <a href="<?=site_url($this->groups_model->get_url($group->id).'/p/'.$stream->id.'/notattending')?>">I'm not gonna make it.</a><?php endif; ?>
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
		</div>
	</div>
	<br />
</div>

<script type="text/javascript">
	$(function () {
		connect.display_images();
	});
</script>