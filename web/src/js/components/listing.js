'use strict';
import Filter from './filter';
import YaMapAll from './map';
import Swiper, { Navigation, Pagination, Thumbs } from 'swiper';
import Form from './form';

export default class Listing {
	constructor($block) {
		self = this;
		this.block = $block;
		this.filter = new Filter($('[data-filter-wrapper]'));
		this.yaMap = new YaMapAll(this.filter);
		this.form = new Form();

		self.form.initCustomFormSelect();



		// let fastFilterTopItems = $('.fast-filters_top').find('.fast-filters__item');
		// if (fastFilterTopItems.length == 0) {
		// 	let fastFilterTop = $('.fast-filters_top');
		// 	let activeFilterItems = $('.filter_select_item._active');
		// 	fastFilterTop.html('');
		// 	activeFilterItems.each(function (index, elem) {
		// 		let text = $(elem).find('p').text();
		// 		fastFilterTop.append('<div class="fast-filters__item filter-active"><a href="/' + text + '/">' + text + '</a> <span data-fast-filter-remove></span></div>');
		// 	})
		// }




		//КЛИК ПО КНОПКЕ "ПОДОБРАТЬ"
		$('[data-filter-button]').on('click', function () {
			self.reloadListing();

			setTimeout(self.initMainSlider, 500);
			setTimeout(self.form.initCustomFormSelect, 500);
		});

		//КЛИК ПО КНОПКЕ "СБРОСИТЬ"
		$('[data-filter-cancel]').on('click', function () {
			self.reloadListing();

			if ($('[data-filter-button]').hasClass('_disabled')) {
				$('[data-filter-button]').removeClass('_disabled');
			}
			// if ($('.header_menu_item').hasClass('_active')) {
			// 	$('.header_menu_item').removeClass('_active');
			// }

			// self.burgerHandler();
			setTimeout(self.initMainSlider, 500);
			setTimeout(self.form.initCustomFormSelect, 500);
		});

		//КЛИК ПО ПАГИНАЦИИ
		$('body').on('click', '[data-pagination-wrapper] [data-listing-pagitem]', function () {
			self.reloadListing($(this).data('page-id'));
			setTimeout(self.initMainSlider, 500);
			setTimeout(self.form.initCustomFormSelect, 500);
		});

		//КЛИК ПО КНОПКЕ "Показать на карте"
		$('[data-show-map]').on('click', function () {
			$(this).toggleClass('_active');
			$('.map').toggleClass('_active');

			self.reloadListing(1, true);
		})

		//map START
		$('[data-listing-list]').on('click', '[data-address]', function (e) {

			if (!$('[data-show-map]').hasClass('_active')) {
				$('[data-show-map]').addClass('_active');
				$('.map').addClass('_active');
			}

			let pansionCoordinates = [$(this).closest('.listing__item').attr("data-pansion-mapDotX"), $(this).closest('.listing__item').attr("data-pansion-mapDotY")];
			let pansionMyBalloonHeader = $(this).closest('.listing__item').attr("data-pansion-name");
			let pansionMyBalloonBody = $(this).closest('.listing__item').attr("data-pansion-address");
			let pansionId = $(this).closest('.listing__item').attr('data-pansion-id');
			let pansionSlug = $(this).closest('.listing__item').attr('data-pansion-slug');

			self.yaMap.showRestaurantOnMap(pansionCoordinates, pansionMyBalloonHeader, pansionMyBalloonBody, pansionId, pansionSlug);

			let map_offset_top = $('.map').offset().top;
			let map_height = $('.map').height();
			let header_height = $('header').height();
			let window_height = $(window).height();
			let scroll_length = map_offset_top - header_height - ((window_height - header_height) / 2) + map_height / 2;
			setTimeout(function () {
				$('html,body').animate({ scrollTop: scroll_length }, 400);
			}, 100);
		});
		//map END
	}

	initMainSlider() {
		document.querySelectorAll('.listing__item-images').forEach(n => {

			var listinImagesSliderThumb = new Swiper(n.querySelector('.listing__images-thumb'), {
				spaceBetween: 4,
				slidesPerView: 'auto',
				freeMode: true,
				watchSlidesProgress: true,
			});

			var listinImagesSlider = new Swiper(n.querySelector('.listing__images-slider'), {
				loop: true,
				spaceBetween: 10,
				modules: [Navigation, Pagination, Thumbs],
				navigation: {
					nextEl: '.listing__item-images-slider-next',
					prevEl: '.listing__item-images-slider-prev',
				},
				thumbs: {
					swiper: listinImagesSliderThumb,
				},
			});
		});
	}


	reloadListing(page = 1, showMap = false) {
		let self = this;

		self.block.addClass('_loading');
		self.filter.filterListingSubmit(page);
		self.filter.promise.then(
			response => {
				console.log("response");
				console.log(response);
				//ym(66603799,'reachGoal','filter');
				//dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Search', 'eventAction' : 'Filter'});
				$('[data-listing-list]').html(response.listing);
				$('[data-listing-title]').html(response.title);
				$('[data-listing-text-top]').html(response.text_top);
				$('[data-listing-text-bottom]').html(response.text_bottom);
				$('[data-pagination-wrapper]').html(response.pagination);
				$('.fast-filters_top').html(response.slices_top);
				$('.fast-filters-wrap').html(response.slices_bot);
				$('.title__header').html(response.title);
				$('.listing-description__text').html(response.content_text);

				$('.title__header').find('.title__tooltip').hover(onIn, onOut);
				function onIn() {
					$('.title__header').find('.title__tooltip-text').addClass('_active');
				}
				function onOut() {
					$('.title__header').find('.title__tooltip-text').removeClass('_active');
				}

				self.block.removeClass('_loading');
				if (showMap) {
					$('html,body').animate({ scrollTop: $('.map').offset().top - 80 }, 400);
				} else {
					$('html,body').animate({ scrollTop: $('.listing').offset().top - 160 }, 400);
				}

				//обновление количества площадок в кнопке "ПОКАЗАТЬ __"
				$('[data-filter-button]').html('Показать (' + response.total + ')');
				// history.pushState({}, '', '/ploshhadki/'+response.url);
				history.pushState({}, '', '/' + response.url);

				this.yaMap.refresh(this.filter);


				// let fastFilterTopItems = $('.fast-filters_top').find('.fast-filters__item');
				// if (fastFilterTopItems.length == 0) {
				// 	let fastFilterTop = $('.fast-filters_top');
				// 	let activeFilterItems = $('.filter_select_item._active');
				// 	fastFilterTop.html('');
				// 	activeFilterItems.each(function (index, elem) {
				// 		let text = $(elem).find('p').text();
				// 		fastFilterTop.append('<div class="fast-filters__item filter-active"><a href="/' + text + '/">' + text + '</a><span data-fast-filter-remove></span></div>');
				// 	})
				// }



			}
		);
	}
}