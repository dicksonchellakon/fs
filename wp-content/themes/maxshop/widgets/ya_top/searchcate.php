<div class="top-form top-search pull-left">
	<div class="topsearch-entry">
	<?php if (FALSE && in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
		<form role="search" method="get" id="searchform_special" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
			<div>
				<?php
				$args = array(
				'type' => 'post',
				'child_of' => 0,
				'parent' => '',
				'orderby' => 'id',
				'order' => 'ASC',
				'hide_empty' => false,
				'hierarchical' => 1,
				'exclude' => '',
				'include' => '',
				'number' => '',
				'taxonomy' => 'product_cat',
				'pad_counts' => false,

				);
				$product_categories = get_categories($args);
				if( count( $product_categories ) > 0 ){
				?>
				<div class="cat-wrapper">
					<label class="label-search">
						<select name="search_category" class="s1_option">
							<option value='' selected><?php _e( 'All Categories', 'yatheme' ) ?></option>
							<?php foreach( $product_categories as $cat ) {
							echo '<option value="'. esc_attr( $cat-> term_id ) .'">' . esc_html( $cat->name ). '</option>';
							}
							?>
						</select>
					</label>
				</div>
				<?php } ?>
				<input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="<?php _e( 'Search for products', 'woocommerce' ); ?>" />
				<button type="submit" title="Search" class="icon-search button-search-pro form-button"></button>
				<input type="hidden" name="search_posttype" value="product" />
			</div>
		</form>
		<?php }else{ ?>
			<?php get_template_part('templates/searchform'); ?>
		<?php } ?>
	</div>
</div>
