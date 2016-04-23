<?php if (is_active_sidebar_YA('above-footer')){ ?>

<div class="sidebar-above-footer theme-clearfix">
       <div class="container theme-clearfix">	   
		<?php dynamic_sidebar('above-footer'); ?>
	    </div>
</div>
<?php } ?>
<footer class="footer theme-clearfix" role="contentinfo">
	<div class="container theme-clearfix">
		<div class="row">
			<?php if (is_active_sidebar_YA('footer')){ ?>
								
					<?php dynamic_sidebar('footer'); ?>
				
			<?php } ?>
		</div>
	</div>
	<div class="copyright theme-clearfix">
		<div class="container clearfix">
			<div class="copyright-text pull-left">
				<?php _e( 'Copyright &copy; 2015 Fashion Shoppee Store. All Rights Reserved.', 'yatheme' )?>
			</div>
			<?php if (is_active_sidebar_YA('footer-copyright')){ ?>
				<div class="sidebar-copyright pull-right">					
					<?php dynamic_sidebar('footer-copyright'); ?>
				</div>
			<?php } ?>
		</div>
	</div>
</footer>
<?php if (is_active_sidebar_YA('floating') ){ ?>
	<div class="floating theme-clearfix">
		<?php dynamic_sidebar('floating');  ?>
	</div>
<?php } ?>
<?php if(ya_options()->getCpanelValue('back_active') == '1') { ?>
<a id="ya-totop" href="#" ></a>
<?php }?>
</div>
<script type="text/javascript">
/*** remove <p> *****/
jQuery('.panel-group .panel-default br').remove(); 
</script>
<?php if( ya_options()-> getCpanelValue( 'effect_active' ) == 1 ){ ?>
<?php if( is_home() || is_front_page() ){?>
<script type="text/javascript">
jQuery(function($){
	// The starting defaults.
    var config = {
        reset: true,
        init: true
    };
    window.scrollReveal = new scrollReveal( );
	
});
</script>
<?php } } ?>
<?php wp_footer(); ?>
