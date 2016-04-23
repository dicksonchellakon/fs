(function($) {
	"use strict";
	/* Add Click On Ipad */
	$(window).resize(function(){
		var $width = $(this).width();
		if( $width < 1199 ){
			$( '.primary-menu .nav .dropdown-toggle'  ).each(function(){
				$(this).attr('data-toggle', 'dropdown');
			});
		}
	});
	/* Responsive Menu */
	$(document).ready(function(){
		$( '.show-dropdown' ).each(function(){
			$(this).on('click', function(){
				var $parent = $(this).parent().attr('class');
				var $class = $parent.replace( /\s/g, '.' );
				var $element = $( '.' + $class + ' > ul' );
				$element.toggle( 300 );
			});
		});
	});
    jQuery('.phone-icon-search').click(function(){
		//alert("The paragraph was clicked.");
        jQuery('.top-search').toggle("slide");
    });
	jQuery('ul.orderby.order-dropdown li ul').hide(); //hover in
    jQuery("ul.orderby.order-dropdown li span.current-li-content,ul.orderby.order-dropdown li ul").hover(function() {
        jQuery('ul.orderby.order-dropdown li ul').stop().fadeIn("fast");
    }, function() {
        jQuery('ul.orderby.order-dropdown li ul').stop().fadeOut("fast");
    });

    jQuery('.orderby-order-container ul.sort-count li ul').hide();
    jQuery('.sort-count.order-dropdown li span.current-li,.orderby-order-container ul.sort-count li ul').hover(function(){
        jQuery('.orderby-order-container ul.sort-count li ul').stop().fadeIn("fast");

    },function(){
        jQuery('.orderby-order-container ul.sort-count li ul').stop().fadeOut("fast");
    });

//  jQuery(".box-newsletter").center();

var mobileHover = function () {
    $('*').on('touchstart', function () {
        $(this).trigger('hover');
    }).on('touchend', function () {
        $(this).trigger('hover');
    });
};

mobileHover();

    jQuery('.product-categories')
        .find('li:gt(4)') //you want :gt(4) since index starts at 0 and H3 is not in LI
        .hide()
        .end()
        .each(function(){
            if($(this).children('li').length > 4){ //iterates over each UL and if they have 5+ LIs then adds Show More...
                $(this).append(
                    $('<li><a>See more   +</a></li>')
                        .addClass('showMore')
                        .click(function(){
                            if($(this).siblings(':hidden').length > 0){
                                $(this).html('<a>See less   -</a>').siblings(':hidden').show(400);
                            }else{
                                $(this).html('<a>See more   +</a>').show().siblings('li:gt(4)').hide(400);
                            }
                        })
                );
            }
        });
    /*Form search iP*/




    jQuery('a.phone-icon-menu').click(function(){
       var temp = jQuery('.navbar-inner.navbar-inverse').toggle( "slide" );
	   $(this).toggleClass('active');
    });
	$('.ya-tooltip').tooltip();
	// fix accordion heading state
	$('.accordion-heading').each(function(){
		var $this = $(this), $body = $this.siblings('.accordion-body');
		if (!$body.hasClass('in')){
			$this.find('.accordion-toggle').addClass('collapsed');
		}
	});
	

	// twice click
	$(document).on('click.twice', '.open [data-toggle="dropdown"]', function(e){
		var $this = $(this), href = $this.attr('href');
		e.preventDefault();
		window.location.href = href;
		return false;
	});

    $('#cpanel').collapse();

    $('#cpanel-reset').on('click', function(e) {

    	if (document.cookie && document.cookie != '') {
    		var split = document.cookie.split(';');
    		for (var i = 0; i < split.length; i++) {
    			var name_value = split[i].split("=");
    			name_value[0] = name_value[0].replace(/^ /, '');

    			if (name_value[0].indexOf(cpanel_name)===0) {
    				$.cookie(name_value[0], 1, { path: '/', expires: -1 });
    			}
    		}
    	}

    	location.reload();
    });

	$('#cpanel-form').on('submit', function(e){
		var $this = $(this), data = $this.data(), values = $this.serializeArray();

		var checkbox = $this.find('input:checkbox');
		$.each(checkbox, function() {

			if( !$(this).is(':checked') ) {
				name = $(this).attr('name');
				name = name.replace(/([^\[]*)\[(.*)\]/g, '$1_$2');

				$.cookie( name , 0, { path: '/', expires: 7 });
			}

		})

		$.each(values, function(){
			var $nvp = this;
			var name = $nvp.name;
			var value = $nvp.value;

			if ( !(name.indexOf(cpanel_name + '[')===0) ) return ;

			//console.log('name: ' + name + ' -> value: ' +value);
			name = name.replace(/([^\[]*)\[(.*)\]/g, '$1_$2');

			$.cookie( name , value, { path: '/', expires: 7 });

		});

		location.reload();

		return false;

	});

	$('a[href="#cpanel-form"]').on( 'click', function(e) {
		var parent = $('#cpanel-form'), right = parent.css('right'), width = parent.width();

		if ( parseFloat(right) < -10 ) {
			parent.animate({
				right: '0px',
			}, "slow");
		} else {
			parent.animate({
				right: '-' + width ,
			}, "slow");
		}

		if ( $(this).hasClass('active') ) {
			$(this).removeClass('active');
		} else $(this).addClass('active');

		e.preventDefault();
	});
/*Product listing select box*/
	jQuery('.catalog-ordering .orderby .current-li a').html(jQuery('.catalog-ordering .orderby ul li.current a').html());
	jQuery('.catalog-ordering .sort-count .current-li a').html(jQuery('.catalog-ordering .sort-count ul li.current a').html());
/*currency Selectbox*/
	$('.currency_switcher li a').click(function(){
		$current = $(this).attr('data-currencycode');
		jQuery('.currency_w > li > a').html($current);
	});
	
/*Quickview*/
	jQuery('.fancybox').fancybox({
		'width'     : 997,
		'height'   : 'auto',
		'autoSize' : false
	});
/*lavalamp*/
	$.fn.lavalamp = function(options){
		var defaults = {
    			elm_class: 'active',
				durations: 400
 		    },
            settings = $.extend(defaults, options);
		this.each( function(){
			var elm = ('> li');
			var current_check = $(elm, this).hasClass( settings.elm_class );
			current = elm + '.' + settings.elm_class;
			if( current_check ){
				var $this=jQuery(this), left0 = $(this).offset().left, dleft0 = $(current, this).offset().left - left0, dwidth0 = $('>ul>li.active', this).width();
				$(this).append('<div class="floatr"></div>');
				var $lava = jQuery('.floatr', $this), move = function(l, w){
					$lava.stop().animate({
						left: l,
						width: w
					}, {
						duration: settings.durations,
						easing: 'linear'
					});
				};
				
				var $li = jQuery('>li', this);
				//console.log( $li );
				// 1st time
				
				move(dleft0, dwidth0);
				$lava.show();
				$li.hover(function(e){
					var dleft = $(this).offset().left - left0;
					var dwidth = $(this).width();
					//console.log(dleft);
					move(dleft, dwidth);
				}, function(e){
					move(dleft0, dwidth0);
				});	
			}
		});
	}
	jQuery(document).ready(function(){
		var currency_show = jQuery('ul.currency_switcher li a.active').html();
		jQuery('.currency_to_show').html(currency_show);	
	}); 
/*end lavalamp*/
	jQuery(function($){
	// back to top
	$("#ya-totop").hide();
	$(function () {
		var wh = $(window).height();
		var whtml = $(document).height();
		$(window).scroll(function () {
			if ($(this).scrollTop() > whtml/10) {
					$('#ya-totop').fadeIn();
				} else {
					$('#ya-totop').fadeOut();
				}
			});
		$('#ya-totop').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
			});
	});
	// end back to top
	}); 
	$('#lang_sel ul > li > a').click(function(){
		$('#lang_sel ul > li ul').slideToggle(); 
	});
	var $current ='';
	$('#lang_sel ul > li > ul li a').on('click',function(){
		//console.log($(this).html());
		$current = $(this).html();
		$('#lang_sel ul > li > a.lang_sel_sel').html($current);
		 $a = $.cookie('lang_select_maxshop', $current, { expires: 1, path: '/'});	
	});
	if( $.cookie('lang_select_maxshop') && $.cookie('lang_select_maxshop').length > 0 ) {
		$('#lang_sel ul > li > a.lang_sel_sel').html($.cookie('lang_select_maxshop'));
	}
	jQuery(document).ready(function(){
  jQuery('.wpcf7-form-control-wrap').hover(function(){
   $(this).find('.wpcf7-not-valid-tip').css('display', 'none');
  });
 });
 $('.panel-group').on('show.bs.collapse', '.panel-collapse', function () {
        $(this).closest('.panel').addClass('active').siblings().removeClass('active');
});
}(jQuery));
(function($){
	$.fn.megamenu = function(options) {
		options = jQuery.extend({
			  wrap:'.nav-mega',
			  speed: 300,
			  justify: "",
			  mm_timeout: 200
		  }, options);
		var menuwrap = $(this);
		buildmenu(menuwrap);
		// Build menu
		function buildmenu(mwrap){
			mwrap.find('li').each(function(){
				var menucontent 		= $(this).find(".dropdown-menu");
				var menuitemlink 		= $(this).find(".item-link:first");
		    	var menucontentinner 	= $(this).find(".nav-level1");
		    	var mshow_timer = 0;
		    	var mhide_timer = 0;
		     	var li = $(this);
		     	var islevel1 = (li.hasClass('level1'))?true:false;
				var havechild = (li.hasClass('dropdown'))?true:false;
				if(menucontent){
		     		menucontent.hide();
		     	}
				li.mouseenter(function(el){
					el.stopPropagation();
					clearTimeout(mhide_timer);
					clearTimeout(mshow_timer);
					addHover(li);
					if(havechild){
						positionSubMenu(li, islevel1);
						mshow_timer = setTimeout(function(){ //Emulate HoverIntent					
							showSubMenu(li, menucontent, menucontentinner);
						}, options.mm_timeout);	
					}
				}).mouseleave(function(el){ //return;
					clearTimeout(mshow_timer);
					clearTimeout(mhide_timer);
					if(havechild){
						mhide_timer = setTimeout(function(){ //Emulate HoverIntent					
							hideSubMenu(li, menucontent, menucontentinner);
						}, options.mm_timeout);	

						//hideSubMenu(li, menucontent, menucontentinner);
					}
					removeHover(li);
			    });
			});
		}
		// Show Submenu
		function showSubMenu(li, mcontent, mcontentinner){		
			mcontentinner.animate({
				  opacity: 1
				}, 100, function() {
			});
			mcontent.css('opacity','1').stop(true, true).slideDown({ duration: options.speed});
		}
		// Hide Submenu
		function hideSubMenu(li, mcontent, mcontentinner){
			mcontentinner.animate({
				  opacity: 0
				}, 2*options.mm_timeout, function() {
			});
			mcontent.slideUp({ duration: options.mm_timeout});
		}
		// Add class hover to li
		function addHover(el){
			$(el).addClass('hover');
			
		}
		// Remove class hover to li
		function removeHover(el){
			$(el).removeClass('hover');
		}
		// Position Submenu
		function positionSubMenu(el, islevel1){
			menucontent 		= $(el).find(".dropdown-menu");
			menuitemlink 		= $(el).find(".item-link:first");
	    	menucontentinner 	= $(el).find(".nav-level1");
	    	wrap_O				= menuwrap.offset().left;
	    	wrap_W				= menuwrap.outerWidth();
	    	menuitemli_O		= menuitemlink.parent('li').offset().left;
	    	menuitemli_W		= menuitemlink.parent('li').outerWidth();
	    	menuitemlink_H		= menuitemlink.outerHeight();
	    	menuitemlink_W		= menuitemlink.outerWidth();
	    	menuitemlink_O		= menuitemlink.offset().left;
	    	menucontent_W		= menucontent.outerWidth();

			if (islevel1) { 
				menucontent.css({
					'top': menuitemlink_H + "px",
					'left': menuitemlink_O - menuitemli_O + 'px'
				})
				
				if(options.justify == "left"){
					var wrap_RE = wrap_O + wrap_W;
											// Coordinates of the right end of the megamenu object
					var menucontent_RE = menuitemlink_O + menucontent_W;
											// Coordinates of the right end of the megamenu content
					if( menucontent_RE >= wrap_RE ) { // Menu content exceeding the outer box
						menucontent.css({
							'left':wrap_RE - menucontent_RE + menuitemlink_O - menuitemli_O + 'px'
						}); // Limit megamenu inside the outer box
					}
				} else if( options.justify == "right" ) {
					var wrap_LE = wrap_O;
											// Coordinates of the left end of the megamenu object
					var menucontent_LE = menuitemlink_O - menucontent_W + menuitemlink_W;
											// Coordinates of the left end of the megamenu content
					if( menucontent_LE <= wrap_LE ) { // Menu content exceeding the outer box
						menucontent.css({
							'left': wrap_O
							- (menuitemli_O - menuitemlink_O) 
							- menuitemlink_O + 'px'
						}); // Limit megamenu inside the outer box
					} else {
						menucontent.css({
							'left':  menuitemlink_W
							+ (menuitemlink_O - menuitemli_O) 
							- menucontent_W + 'px'
						}); // Limit megamenu inside the outer box
					}
				}
			}else{
				_leftsub = 0;
				menucontent.css({
					'top': menuitemlink_H*0 +"px",
					'left': menuitemlink_W + _leftsub + 'px'
				})
				
				if(options.justify == "left"){
					var wrap_RE = wrap_O + wrap_W;
											// Coordinates of the right end of the megamenu object
					var menucontent_RE = menuitemli_O + menuitemli_W + _leftsub + menucontent_W;
											// Coordinates of the right end of the megamenu content
					//console.log(menucontent_RE+' vs '+wrap_RE);
					if( menucontent_RE >= wrap_RE ) { // Menu content exceeding the outer box
						menucontent.css({
							'left': _leftsub - menucontent_W + 'px'
						}); // Limit megamenu inside the outer box
					}
				} else if( options.justify == "right" ) {
					var wrap_LE = wrap_O;
											// Coordinates of the left end of the megamenu object
					var menucontent_LE = menuitemli_O - menucontent_W + _leftsub;
											// Coordinates of the left end of the megamenu content
					//console.log(menucontent_LE+' vs '+wrap_LE);
					if( menucontent_LE <= wrap_LE ) { // Menu content exceeding the outer box
						menucontent.css({
							'left': menuitemli_W - _leftsub + 'px'
						}); // Limit megamenu inside the outer box
					} else {
						menucontent.css({
							'left':  - _leftsub - menucontent_W + 'px'
						}); // Limit megamenu inside the outer box
					}
				}
			}
		}
	};
	$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
	jQuery(function($){
		$('.nav-mega').megamenu({ 
			'wrap':'#primary-menu .container'			
		});
	});
})(jQuery);


