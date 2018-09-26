(function( $ ) {
	'use strict';

	/**
	* KIVI js stuff
	 */

	$(function() {

		$('.slick-for').slick({
		  slidesToShow: 1,
		  slidesToScroll: 1,
		  arrows: false,
		  fade: true,
		  adaptiveHeight: false,
		  asNavFor: '.slick-carousel'
		});

		$(".slick-carousel").slick({

		  // normal options...
		  infinite: true,
		  autoplay: true,
  		autoplaySpeed: 5000,
  		slidesToShow: 4,
  		slidesToScroll: 1,
  		asNavFor: '.slick-for',
  		adaptiveHeight: false,
  		dots: false,
  		centerMode: true,
  		focusOnSelect: true,

		  // the magic
		  responsive: [{

		      breakpoint: 1024,
		      settings: {
		        slidesToShow: 3,
		        infinite: true
		      }

		    }, {

		      breakpoint: 600,
		      settings: {
		        slidesToShow: 2,
		        dots: false
		      }

		    }, {

		      breakpoint: 300,
		      settings: "unslick" // destroys slick

		    }]
		});

	});

})( jQuery );
