<?php

function simple_lineup__showbox_template($start, $duration, $display_on_program_page=false, $id=null, $order=0){
	// Damn PHP.
	// ID will be for existing records. Records will be uneditable.
?>

<li class="sheduling-widget--item js-sheduling-widget--item">
	<table class="sheduling-widget--item--table">
		<tbody>
			<tr>
				<th>Starts</th>
				<td class="js-sheduling-widget--item--start"><?php echo $start; ?></td> <!-- HH:MM - D.M.YYYY -->
			</tr>
			<tr>
				<th>Duration</th>
				<td class="js-sheduling-widget--item--duration"><?php echo $duration; ?></td> <!-- DDd HHh MMm -->
			</tr>
			<tr>
				<th>In program</th>
				<td>
					<div class="sheduling-widget--item--yesno js-sheduling-widget--item--yesno dashicons-before dashicons-<?php echo $display_on_program_page ? 'yes' : 'no'; ?>"></div>
				</td>
			</tr>
		</tbody>
	</table>
	<button type="button" class="button button-primary dashicons-before dashicons-trash sheduling-widget--item--delete-button js-sheduling-widget--item--delete-button"></button>
	<?php if($id): ?>
		<input type="hidden" name="simple_lineup__id[<?php echo $order; ?>]" value="<?php echo $id; ?>">
	<?php endif; ?>
</li>

<?php
};
