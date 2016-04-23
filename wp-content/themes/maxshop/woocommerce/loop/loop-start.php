<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */
$product_grid = isset($_COOKIE['product_grid']) ? $_COOKIE['product_grid'] : 0;
?>
<ul id="loop-products" class="products-loop list-unstyled <?php echo ( $product_grid == 1 ) ? 'products-list': ''; ?> row">