<rss version="2.0">
<channel>
	<title><?=$title ? $title : $group->name?></title>
	<link><?=$link ? $link : site_url($this->groups_model->get_url($group->id))?></link>
	<description><?=$description ? $description : $group->description?></description>
	
<?php foreach($streams->result() as $stream): ?>
<item>
	<title><?=$stream->type === 'prayer' ? 'Prayer from '.$this->users->fullname($stream->user_id) : $stream->subject?></title>
	<guid><?=site_url($this->groups_model->get_url($stream->group_id).'/p/'.$stream->id)?></guid>
	<link><?=site_url($this->groups_model->get_url($stream->group_id).'/p/'.$stream->id)?></link>
	<description><?=htmlentities($stream->content)?></description>
	<pubDate><?=date(DATE_RSS, $stream->created_at)?></pubDate>
</item>
<?php endforeach; ?>
</channel>