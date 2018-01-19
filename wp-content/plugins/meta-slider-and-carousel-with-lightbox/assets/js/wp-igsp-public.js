jQuery(document).ready(function($){

	// Initialize Slider
	$( '.msacwl-slider' ).each(function( index ) {

		var slider_id   = $(this).attr('id');
		var slider_conf = $.parseJSON( $(this).parent('.msacwl-slider-wrap').find('.msacwl-slider-conf').text());
		
		jQuery('#'+slider_id+' .msacwl-gallery-slider').slick({
			dots			: (slider_conf.dots) == "true" ? true : false,
			infinite		: true,
			arrows			: (slider_conf.arrows) == "true" ? true : false,
			speed			: parseInt(slider_conf.speed),
			autoplay		: (slider_conf.autoplay) == "true" ? true : false,
			autoplaySpeed	: parseInt(slider_conf.autoplay_speed),			
			rtl             : (WpIsgp.is_rtl) == "true" ? true : false,
			adaptiveHeight  : true,
			mobileFirst    	: (WpIsgp.is_mobile == 1) ? true : false,
		});
	});
	
	// Initialize Carousel
	$( '.msacwl-carousel' ).each(function( index ) {
		
		var slider_id   = $(this).attr('id');
		var slider_conf = $.parseJSON( $(this).parent('.msacwl-carousel-wrap').find('.msacwl-carousel-conf').text());
		
		jQuery('#'+slider_id+' .msacwl-gallery-carousel').slick({
			dots			: (slider_conf.dots) == "true" ? true : false,
			infinite		: true,
			arrows			: (slider_conf.arrows) == "true" ? true : false,
			speed			: parseInt(slider_conf.speed),
			autoplay		: (slider_conf.autoplay) == "true" ? true : false,
			autoplaySpeed	: parseInt(slider_conf.autoplay_speed),
			slidesToShow	: parseInt(slider_conf.slide_to_show),
			slidesToScroll	: parseInt(slider_conf.slide_to_scroll),
			rtl             : (WpIsgp.is_rtl) == "true" ? true : false,
			adaptiveHeight  : true,
			mobileFirst    	: (WpIsgp.is_mobile == 1) ? true : false,
			responsive: [{
				breakpoint: 1023,
				settings: {
					slidesToShow: (parseInt(slider_conf.slide_to_show) > 3) ? 3 : parseInt(slider_conf.slide_to_show),
					slidesToScroll: 1,
				}
			},{
				breakpoint: 767,	  			
				settings: {
					slidesToShow: (parseInt(slider_conf.slide_to_show) > 2) ? 2 : parseInt(slider_conf.slide_to_show),
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 479,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: false
				}
			},
			{
				breakpoint: 319,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: false
				}	    		
			}]
		});
	});
	
	// Magnific Popup
	jQuery('.msacwl-gallery-slider, .msacwl-gallery-carousel').magnificPopup({
		delegate: '.slick-slide:not(.slick-cloned) a',
		type: 'image',
		tLoading: 'Loading image #%curr%...',
		mainClass: 'mfp-with-zoom mfp-img-mobile',
		gallery: {
			enabled: true,
			navigateByImgClick: true,			
		}
	});
});