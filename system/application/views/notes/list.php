<div id="notes">
	<h2><?php if ($note): ?><?=$note->big_idea?> (<?=$note->series_title?>)<a href="javascript:window.print()" id="print_notes">Print</a><?php else:?><em>no notes</em><?php endif; ?></h2>
	<div id="notebook">
		<?php if ($note): ?>
		<ul id="notelist">
			<?php foreach ($notes->result() as $n): ?>
			<li><a href="<?=site_url('notes/'.$n->id)?>" class="item <?=$n->id === $note->id ? 'selected"' : ''?>"><span class="big_idea"><?=$n->big_idea?></span> <span class="series_title">(<?=$n->series_title?>)</span></a> <span class="date"><?=date('m/d h:i a', $n->updated_at)?></span><a href="<?=site_url('notes/'.$n->id.'/delete')?>" class="delete">delete</a></li>
			<?php endforeach; ?>
		</ul>
		<div id="note">
			<textarea><?=$note->content?></textarea>
		</div>
		<?php else: ?>
		<div id="nonotes">
			Notes are created during a service.
		</div>
		<?php endif; ?>
		<br />
	</div>
	<br />
</div>