			<div class="table_view">
				<h2>Big Idea &raquo; <span class="current_page">View Big Ideas</span></h2>
				<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
				<table id="service_list" cellspacing="0">
					<thead>
						<tr>
							<th>Name</th>
							<th>Series Name</th>
							<th>Start Date</th>
							<th>End Date</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($big_ideas->result() as $big_idea): ?>
							<tr>
								<td class="big_idea"><a href="<?=site_url('admin/big_idea/edit/'.$big_idea->id)?>"><?=$big_idea->series_title?></a></td>
								<td class="series_title"><?=$big_idea->series_title?></td>
								<td class="begin_at"><?=date('m/d/Y', $big_idea->begin_at)?></td>
								<td class="end_at"><?=date('m/d/Y', $big_idea->end_at)?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div id="sidebar">
				<h2>Navigate Services</h2>
				<ul>
					<li><a href="<?=site_url('admin/big_idea/add')?>">Add Big Idea</a></li>
					<li><a href="<?=site_url('admin/big_idea')?>" class="selected">View Big Idea List</a></li>
				</ul>
			</div>