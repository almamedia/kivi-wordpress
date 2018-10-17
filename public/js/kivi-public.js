(function( $ ) {
	'use strict';

	/**
	* KIVI js stuff
	 */

	$(function() {

		$('.slick-for').slick({
		  slidesToShow: 1,
		  slidesToScroll: 1,
		  arrows: true,
      fade: true,
      adaptiveHeight: true,
      infinite: false,
		  asNavFor: '.slick-carousel'
		});

		$(".slick-carousel").slick({

      asNavFor: '.slick-for',
      slidesToShow: 8,
      slidesToScroll: 8,
      dots: false,
      focusOnSelect: true,
      infinite: false,
      arrows: true,
      variableWidth: true,

		  responsive: [{

		      breakpoint: 1024,
		      settings: {
		        slidesToShow: 6,
		        infinite: false
		      }

		    }, {

		      breakpoint: 600,
		      settings: {
		        slidesToShow: 4,
		      }

		    }, {

		      breakpoint: 300,
		      settings: "unslick" // destroys slick

		    }]
    });
    $(".slick-carousel .slick-slide").on("click", function (e) {
      $(".slick-carousel .slick-slide").removeClass("slick-current");
      $(this).addClass('slick-current slick-active');
    })

    $(".slick-next").on("click", function () {
      var active = $(".slick-for .slick-current.slick-active").data("slickIndex");
      $(".slick-carousel .slick-slide").removeClass("slick-current");
      $(".slick-carousel [data-slick-index='"+ active +"'] ").addClass('slick-current');
    })

    $(".slick-prev").on("click", function () {
      var active = $(".slick-for .slick-current.slick-active").data("slickIndex");
      $(".slick-carousel .slick-slide").removeClass("slick-current");
      $(".slick-carousel [data-slick-index='"+ active +"'] ").addClass('slick-current');
    })

    $('.slick-for').on('setPosition', function(event, slick, currentSlide, nextSlide){
      var pic = document.querySelector(".slick-for [data-slick-index='0'] img ");
      var rootElement = document.documentElement;
      console.log("resized", pic.height);
      if( pic.height > 150 ){
        rootElement.style.setProperty('--img-height', pic.height + "px");
      }
    });
  });

})( jQuery );


document.addEventListener("DOMContentLoaded", function(){

  var hideDefault = document.querySelectorAll(".hide-by-default");
  hideDefault.forEach( function( item ) {
    item.classList.add("is-hidden");
    var select = item.dataset.target;
    document.getElementById(select).classList.add("is-hidden");
  } );

  var toggles = document.querySelectorAll(".kivi-toggle");
  toggles.forEach( function(toggle) {
    toggle.addEventListener ( "click" ,  function(e) {
      e.target.classList.toggle("is-hidden");
      var select = e.target.dataset.target;
      document.getElementById(select).classList.toggle("is-hidden");
    });
  });

});
