/*
*	Smooth Scroll Handle
*	pre-minification
*/
jQuery(function(){	

    var $window = jQuery(window);
	var scrollTime = smooth_scroll.scrollTime; // defined on scroll-progress-options.php (optons page)
	var scrollDistance = smooth_scroll.scrollDistance; // defined on scroll-progress-options.php (optons page)
	
	$window.on("mousewheel DOMMouseScroll", function(event){

		event.preventDefault();	

		var delta = event.originalEvent.wheelDelta/120 || -event.originalEvent.detail/3;
		var scrollTop = $window.scrollTop();
		var finalScroll = scrollTop - parseInt(delta*scrollDistance);

		TweenMax.to($window, scrollTime, {
			scrollTo : { y: finalScroll, autoKill:true },
				ease: Power1.easeOut,
				overwrite: 5							
			});

	});
});