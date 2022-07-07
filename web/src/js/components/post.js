'use strict';
import Swiper, { Navigation, Pagination, Thumbs } from 'swiper';

export default class Post{
	constructor($block){
		self = this;
		this.block = $block;
		this.swipers_gal = [];
		this.swipers_rest = [];
		

		$('[data-action="show_phone"]').on("click", function(){
			let $object_book = $(this).closest(".object_book");
			$object_book.addClass("_active");
			$object_book.find(".object_book_hidden").addClass("_active");
			$object_book.find(".object_book_interactive_part").removeClass("_hide");
			$object_book.find(".object_book_send_mail").removeClass("_hide");
			ym(66603799,'reachGoal','showphone');
			dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Search', 'eventAction' : 'ShowPhone'});
		});

		$('[data-action="show_form"]').on("click", function(){
			$(".object_book_send_mail").addClass("_hide");
			$(".send_restaurant_info").removeClass("_hide");
		});

		$('[data-action="show_mail_sent"]').on("click", function(){
			let $object_book = $(this).closest(".object_book");
			$object_book.find(".send_restaurant_info").addClass("_hide");
			$object_book.find(".object_book_mail_sent").removeClass("_hide");
		});

		$('[data-action="show_form_again"]').on("click", function(){
			let $object_book = $(this).closest(".object_book");
			$object_book.find(".object_book_mail_sent").addClass("_hide");
			$object_book.find(".send_restaurant_info").removeClass("_hide");
		});

		$('[data-book-open]').on('click', function(){
            $(this).closest('.object_book_email').addClass('_form');
        })

        $('[data-book-email-reload]').on('click', function(){
            $(this).closest('.object_book_email').removeClass('_success');
            $(this).closest('.object_book_email').addClass('_form');
        })
		
		/* ----------ARTICLE START---------- */

		// let articleSliderThumb = new Swiper('.article__slider-thumb', {
		// 	spaceBetween: 4,
		// 	slidesPerView: 'auto',
		// 	freeMode: true,
		// 	watchSlidesProgress: true,
		// });

		// let articleSlider = new Swiper('.article__slider-slider', {
		// 	modules: [Navigation, Pagination, Thumbs],
		// 	loop: true,
		// 	spaceBetween: 10,
		// 	navigation: {
		// 		nextEl: '.article__slider-next',
		// 		prevEl: '.article__slider-prev',
		// 	},
		// 	thumbs: {
		// 		swiper: articleSliderThumb,
		// 	},
		// 	pagination: {
		// 		el: '.article__slider-pagination',
		// 	},
		// });

		document.querySelectorAll('.article__slider').forEach(n => {

			var articleSliderThumb = new Swiper(n.querySelector('.article__slider-thumb'), {
				spaceBetween: 4,
				slidesPerView: 'auto',
				freeMode: true,
				watchSlidesProgress: true,
			});

			var articleSlider = new Swiper(n.querySelector('.article__slider-slider'), {
				modules: [Navigation, Pagination, Thumbs],
				loop: true,
				spaceBetween: 10,
				navigation: {
					nextEl: '.article__slider-next',
					prevEl: '.article__slider-prev',
				},
				thumbs: {
					swiper: articleSliderThumb,
				},
				pagination: {
					el: '.article__slider-pagination',
				},
			});
		});

		/* ----------ARTICLE END---------- */

		
	}
}