(function($) {
	"use strict";
	var $page = 0;
	var $ajax_url = ya_portfolio.ajax_url;
    var $ya_style = ya_portfolio.style;
	var $categories = ya_portfolio.categories;
	var $max_page = ya_portfolio.max_page;
	var $attributes = ya_portfolio.attributes;
	var $number = ya_portfolio.number;
	var $offset = ya_portfolio.offset;
	var $orderby = ya_portfolio.orderby;
	var $order = ya_portfolio.order;
	var $pf_id = ya_portfolio.pf_id;
	var $container_id = $('#container-'+ $pf_id);
	var $tab_id = $('#tab-'+ $pf_id);
	var $container = $container_id; //The ID for the list with all the blog posts
	if( $ya_style == 'fitRows' ){
		$container.isotope({ //Isotope options, 'item' matches the class in the PHP
			layoutMode : $ya_style
		});
	}else{
		$container.isotope({ //Isotope options, 'item' matches the class in the PHP
			layoutMode : $ya_style,
			masonry: {
			  columnWidth: '.portfolio-item'
			}
		});
	}
 
	//Add the class selected to the item that is clicked, and remove from the others
	var $optionSets = $tab_id,
	$optionLinks = $optionSets.find('li');
 
	$optionLinks.click(function(){
	var $this = $(this);
	// don't proceed if already selected
	if ( $this.hasClass('selected') ) {
	  return false;
	}
	var $optionSet = $this.parents($tab_id);
	$optionSets.find('.selected').removeClass('selected');
	$this.addClass('selected');
 
	//When an item is clicked, sort the items.
	 var selector = $(this).attr('data-portfolio-filter');
	$container.isotope({ filter: selector });
	return false;
	});
	var $page = 1;
	var $btn_loadmore = $('#'+ $pf_id + ' .btn-loadmore');
	$btn_loadmore.on("click", function(){
		if( $page >= $max_page ){
			return false;
		}
		$(this).addClass('btn-loading');
		jQuery.ajax({
			 type: "POST",
			 url: $ajax_url,
			 data: ({
				action : "sw_portfolio_ajax",
				catid  : $categories,
				numb   : $number,
				orderby: $orderby,
				order : $order,
				offset : $offset,
				page : $page,
				style : $ya_style,
				attributes: $attributes
			}),
			 success: function(data) {				
				var $newItems = $(data);
				if( $newItems.length > 0 ){
					$newItems.imagesLoaded( function(){
						$container_id.isotope("insert",$newItems).isotope();
					});
					$btn_loadmore.removeClass('btn-loading');
					if( $newItems.length < $number ){
						$btn_loadmore.addClass( 'btn-loaded' );
					}
					$page = $page + 1;
				 }else{
					$btn_loadmore.addClass( 'btn-loaded' );
				 }
			 }
		 });
	});
})(jQuery);


