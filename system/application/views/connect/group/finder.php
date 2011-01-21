<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<form id="small_group_finder_container">
	<h2>Find a Small Group</h2>
	<div id="small_group_finder_filters_results">
		<div id="small_group_finder_filters">
			<div class="small_group_finder_filter">
				<label for="small_group_finder_field_campus">Campus</label>
				<select name="small_group_finder_campus" id="small_group_finder_field_campus">
					<option value="">any campus</option>
					<option value="2"<?=($_GET && isset($_GET['campus']) && $_GET['campus'] == 2 ? ' selected="selected"' : '')?>>Naperville Yellow Box</option>
					<option value="4"<?=($_GET && isset($_GET['campus']) && $_GET['campus'] == 4 ? ' selected="selected"' : '')?>>Naperville Downtown</option>
					<option value="6"<?=($_GET && isset($_GET['campus']) && $_GET['campus'] == 6 ? ' selected="selected"' : '')?>>East Aurora</option>
					<option value="7"<?=($_GET && isset($_GET['campus']) && $_GET['campus'] == 7 ? ' selected="selected"' : '')?>>Romeoville</option>
					<option value="8"<?=($_GET && isset($_GET['campus']) && $_GET['campus'] == 8 ? ' selected="selected"' : '')?>>Shorewood</option>
					<option value="9"<?=($_GET && isset($_GET['campus']) && $_GET['campus'] == 9 ? ' selected="selected"' : '')?>>Montgomery</option>
					<option value="10"<?=($_GET && isset($_GET['campus']) && $_GET['campus'] == 10 ? ' selected="selected"' : '')?>>Carillon (Plainfield)</option>
					<option value="11"<?=($_GET && isset($_GET['campus']) && $_GET['campus'] == 11 ? ' selected="selected"' : '')?>>Yorkville</option>
					<option value="13"<?=($_GET && isset($_GET['campus']) && $_GET['campus'] == 13 ? ' selected="selected"' : '')?>>Plainfield</option>
				</select>
			</div>
			<div class="small_group_finder_filter">
				<label for="small_group_finder_field_city">City</label>
				<?php
					$cities = $this->db->where('type', 'small group')->select('city')->distinct()->order_by('city')->get('groups');
				?>
				<select name="small_group_finder_city" id="small_group_finder_field_city">
					<option value="">any city</option>
					<?php foreach ($cities->result() as $city): ?>
						<?php if ($city->city): ?>
						<option value="<?=$city->city?>"><?=$city->city?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="small_group_finder_filter">
				<?php
					$categories = $this->db->where('type', 'small group')->select('category')->distinct()->order_by('category')->get('groups');
				?>
				<label for="small_group_finder_field_category">Category</label>
				<select name="small_group_finder_category" id="small_group_finder_field_category">
					<option value="">any category</option>
					<?php foreach ($categories->result() as $category): ?>
						<?php if ($category->category): ?>
						<option value="<?=$category->category?>"><?=$category->category?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="small_group_finder_filter">
				<label for="small_group_finder_field_day">Day</label>
				<select name="small_group_finder_day" id="small_group_finder_field_day">
					<option value="">any day</option>
					<option value="Sunday">Sunday</option>
					<option value="Monday">Monday</option>
					<option value="Tuesday">Tuesday</option>
					<option value="Wednesday">Wednesday</option>
					<option value="Thursday">Thursday</option>
					<option value="Friday">Friday</option>
					<option value="Saturday">Saturday</option>
				</select>
			</div>
			<div class="small_group_finder_filter">
				<label for="small_group_finder_field_childcare">Childcare</label>
				<select name="small_group_finder_childcare" id="small_group_finder_field_childcare">
					<option value="">doesn't matter</option>
					<option value="1">yes</option>
					<option value="0">no</option>
				</select>
			</div>
		</div>
		<div id="small_group_finder_results">
			
		</div>
	</div>
	<div id="small_group_finder_map"></div>
</form>