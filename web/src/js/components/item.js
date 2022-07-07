'use strict';
import Swiper, { Navigation, Pagination, Thumbs } from 'swiper';
import 'slick-carousel';
import * as Lightbox from '../../../node_modules/lightbox2/dist/js/lightbox.js';

export default class Item {
	constructor($item) {
		var self = this;
		this.sliders = new Array();

		$('[data-action="show_phone"]').on("click", function () {
			$(".object_book").addClass("_active");
			$(".object_book_hidden").addClass("_active");
			$(".object_book_interactive_part").removeClass("_hide");
			$(".object_book_send_mail").removeClass("_hide");
			// ym(66603799, 'reachGoal', 'showphone');
			// dataLayer.push({ 'event': 'event-to-ga', 'eventCategory': 'Search', 'eventAction': 'ShowPhone' });
		});

		$('[data-action="show_form"]').on("click", function () {
			$(".object_book_send_mail").addClass("_hide");
			$(".send_restaurant_info").removeClass("_hide");
		});

		$('[data-action="show_mail_sent"]').on("click", function () {
			$(".send_restaurant_info").addClass("_hide");
			$(".object_book_mail_sent").removeClass("_hide");
		});

		$('[data-action="show_form_again"]').on("click", function () {
			$(".object_book_mail_sent").addClass("_hide");
			$(".send_restaurant_info").removeClass("_hide");
		});

		$('[data-address]').on('click', function () {
			let mapOffsetTop = $('.map').offset().top;
			let mapHeight = $('.map').height();
			let headerHeight = $('header').height();
			let windowHeight = $(window).height();
			let scrollLength = mapOffsetTop - headerHeight - ((windowHeight - headerHeight) / 2) + mapHeight / 2;
			$('html,body').animate({ scrollTop: scrollLength }, 400);
		});

		$('[data-calc]').on('click', function (e) {
			e.preventDefault();
			let formCalcOffsetTop = $('.item__calc').offset().top;
			let formCalcHeight = $('.item__calc').height();
			let headerHeight = $('header').height();
			let windowHeight = $(window).height();
			let scrollLength = formCalcOffsetTop - headerHeight - ((windowHeight - headerHeight) / 2) + formCalcHeight / 2;
			$('html,body').animate({ scrollTop: scrollLength }, 400);
		});

		$('[data-book-open]').on('click', function () {
			$(this).closest('.object_book_email').addClass('_form');
		})

		$('[data-book-email-reload]').on('click', function () {
			$(this).closest('.object_book_email').removeClass('_success');
			$(this).closest('.object_book_email').addClass('_form');
		})


		/* ----------ITEM START---------- */

		$('.item__gallery-img').on('click', function () {
			let index = $(this).attr('data-index');

			let itemSliderThumb = new Swiper('.item__slider-thumb', {
				spaceBetween: 4,
				slidesPerView: 'auto',
				freeMode: true,
				watchSlidesProgress: true,
			});

			let itemSlider = new Swiper('.item__slider', {
				modules: [Navigation, Thumbs],
				loop: true,
				spaceBetween: 10,
				navigation: {
					nextEl: '.item__slider-next',
					prevEl: '.item__slider-prev',
				},
				thumbs: {
					swiper: itemSliderThumb,
				},
			});

			itemSlider.slideTo(index, 400);

			$('.item__slider-wrap').addClass('_active');
		})

		if ($(window).width() <= '768') {
			let itemSlider = new Swiper('.item__slider', {
				modules: [Navigation, Pagination],
				loop: true,
				spaceBetween: 10,
				navigation: {
					nextEl: '.item__slider-next',
					prevEl: '.item__slider-prev',
				},
				pagination: {
					el: '.item__slider-pagination',
				},
			});
		}

		$('.item__slider-wrap').on('click', function (e) {
			if (!$('.item__slider-innerwrap').is(e.target) && $('.item__slider-innerwrap').has(e.target).length === 0) {
				$('.item__slider-wrap').removeClass('_active');
			}
		})


		let itemLicenceSlider = new Swiper('.item__licence-slider', {
			modules: [Navigation, Pagination],
			spaceBetween: 24,
			slidesPerView: 'auto',
			freeMode: true,
			watchSlidesProgress: true,
			navigation: {
				nextEl: '.item__licence-slider-next',
				prevEl: '.item__licence-slider-prev',
			},
			pagination: {
				el: '.item__licence-slider-pagination',
			},
		});

		let itemReviewSlider = new Swiper('.item__review-slider', {
			modules: [Navigation, Pagination],
			spaceBetween: 24,
			slidesPerView: 'auto',
			navigation: {
				nextEl: '.item__review-slider-next',
				prevEl: '.item__review-slider-prev',
			},
			pagination: {
				el: '.item__review-slider-pagination',
			},
		});

		/* ----------ITEM END---------- */


	}
}


