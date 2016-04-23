<?php

$order      = isset( $instance['order'] )       ? strip_tags($instance['order']) : 'ASC';
$number     = isset( $instance['number'] ) ? intval($instance['number']) : 5;
$length     = isset( $instance['length'] )      ? intval($instance['length']) : 25;

?>

<p>
	<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'yatheme')?></label>
	<br />
	<select class="widefat"
		id="<?php echo $this->get_field_id('order'); ?>"
		name="<?php echo $this->get_field_name('order'); ?>">
		<option value="DESC" <?php if ($order=='DESC'){?> selected="selected"
		<?php } ?>>
			<?php _e('Descending', 'yatheme')?>
		</option>
		<option value="ASC" <?php if ($order=='ASC'){?> selected="selected"
		<?php } ?>>
			<?php _e('Ascending', 'yatheme')?>
		</option>
	</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of comments', 'yatheme')?></label>
	<br />
	<input class="widefat"
		id="<?php echo $this->get_field_id('number'); ?>"
		name="<?php echo $this->get_field_name('number'); ?>" type="text"
		value="<?php echo esc_attr($number); ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id('length'); ?>"><?php _e('Excerpt length (in words): ', 'yatheme')?></label>
	<br />
	<input class="widefat"
		id="<?php echo $this->get_field_id('length'); ?>"
		name="<?php echo $this->get_field_name('length'); ?>" type="text"
		value="<?php echo esc_attr($length); ?>" />
</p>
