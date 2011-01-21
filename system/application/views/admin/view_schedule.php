<div class="table_view">
	<h2><span class="current_page">Schedule</span></h2>
	<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
	<table id="schedule_list" cellspacing="0">
		<thead>
			<tr>
				<th>Service Time and Day</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($schedule->result() as $service): ?>
				<tr>
					<td class="day_of_week"><a href="<?=site_url('admin/schedule/edit/'.$service->id)?>"><?php
						$service->time = $service->time / 60;
						$hours         = floor($service->time / 60);
						$minutes       = $service->time - (floor($service->time / 60) * 60);
						$service->time = $hours.($minutes < 10 ? '0' : '').$minutes;
						
						preg_match('/([0-9]?[0-9])([0-9]{2})/', $service->time, $matches);
						if ($matches[1] > 11) {
							$time = ($matches[1] != 12 ? $matches[1] - 12 : $matches[1]).$matches[2].' PM';
						}
						else {
							$time = $matches[1].$matches[2].' AM';
						}
						
						$service->time = preg_replace('/([0-9]?[0-9])([0-9]{2})/', '$1:$2', $time);
						echo $service->time;
					?> on <?php
						switch ($service->day_of_week) {
							case 0:
								echo 'Sunday';
								break;
							case 1:
								echo 'Monday';
								break;
							case 2:
								echo 'Tuesday';
								break;
							case 3:
								echo 'Wednesday';
								break;
							case 4:
								echo 'Thursday';
								break;
							case 5:
								echo 'Friday';
								break;
							case 6:
								echo 'Saturday';
								break;
						}
					?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<div id="sidebar">
	<h2>Navigate Schedule</h2>
	<ul>
		<li><a href="<?=site_url('admin/schedule/add')?>">Add Service Time</a></li>
		<li><a href="<?=site_url('admin/schedule')?>" class="selected">View Schedule</a></li>
	</ul>
</div>