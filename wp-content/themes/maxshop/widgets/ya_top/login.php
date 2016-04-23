<?php 
$header_style = ya_options()->getCpanelValue('header_style');
if($header_style !='style4') { ?>
<?php do_action( 'before' ); ?>
<?php if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
<?php global $woocommerce; ?>
<div class="top-login pull-right">
	<?php if ( ! is_user_logged_in() ) {  ?>
	<ul>
		<li>
		    	<?php echo ' <a href="javascript:void(0);" data-toggle="modal" data-target="#login_form"><span>'.__('Login', 'yatheme').'</span></a> '; ?>
 <div class="modal fade" id="login_form" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog block-popup-login">
		<a href="javascript:void(0)" title="Close" class="close close-login" data-dismiss="modal">Close</a>
		<div class="tt_popup_login"><strong><?php _e('Sign in Or Register', 'yatheme'); ?></strong></div>
		<?php get_template_part('woocommerce/myaccount/login-form'); ?>
	</div>
</div>
		</li>
	</ul>
	<?php } else{?>
		<div class="div-logined">
			<?php 
				$user_id = get_current_user_id();
				$user_info = get_userdata( $user_id );	
			?>
			<a href="<?php echo wp_logout_url( home_url() ); ?>" title="Logout"><?php _e('Logout', 'yatheme'); ?></a>
		</div>
	<?php } ?>
</div>
<?php }  } ?>
