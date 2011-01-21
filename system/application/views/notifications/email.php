<html>
	<head>
		<title></title>
	</head>
	<body style="padding: 0; margin: 0;">
		<?php
			$notification->message = unserialize($notification->message);
			$group                 = $this->groups_model->item($notification->group_id);
			$group_url             = site_url($this->groups_model->get_url($group->id));
		?>
		<div style="background: #173867; padding: 5px 10px; text-align: center;"><img src="<?=base_url()?>resources/images/logo.png" style="max-width: 100%;" /></div>
		<div style="background: #eee; padding: 10px;">
			<div style="background: #fff; padding: 10px; border: 1px solid #C3C3C3; -webkit-border-radius: 3px; -webkit-box-shadow: 0 2px 5px rgba(0, 0, 0, .15); font: 14px/20px Helvetica Neue, Helvetica, Arial, sans-serif;">
				<?php if ($notification->message['subject']): ?>
					<h2 style="padding: 0; margin: 0;"><a href="<?=$notification->message['link']?>" style="color: #1282C7"><?=$notification->message['subject']?></a></h2>
				<?php endif; ?>
				<?php if (isset($notification->message['content']) && $notification->message['content']): ?>
					<p><?=nl2br($notification->message['content'])?></p>
					<?php if ($notification->type == 'register' || (isset($notification->message['special']) && $notification->message['special'] === 'reply')): ?>
					<?php else: ?>
						<?php if (isset($notification->message['posted_by'])): ?>
						<div style="color: #AAA; font-size: 12px;"><?=$notification->type?> posted by <strong style="color: #666;"><?=$notification->message['posted_by']?></strong> on <a href="<?=$group_url?>" style="color: #1282C7"><?=$group->name?></a> at <?=date('h:i a \o\n F d', $notification->message['created_at'])?></div>
						<?php else: ?>
						<div style="color: #AAA; font-size: 12px;"><?=$notification->type?> shared by <strong style="color: #666;"><?=$notification->message['shared_by']?></strong> with <a href="<?=$group_url?>" style="color: #1282C7"><?=$group->name?></a> at <?=date('h:i a \o\n F d', $notification->message['created_at'])?></div>
						<?php endif; ?>
					<?php endif; ?>
				<?php else: ?>
					<p><?=$notification->short_message?></p>
				<?php endif; ?>
			</div>
			<div style="margin-top: 10px; color: #AAA; font: 12px/20px Helvetica Neue, Helvetica, Arial, sans-serif; text-align: center;">
				Hey, if you don't want to receive these emails or don't wanna receive as many, go to your group settings for any group on Community Online and turn them off.
			</div>
		</div>
	</body>
</html>
