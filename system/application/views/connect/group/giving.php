<?php
	$agent = $this->agent->agent_string();
	$is_mobile = false;
	if (preg_match('/AppleWebKit/', $agent))
	{
		//is webOS
		$is_webos   = preg_match('/webOS/', $agent) > 0 ? true : false;
		
		//is Android
		$is_android = preg_match('/Android/', $agent) > 0 ? true : false;
		
		//is Mobile
		$is_mobile  = preg_match('/Mobile/', $agent) > 0 ? true : false;
		
		if ($is_mobile || $is_android || $is_webos)
		{
			$is_mobile = true;
		}
	}
?>
<?php
	$page->content = unserialize($page->content);
?>
<div id="group_page" class="container static_page" rel="<?=$group->latitude?>,<?=$group->longitude?>">
	<div id="group_header">
		<h2><?=character_limiter($group->name, 45)?></h2>
		<a href="<?=site_url($this->groups_model->get_url($group->id))?>" id="">back to group</a>
		<ul id="filter_stream">
		</ul>
	</div>
	<div id="page">
		<form id="giveback" class="on_page">
			<h2><?=$page->title?></h2>
			<span class="error" style="display: none;">all fields are required</span>
			<fieldset>
				<div class="label_set firstName">
					<label for="giveback_firstName">first name</label>
					<input type="text" id="giveback_firstName" value="<?=$account ? $account->first_name : ''?>" />
				</div>
				<div class="label_set lastName">
					<label for="giveback_lastName">last name</label>
					<input type="text" id="giveback_lastName" value="<?=$account ? $account->last_name : ''?>" />
				</div>
				<div class="label_set email<?php if ($page->content['campus_field'] == 0): ?> no_campus_field<?php endif; ?>">
					<label for="giveback_email">email</label>
					<input type="<?=$is_mobile ? 'email' : 'text'?>" id="giveback_email" value="<?=$account ? $account->email : ''?>" />
				</div>
				<?php if ($page->content['campus_field']): ?>
				<div class="label_set campus">
					<label for="giveback_campus">campus</label>
					<?php
						$campuses = $this->groups_model->items(0, 'campus');
					?>
					<select id="giveback_campus">
						<option value="">Choose a campus.</option>
						<option value="0">Community Online</option>
						<?php foreach ($campuses->result() as $campus): ?>
							<option value="<?=$campus->id?>"><?=$campus->name?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<?php endif; ?>
			</fieldset>
			<fieldset>
				<div class="segmented">
					<a href="#" class="tab selected">credit</a> <a href="#" class="tab">echeck</a>
				</div>
			</fieldset>
			<div id="giveback_payment">
				<fieldset id="giveback_credit_payment" style="left: 9px; ">
					<div class="label_set cc">
						<label for="giveback_cc">card number</label>
						<input type="<?=$is_mobile ? 'number' : 'text'?>" autocomplete="off" id="giveback_cc" placeholder="1234567890123456" />
					</div>
					<div class="label_set code">
						<label for="giveback_code">CVS</label>
						<input type="<?=$is_mobile ? 'number' : 'text'?>" placeholder="123" autocomplete="off" id="giveback_code" />
					</div>
					<div class="label_set exp">
						<label for="giveback_exp">exp</label>
						<input type="<?=$is_mobile ? 'number' : 'text'?>" id="giveback_exp" placeholder="00/00" />
					</div>
					<div class="label_set zip">
						<label for="giveback_zip">zip code</label>
						<input type="<?=$is_mobile ? 'number' : 'text'?>" id="giveback_zip" placeholder="60540" />
					</div>
				</fieldset>
				<fieldset id="giveback_echeck_payment" style="display: none; left: 328px;">
					<div class="label_set routing">
						<label for="giveback_routing">routing number</label>
						<input autocomplete="off" type="<?=$is_mobile ? 'number' : 'text'?>" id="giveback_routing" value="" />
					</div>
					<div class="label_set account">
						<label for="giveback_account">account number</label>
						<input autocomplete="off" type="<?=$is_mobile ? 'number' : 'text'?>" id="giveback_account" value="" />
					</div>
					<div class="label_set bank">
						<label for="giveback_bank">bank name</label>
						<input type="text" id="giveback_bank" value="" />
					</div>
					<div class="label_set account_type">
						<label for="giveback_account_type">account type</label>
						<select id="giveback_account_type">
							<option value="checking">checking</option>
							<option value="savings">savings</option>
						</select>
					</div>
				</fieldset>
			</div>
			<fieldset>
				<div class="label_set amount">
					<label for="giveback_amount">amount</label>
					<input type="<?=$is_mobile ? 'number' : 'text'?>" id="giveback_amount" placeholder="$0.00" />
				</div>
			</fieldset>
			<fieldset>
				<div class="label_set comments">
					<label for="giveback_comments">comments</label>
					<input type="text" id="giveback_comments" />
				</div>
				<input type="hidden" id="giveback_campus" value="<?=$group->id?>" />
				<input type="hidden" id="giveback_page_id" value="<?=$page->id?>" />
				<?php if ($page->content['fields']): ?>
					<?php $i=0; ?>
					<?php foreach ($page->content['fields'] as $field): ?>
						<?php 
							switch ($field['type']) { 
								case 'select': ?>
								<div class="label_set custom">
									<label><?=$field['name']?></label>
									<select name="custom_field[<?=$i?>]">
										<option value="">please choose one</option>
										<?php foreach (explode(', ', $field['value']) as $value): ?>
											<option value="<?=$value?>"><?=$value?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<?php
								break;
								case 'radios': ?>
								<div class="label_set custom">
									<label><?=$field['name']?></label>
									<?php foreach (explode(', ', $field['value']) as $value): ?>
										<input type="radio" name="custom_field[<?=$i?>]" value="<?=$value?>" /> <?=$value?><br />
									<?php endforeach; ?>
								</div>
								<?php
								break;
							} 
							?>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</fieldset>
			<fieldset>
				<input type="checkbox" id="giveback_recurring_field" style="display: none;">
				<div class="toggleset">
					<div id="recurring_toggler" rel="giveback_recurring_field" class="toggler">
						<div class="bounce">
							<div class="slide"></div>
						</div>
					</div>
					<label rel="recurring_toggler">Recurring?</label>
				</div>
				<div class="hide_these" style="display: none;">
					<div class="label_set recurring_frequency">
						<label for="giveback_recurring_frequency">frequency</label>
						<select id="giveback_recurring_frequency">
							<option value="weekly">weekly</option>
							<option value="monthly">monthly</option>
							<option value="quarterly">quarterly</option>
							<option value="semi-annually">semi-annually</option>
							<option value="annually">annually</option>
						</select>
					</div>
					<div class="label_set recurring_date">
						<label for="giveback_recurring_date">start date</label>
						<input type="text" id="giveback_recurring_date" placeholder="00/00/0000" />
					</div>
					<div class="label_set recurring_count">
						<label for="giveback_recurring_count"># of gifts</label>
						<input type="<?=$is_mobile ? 'number' : 'text'?>" id="giveback_recurring_count" />
					</div>
					<br>
				</div>
			</fieldset>
			<input type="button" value="Cancel" /><input type="submit" value="Process" />
		</form>
	</div>
	<?php $this->load->view('connect/group/sidebar'); ?>
	<br />
</div>

<script type="text/javascript">
	$(function () {
		connect.display_images();
	});
</script>