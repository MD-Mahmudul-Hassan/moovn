var $prevThumbnail = $('.prev > div > img'), $prevText = $('.prev > div > h3'), $prevButn = $('.prev');
var $nextThumbnail = $('.next > div > img'), $nextText = $('.next > div > h3'), $nextButn = $('.next');
var thumbs, labels, speed;
var active, next, total, interval;
var prevClicked = false;
var sliderClass;

$.fn.sleekslider = function(object) {
	active = 1;

	sliderClass = $(this).attr('class');

	if (object.thumbs != undefined || object.thumbs != null) {
		if (object.thumbs.length > 1) {
			thumbs = object.thumbs;
			labels = object.labels;

			$prevThumbnail.attr('src', 'images/' + thumbs[thumbs.length - 1]);
			$prevText.text(labels[thumbs.length - 1]);

			$nextThumbnail.attr('src', 'images/' + thumbs[active]);
			$nextText.text(labels[active]);
		} else {
			$('.nav-split').hide();
		}
	} else {
		$('.nav-split').hide();
	}

	total = $('.slide').length;

	if (total > 1) {
		speed = object.speed;
		interval = setInterval('cycleSlides()', speed);
	} else {
		$('.pagination').hide();
		$('.tabs').hide();
	}
}

function cycleSlides() {
	next = (active === total)? 1 : active + 1;
	animateSlider();
}

function animateSlider() {
	var $activeSlide = $('.' + sliderClass + ' > .slide.active');
	var $nextSlide = $('.' + sliderClass + ' > .slide:nth-child(' + next + ')');

	$('nav.pagination > span.current').removeClass('current');
	$('nav.pagination > span:nth-child(' + next + ')').addClass('current');

	$('nav.tabs li.current').removeClass('current');
	$('nav.tabs li:nth-child(' + next + ')').addClass('current');

	$nextSlide.css('z-index',2);

	$activeSlide.fadeOut(1500,function(){
		$activeSlide.css('z-index',1).show().removeClass('active');
		$nextSlide.css('z-index',3).addClass('active');
    });

	if (thumbs != undefined || thumbs != null) {
		if (prevClicked) {
			var value = next - 2;
			if (value < 0) {
				value = thumbs.length - 1;
			}
			$prevThumbnail.attr('src', 'images/' + thumbs[value]);
			$prevText.text(labels[value]);
		} else {
			$prevThumbnail.attr('src', 'images/' + thumbs[active - 1]);
			$prevText.text(labels[active - 1]);
		}

		if (active === (thumbs.length - 1)) {
			$nextThumbnail.attr('src', 'images/' + thumbs[0]);
			$nextText.text(labels[0]);
		} else {
			$nextThumbnail.attr('src', 'images/' + thumbs[next]);
			$nextText.text(labels[next]);
		}
	}

    active = next;
}

$nextButn.click(function(event) {
	event.preventDefault();
	
	if ($(".sleekslider > .slide").is(":animated")) {
		return false;
	} else {
		next = (active === total) ? 1 : active + 1;
		animateSlider();
	}
});

$nextButn.hover(function(){
	clearInterval(interval);
}, function() {
	interval = setInterval('cycleSlides()', speed);
});

$prevButn.click(function(event) {
	event.preventDefault();

	if ($(".sleekslider > .slide").is(":animated")) {
		return false;
	} else {
		prevClicked = true;
		next = (active === 1) ? total : active - 1;
		animateSlider();
	}
});

$prevButn.hover(function(){
	clearInterval(interval);
}, function() {
	interval = setInterval('cycleSlides()', speed);
});

$('nav.pagination > span').hover(function(){
	clearInterval(interval);
}, function() {
	interval = setInterval('cycleSlides()', speed);
});

$('nav.pagination > span').click(function () {
	if ($(".sleekslider > .slide").is(":animated")) {
		return false;
	} else {
		var clicked = $(this).index();
		if (clicked === 0) {
			prevClicked = true;
		}

		next = clicked + 1;
		active = clicked;
		animateSlider();
	}
});

$('nav.tabs li').hover(function(){
	clearInterval(interval);
}, function() {
	interval = setInterval('cycleSlides()', speed);
});

$('nav.tabs li').click(function () {
	if ($(".sleekslider > .slide").is(":animated")) {
		return false;
	} else {
		var clicked = $(this).index();
		if (clicked === 0) {
			prevClicked = true;
		}
		
		next = clicked + 1;
		active = clicked;
		animateSlider();
	}
});