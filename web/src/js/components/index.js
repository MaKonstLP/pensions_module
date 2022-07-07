'use strict';
import Filter from './filter';
import YaMapAll from './map';
// import Swiper from 'swiper';

export default class Index{
	constructor($block){
		var self = this;
		this.block = $block;
		this.filter = new Filter($('[data-filter-wrapper]'));
		this.yaMap = new YaMapAll(this.filter);

		//КЛИК ПО КНОПКЕ "ПОДОБРАТЬ"
		$('[data-filter-button]').on('click', function(){
			self.redirectToListing();
		});

		// var topSlider = new Swiper('.swiper_top', {

		// 	// width: 1136,
		// 	initialSlide: 1,
		// 	spaceBetween: 0,
		// 	loop: true,

		// 	slidesPerView: 'auto',
		// 	centeredSlides: true,

		// 	navigation: {
		// 		nextEl: '.top-swiper-button-next',
		// 		prevEl: '.top-swiper-button-prev',
		// 	},

		// 	pagination: {
		// 		el: '.top-swiper-pagination',
		// 		clickable: true,
		// 	},

		// 	breakpoints: {

		// 		// 1200: {
		// 		// 	width: 768,
		// 		// },
		// 	},

		// });
	}

	redirectToListing(){
		this.filter.filterMainSubmit();
		this.filter.promise.then(
			response => {
				// ym(66603799,'reachGoal','filter');
				// dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Search', 'eventAction' : 'Filter'});
				// console.log(response);
				window.location.href = response;
			}
		);
	}

	
}