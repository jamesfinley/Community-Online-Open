			<div class="table_view">
				<h2>Services &raquo; <span class="current_page">View Services</span></h2>
				<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
				<table id="service_list" cellspacing="0">
					<thead>
						<tr>
							<th>Big Idea</th>
							<th>Series Name</th>
							<th>Start Time</th>
							<th>Date</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($services->result() as $service): ?>
							<?php
								$service->time = $service->time / 60;
								$hours         = floor($service->time / 60);
								$minutes       = $service->time - (floor($service->time / 60) * 60);
								$service->time = $hours.($minutes < 10 ? '0' : '').$minutes;
								
								preg_match('/([0-9]?[0-9])([0-9]{2})/', $service->time, $matches);
								if ($matches[1] > 12) {
									$time = $matches[1].$matches[2].' PM';
								}
								else {
									$time = $matches[1].$matches[2].' AM';
								}
								$time = preg_replace('/([0-9]?[0-9])([0-9]{2})/', '$1:$2', $time);
							?>
							<tr>
								<td class="big_idea"><a href="<?=site_url('admin/service/'.$service->id)?>"><?=$service->big_idea?></a></td>
								<td class="series_title"><?=$service->series_title?></td>
								<td class="start_at"><?=date('h:i A', $service->service_time)?></td>
								<td class="start_on"><?=date('m/d/Y', $service->service_time)?></td>
								<td class="status"></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div id="sidebar">
				<h2>Navigate Services</h2>
				<ul>
					<li><a href="<?=site_url('admin')?>">Add Service</a></li>
					<li><a href="<?=site_url('admin/services')?>" class="selected">View Service List</a></li>
				</ul>
			</div>