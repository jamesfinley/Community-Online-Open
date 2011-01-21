<html>
	<head>
		<title></title>
	</head>
	<body style="padding: 0; margin: 0;">
		<div style="background: #173867; padding: 5px 10px; text-align: center;"><img src="<?=base_url()?>resources/images/logo.png" style="max-width: 100%;" /></div>
		<div style="background: #eee; padding: 10px;">
			<div style="background: #fff; padding: 10px; border: 1px solid #C3C3C3; -webkit-border-radius: 3px; -webkit-box-shadow: 0 2px 5px rgba(0, 0, 0, .15); font: 14px/20px Helvetica Neue, Helvetica, Arial, sans-serif;">
				<h2 style="padding: 0; margin: 0;">Your Daily Digest for <a href="<?=site_url($this->groups_model->get_url($group->id))?>" style="color: #1282C7"><?=$group->name?></a></h2>
				<ul style="list-style: none;">
					<?php foreach ($notifications->result() as $notification): ?>
						<?php
							$notification->message = unserialize($notification->message);
							$group                 = $this->groups_model->item($notification->group_id);
							$group_url             = site_url($this->groups_model->get_url($group->id));
						?>
						<li style="margin-bottom: 10px;">
							<h2 style="padding: 0; margin: 0; font-size: 18px;"><a href="<?=$notification->message['link']?>" style="color: #1282C7"><?=$notification->subject?></a></h2>
							<div><?=$notification->short_message?></div>
							<div style="color: #AAA; font-size: 12px;"><?=date('h:i a \o\n F d', $notification->message['created_at'])?></div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div style="margin-top: 10px; color: #AAA; font: 12px/20px Helvetica Neue, Helvetica, Arial, sans-serif; text-align: center;">
				Hey, if you don't want to receive these emails or don't wanna receive as many, go to your group settings for any group on Community Online and turn them off.
			</div>
		</div>
	</body>
</html>
