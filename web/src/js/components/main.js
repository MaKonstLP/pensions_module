'use strict';
import Swiper, { Navigation, Pagination, Thumbs } from 'swiper';
import YouTubePlayer from 'youtube-player';

export default class Main {
	constructor() {
		let self = this;
		$('body').on('click', '[data-seo-control]', function () {
			$(this).closest('[data-seo-text]').addClass('_active');
		});
		var fired = false;

		window.addEventListener('click', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		window.addEventListener('scroll', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		window.addEventListener('mousemove', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		window.addEventListener('touchmove', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		$('.header_callback_button').on('click', function () {
			bookingPopupOpenClose();
		});

		$('.object_reserve').on('click', function () {
			bookingPopupOpenClose();
		});

		$('.popup_form_close').on('click', function () {
			bookingPopupOpenClose();
		});

		$('.footer_callback_button').on('click', function () {
			bookingPopupOpenClose();
		});


		/* ----------------------- Dinamic Adapt START -----------------------  */
		// Dynamic Adapt v.1
		// HTML data-da="where(uniq class name),position(digi),when(breakpoint)"
		// e.x. data-da="item,2,992"

		let originalPositions = [];
		let daElements = document.querySelectorAll('[data-da]');
		let daElementsArray = [];
		let daMatchMedia = [];
		//Заполняем массивы
		if (daElements.length > 0) {
			let number = 0;
			for (let index = 0; index < daElements.length; index++) {
				const daElement = daElements[index];
				const daMove = daElement.getAttribute('data-da');
				if (daMove != '') {
					const daArray = daMove.split(',');
					const daPlace = daArray[1] ? daArray[1].trim() : 'last';
					const daBreakpoint = daArray[2] ? daArray[2].trim() : '767';
					const daType = daArray[3] === 'min' ? daArray[3].trim() : 'max';
					const daDestination = document.querySelector('.' + daArray[0].trim())
					if (daArray.length > 0 && daDestination) {
						daElement.setAttribute('data-da-index', number);
						//Заполняем массив первоначальных позиций
						originalPositions[number] = {
							"parent": daElement.parentNode,
							"index": indexInParent(daElement)
						};
						//Заполняем массив элементов 
						daElementsArray[number] = {
							"element": daElement,
							"destination": document.querySelector('.' + daArray[0].trim()),
							"place": daPlace,
							"breakpoint": daBreakpoint,
							"type": daType
						}
						number++;
					}
				}
			}
			dynamicAdaptSort(daElementsArray);

			//Создаем события в точке брейкпоинта
			for (let index = 0; index < daElementsArray.length; index++) {
				const el = daElementsArray[index];
				const daBreakpoint = el.breakpoint;
				const daType = el.type;

				daMatchMedia.push(window.matchMedia("(" + daType + "-width: " + daBreakpoint + "px)"));
				daMatchMedia[index].addListener(dynamicAdapt);
			}
		}
		//Основная функция
		function dynamicAdapt(e) {
			for (let index = 0; index < daElementsArray.length; index++) {
				const el = daElementsArray[index];
				const daElement = el.element;
				const daDestination = el.destination;
				const daPlace = el.place;
				const daBreakpoint = el.breakpoint;
				const daClassname = "_dynamic_adapt_" + daBreakpoint;

				if (daMatchMedia[index].matches) {
					//Перебрасываем элементы
					if (!daElement.classList.contains(daClassname)) {
						let actualIndex = indexOfElements(daDestination)[daPlace];
						if (daPlace === 'first') {
							actualIndex = indexOfElements(daDestination)[0];
						} else if (daPlace === 'last') {
							actualIndex = indexOfElements(daDestination)[indexOfElements(daDestination).length];
						}
						daDestination.insertBefore(daElement, daDestination.children[actualIndex]);
						daElement.classList.add(daClassname);
					}
				} else {
					//Возвращаем на место
					if (daElement.classList.contains(daClassname)) {
						dynamicAdaptBack(daElement);
						daElement.classList.remove(daClassname);
					}
				}
			}
			customAdapt();
		}

		//Вызов основной функции
		dynamicAdapt();

		//Функция возврата на место
		function dynamicAdaptBack(el) {
			const daIndex = el.getAttribute('data-da-index');
			const originalPlace = originalPositions[daIndex];
			const parentPlace = originalPlace['parent'];
			const indexPlace = originalPlace['index'];
			const actualIndex = indexOfElements(parentPlace, true)[indexPlace];
			parentPlace.insertBefore(el, parentPlace.children[actualIndex]);
		}
		//Функция получения индекса внутри родителя
		function indexInParent(el) {
			var children = Array.prototype.slice.call(el.parentNode.children);
			return children.indexOf(el);
		}
		//Функция получения массива индексов элементов внутри родителя 
		function indexOfElements(parent, back) {
			const children = parent.children;
			const childrenArray = [];
			for (let i = 0; i < children.length; i++) {
				const childrenElement = children[i];
				if (back) {
					childrenArray.push(i);
				} else {
					//Исключая перенесенный элемент
					if (childrenElement.getAttribute('data-da') == null) {
						childrenArray.push(i);
					}
				}
			}
			return childrenArray;
		}
		//Сортировка объекта
		function dynamicAdaptSort(arr) {
			arr.sort(function (a, b) {
				if (a.breakpoint > b.breakpoint) { return -1 } else { return 1 }
			});
			arr.sort(function (a, b) {
				if (a.place > b.place) { return 1 } else { return -1 }
			});
		}
		//Дополнительные сценарии адаптации
		function customAdapt() {
			//const viewport_width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
		}
		/* ----------------------- Dinamic Adapt END -----------------------  */


		function load_other() {
			setTimeout(function () {
				self.init();
			}, 100);
			self.youtube_run();
		}






		/* ------------------------------- NEW JS START ------------------------------- */


		let submenuBurger = $('[data-header-submenu-burger]');
		submenuBurger.on('click', function () {
			// $(this).parent().toggleClass('_active');
			$(this).toggleClass('_active');
		})

		let topMenuBurger = $('[data-header-top-burger]');
		topMenuBurger.on('click', function () {
			$(this).toggleClass('_active');
			$('.header__bot').toggleClass('_active');
		})

		$(document).click(function (e) {
			let headerSubmenu = $('.header__bot-submenu');
			let headerMenu = $('.header__bot');

			if (!submenuBurger.is(e.target) && !submenuBurger.children().is(e.target) && !headerSubmenu.is(e.target) && headerSubmenu.has(e.target).length === 0) {
				$('.header__bot-menu-item_service').removeClass('_active');
			}

			if (!topMenuBurger.is(e.target) && !topMenuBurger.children().is(e.target) && !headerMenu.is(e.target) && headerMenu.has(e.target).length === 0) {
				headerMenu.removeClass('_active');
				topMenuBurger.removeClass('_active');
			}

			let select = $('.select');
			let selectList = $('.new-select__list')
			if (!select.is(e.target) && !select.children().is(e.target) && !selectList.is(e.target) && selectList.has(e.target).length === 0) {
				select.removeClass('is-active');
				select.find('.new-select__list').slideUp(450);
			}
		});

		$('.title__header').find('.title__tooltip').hover(onIn, onOut);
		function onIn() {
			$('.title__header').find('.title__tooltip-text').addClass('_active');
		}
		function onOut() {
			$('.title__header').find('.title__tooltip-text').removeClass('_active');
		}

		// /* ----------FILTER START---------- */

		// $('.filter_check_item').on('click', function () {
		// 	let typePrice = $('[data-type="price"] .filter_check_item');

		// 	if ($(this).parent().is('[data-type="price"]')) {
		// 		typePrice.not(this).removeClass('_active');
		// 		$(this).toggleClass('_active');
		// 	} else {
		// 		$(this).toggleClass('_active');
		// 	}
		// })

		// let filterWrapper = $('.filter_wrapper')
		// $('[data-show-filter]').on('click', function () {
		// 	$('[data-filter-wrapper]').addClass('_active');
		// 	// filterWrapper.addClass('_active');

		// 	windowSize();
		// })
		// $('[data-filter-close]').on('click', function () {
		// 	$('[data-filter-wrapper]').removeClass('_active');
		// 	// filterWrapper.removeClass('_active');
		// })

		// $(document).on('click', function (e) {
		// 	if (!$('[data-filter-wrapper]').is(e.target)
		// 	// if (!filterWrapper.is(e.target)
		// 		&& !$('[data-filter-wrapper]').children().is(e.target)
		// 		// && !filterWrapper.children().is(e.target)
		// 		&& !$('[data-show-filter]').is(e.target)
		// 		&& $('[data-filter-wrapper]').has(e.target).length === 0) {
		// 		// && filterWrapper.has(e.target).length === 0) {
		// 		$('[data-filter-wrapper]').removeClass('_active');
		// 		// filterWrapper.removeClass('_active');
		// 	}
		// });

		// function windowSize() {
		// 	let windowHeight = $(window).height();
		// 	let filterHeight = $('[data-filter-wrapper]').find('.filter').height();
		// 	// let filterHeight = filterWrapper.find('.filter').height();

		// 	console.log('filterHeight ' + filterHeight);

		// 	if ($(window).width() < '1200' && filterHeight > windowHeight) {

		// 		$('.filter_wrapper').addClass('_fixed');

		// 		$('.filter_wrapper').on('scroll', function () {
		// 			var scroll = $(this).scrollTop() + windowHeight;
		// 			console.log('scroll: ' + scroll);

		// 			if (scroll+1 > filterHeight) {
		// 				$('.filter_wrapper').removeClass('_fixed');
		// 			} else {
		// 				$('.filter_wrapper').addClass('_fixed');
		// 			}
		// 		})
		// 	} else {
		// 		$('.filter_wrapper').removeClass('_fixed');
		// 	}
		// }

		// $(window).on('load resize', windowSize);

		// /* ----------FILTER END---------- */


		document.querySelectorAll('.listing__item-images').forEach(n => {

			// $('.listing__item-images').forEach((t,e) => {

			var listinImagesSliderThumb = new Swiper(n.querySelector('.listing__images-thumb'), {
				// let listinImagesSliderThumb = new Swiper($(e).find('.listing__images-thumb'), {
				spaceBetween: 4,
				slidesPerView: 'auto',
				freeMode: true,
				watchSlidesProgress: true,
			});

			var listinImagesSlider = new Swiper(n.querySelector('.listing__images-slider'), {
				// let listinImagesSlider = new Swiper($(e).find('.listing__images-slider'), {
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


		/* ----------FORM SELECT START---------- */

		/* $('.select').each(function () {
			const _this = $(this),
				selectOption = _this.find('option'),
				selectOptionLength = selectOption.length,
				selectedOption = selectOption.filter(':selected'),
				duration = 450; // длительность анимации 

			_this.hide();
			_this.wrap('<div class="select"></div>');
			$('<div>', {
				class: 'new-select',
				text: _this.children('option:disabled').text()
			}).insertAfter(_this);

			const selectHead = _this.next('.new-select');
			$('<div>', {
				class: 'new-select__list'
			}).insertAfter(selectHead);

			const selectList = selectHead.next('.new-select__list');
			for (let i = 1; i < selectOptionLength; i++) {
				$('<div>', {
					class: 'new-select__item',
					html: $('<span>', {
						text: selectOption.eq(i).text()
					})
				})
					.attr('data-value', selectOption.eq(i).val())
					.appendTo(selectList);
			}

			const selectItem = selectList.find('.new-select__item');
			selectList.slideUp(0);
			selectHead.on('click', function () {
				if (!$(this).hasClass('_active')) {
					$(this).addClass('_active');
					selectList.slideDown(duration);

					selectItem.on('click', function () {
						let chooseItem = $(this).data('value');

						selectItem.not(this).removeClass('_active');
						$(this).addClass('_active');

						$('select').val(chooseItem).attr('selected', 'selected');
						selectHead.text($(this).find('span').text());

						selectList.slideUp(duration);
						selectHead.removeClass('_active');
					});

				} else {
					$(this).removeClass('_active');
					selectList.slideUp(duration);
				}
			});
		}); */

		/* ----------FORM SELECT END---------- */

		$('[data-callback]').on('click', function () {
			let form = $('.form-callback');
			openPopupForm(form);
		})
		$('[data-form-close]').on('click', function () {
			$('body').removeClass('_overflow');
			$(this).closest('.form').removeClass('_active');
			$(this).closest('.form').removeClass('_success');
		})

		$('[data-listing-list]').on('click', '[data-check]', function () {
			let form = $('.form-listing');
			let pansionName = $(this).closest('.listing__item-info').find('.listing__item-info-title').text();
			let hostName = document.location.hostname;
			let pansionUrl = $(this).closest('.listing__item-info').find('.listing__item-info-title').attr('href');

			form.find('form').attr('data-pansion-name', $.trim(pansionName));
			form.find('form').attr('data-pansion-url', 'https://'+hostName + pansionUrl);

			openPopupForm(form);
		})

		$('[data-book]').on('click', function () {
			let form = $(this).closest('.item__info').find('.form-item-book');
			openPopupForm(form);
		})

		$('[data-callback-item]').on('click', function () {
			let form = $(this).closest('.item__info').find('.form-item-callback');
			openPopupForm(form);
		})

		$('[data-callback-service]').on('click', function () {
			let form = $('.form-item-callback');
			openPopupForm(form);
		})

		function openPopupForm(formWrap) {
			$('body').addClass('_overflow');
			formWrap.addClass('_active');
			// formWrap.find('.form__success').hide();
			formWrap.find('.form_wrapper').show();
		}

		$('[data-form-choose-step-two]').on('click', function () {
			$('.form-choose__first-step').removeClass('_active');
			$('.form-choose__second-step').addClass('_active');
		})
		$('[data-form-choose-step-one]').on('click', function () {
			$('.form-choose__first-step').addClass('_active');
			$('.form-choose__second-step').removeClass('_active');
		})

		// $('[data-action="form_checkbox"]').on('click', (e) => {
		// 	let $el = $(e.currentTarget);
		// 	let $input = $el.siblings('input');

		// 	if (!$(e.target).hasClass('form_policy_link')) {
		// 		$el.toggleClass("_active");
		// 		$input.prop("checked", !$input.prop("checked"));
		// 		// e.stopImmediatePropagation();
		// 	}
		// });

		// ПЕРЕДЕЛАТЬ НА УСПЕШНОЕ СОБЫТИЕ ОТПРАВКИ ФОРМЫ
		$('.form__submit-btn').on('click', function () {
			let form = $(this).closest('.form');

			// form.find('.form_wrapper').hide();
			// form.find('.form__success').show();
		})

		// $('[data-form-choose-success]').on('click', function () {
		// 	let form = $(this).closest('.form');

		// 	form.find('.form_wrapper').show();
		// 	form.find('.form__success').hide();
		// 	form.find('.form-choose__first-step').addClass('_active');
		// 	form.find('.form-choose__second-step').removeClass('_active');
		// })


		var reviewsSlider = new Swiper('.reviews__slider', {
			modules: [Navigation, Pagination],
			slidesPerView: 'auto',
			spaceBetween: 32,

			navigation: {
				nextEl: '.reviews__slider-btn-next',
				prevEl: '.reviews__slider-btn-prev',
			},
			pagination: {
				el: '.reviews__slider-pagination',
			},
		});

		$('.reviews__slider-text p').each(function () {
			let paragraphHeight = $(this).height();

			if (paragraphHeight > 116) {
				$(this).closest('.reviews__slider-text').addClass('_collapse');
				$(this).closest('.reviews__slider-text').find('[data-review-text-more]').show();
			} else {
				$(this).closest('.reviews__slider-text').find('[data-review-text-more]').hide();
			}
		})

		$('[data-review-text-more]').on('click', function () {
			$(this).hide();
			$(this).closest('.reviews__slider-text').removeClass('_collapse');
			$(this).closest('.reviews__slider-text').addClass('_active');
		})

		$('.faq__accordion-item-title').on('click', function () {
			if ($('.faq__accordion-item-title').not(this).closest('.faq__accordion-item').hasClass('_active')) {
				$('.faq__accordion-item-text').slideUp();
				$('.faq__accordion-item').removeClass('_active');
			}
			$(this).closest('.faq__accordion-item').toggleClass('_active');
			$(this).closest('.faq__accordion-item').find('.faq__accordion-item-text').slideToggle();
		})

		var videoReviewsSlider = new Swiper('.video-reviews__slider', {
			modules: [Navigation, Pagination],
			slidesPerView: 'auto',
			spaceBetween: 32,
			loop: true,

			navigation: {
				nextEl: '.video-reviews__slider-btn-next',
				prevEl: '.video-reviews__slider-btn-prev',
			},
			pagination: {
				el: '.video-reviews__slider-pagination',
			},
		});


		// /* ----------ITEM START---------- */

		// $('.item__gallery-img').on('click', function () {
		// 	let index = $(this).attr('data-index');

		// 	let itemSliderThumb = new Swiper('.item__slider-thumb', {
		// 		spaceBetween: 4,
		// 		slidesPerView: 'auto',
		// 		freeMode: true,
		// 		watchSlidesProgress: true,
		// 	});

		// 	let itemSlider = new Swiper('.item__slider', {
		// 		modules: [Navigation, Pagination, Thumbs],
		// 		loop: true,
		// 		spaceBetween: 10,
		// 		navigation: {
		// 			nextEl: '.item__slider-next',
		// 			prevEl: '.item__slider-prev',
		// 		},
		// 		thumbs: {
		// 			swiper: itemSliderThumb,
		// 		},
		// 	});

		// 	itemSlider.slideTo(index, 400);

		// 	$('.item__slider-wrap').addClass('_active');
		// })

		// if ($(window).width() <= '768') {
		// 	let itemSlider = new Swiper('.item__slider', {
		// 		loop: true,
		// 		spaceBetween: 10,
		// 		navigation: {
		// 			nextEl: '.item__slider-next',
		// 			prevEl: '.item__slider-prev',
		// 		},
		// 		pagination: {
		// 			el: '.item__slider-pagination',
		// 		},
		// 	});
		// }

		// $('.item__slider-wrap').on('click', function (e) {
		// 	if (!$('.item__slider-innerwrap').is(e.target) && $('.item__slider-innerwrap').has(e.target).length === 0) {
		// 		$('.item__slider-wrap').removeClass('_active');
		// 	}
		// })


		// let itemLicenceSlider = new Swiper('.item__licence-slider', {
		// 	modules: [Navigation, Pagination],
		// 	spaceBetween: 24,
		// 	slidesPerView: 'auto',
		// 	freeMode: true,
		// 	watchSlidesProgress: true,
		// 	navigation: {
		// 		nextEl: '.item__licence-slider-next',
		// 		prevEl: '.item__licence-slider-prev',
		// 	},
		// 	pagination: {
		// 		el: '.item__licence-slider-pagination',
		// 	},
		// });

		// let itemReviewSlider = new Swiper('.item__review-slider', {
		// 	modules: [Navigation, Pagination],
		// 	spaceBetween: 24,
		// 	slidesPerView: 'auto',
		// 	navigation: {
		// 		nextEl: '.item__review-slider-next',
		// 		prevEl: '.item__review-slider-prev',
		// 	},
		// 	pagination: {
		// 		el: '.item__review-slider-pagination',
		// 	},
		// });

		// /* ----------ITEM END---------- */


		// /* ----------CONTACTS START---------- */
		// $('[data-form-contacts-success]').on('click', function () {
		// 	let form = $(this).closest('.contacts__form-wrapper');

		// 	form.find('.contacts__form').show();
		// 	form.find('.contacts__form-success').hide();
		// })
		// /* ----------CONTACTS END---------- */

		// /* ----------ARTICLE START---------- */

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
		// /* ----------ARTICLE END---------- */

		/* ----------CONTACTS START---------- */
		$('[data-form-faq-success]').on('click', function () {
			let form = $(this).closest('.faq__form-wrapper');

			form.find('.faq__form').show();
			form.find('.faq__form-success').hide();
		})
		/* ----------CONTACTS END---------- */
		/* ------------------------------- NEW JS END ------------------------------- */







	}

	youtube_run() {
		var player = [];

		// $('.service_video_item_wrapper').each(function (i, obj) {
		$('.video__wrap').each(function (i, obj) {
			var currentVideoId = $(obj).find('.video__player').attr('id');
			player[i] = YouTubePlayer(currentVideoId, {
				videoId: currentVideoId,
				width: '100%',
				height: '100%',
			});

			let test = $(obj).find('.ytp-large-play-button.ytp-button');
			setTimeout(function () {
				test.hide();
			}, 100);
			console.log(test);
			// $(obj).find('.play_button_wrapper .play_button').on('click', function () {
			$(obj).find('.video__btn').on('click', function () {
				player[i].playVideo();
				// $(this).closest('.play_button_wrapper').addClass('_hidden');
				$(this).hide();
			});
		});
	}

	init() {
		//setTimeout(function() {
		//	(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		//	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		//	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		//	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		//	})(window,document,'script','dataLayer','GTM-PTTPDSK');
		//}, 100);

		$(".header_phone_button").on("click", this.helpWhithBookingButtonHandler);
		$(".footer_phone_button").on("click", this.helpWhithBookingButtonHandler);
		$(".header_form_popup").on("click", this.closePopUpHandler);
		$('.header_burger').on('click', this.burgerHandler);


		$(".back_to_header_menu").on("click", function () {
			var $button = $(".header_city_select");
			var $cityList = $(".city_select_search_wrapper");

			$cityList.addClass("_hide");
			$button.removeClass("_active");
		});

		$(".show_filter_button").on("click", this.showFilterButtonHandler);

		/* Настройка формы в окне popup */
		var $inputs = $(".header_form_popup .input_wrapper");

		for (var input of $inputs) {
			if ($(input).find("[name='email']").length !== 0
				|| $(input).find("[name='question']").length !== 0) {
				$(input).addClass("_hide");
			}
		}

		$(".header_form_popup .form_title_main").text("Помочь с выбором зала?");
		$(".header_form_popup .form_title_desc").addClass("_hide");
	}

	helpWhithBookingButtonHandler() {
		var $popup = $(".header_form_popup");
		var body = document.querySelector("body");
		if ($popup.hasClass("_hide")) {

			body.dataset.scrollY = self.pageYOffset;
			body.style.top = `-${body.dataset.scrollY}px`;

			$popup.removeClass("_hide");
			$(body).addClass("_modal_active");
			ym(66603799, 'reachGoal', 'headerlink')
		}
	}

	closePopUpHandler(e) {
		var $popupWrap = $(".header_form_popup");
		var $target = $(e.target);
		var $inputs = $(".header_form_popup input");
		var body = document.querySelector("body");

		if ($target.hasClass("close_button")
			|| $target.hasClass("header_form_popup")
			|| $target.hasClass("header_form_popup_message_close")) {
			$inputs.prop("value", "");
			$inputs.attr("value", "");
			$('.fc-day-number.fc-selected-date').removeClass('fc-selected-date')
			$popupWrap.addClass("_hide");
			$("body").removeClass("_modal_active");
			window.scrollTo(0, body.dataset.scrollY);
		}
	}

	burgerHandler(e) {
		if ($('header').hasClass('_active') || $('header').hasClass('filter_active')) {
			$('header').removeClass('_active');
		}
		else {
			$('header').addClass('_active');
		}

		if ($('header').hasClass('filter_active')) {
			$('header').removeClass('filter_active');
		}

		$(".filter_wrapper").removeClass("active");

		$(".header_menu_item").css("pointer-events", "auto");

		if (window.innerWidth < 768 && $("body").css("overflow") != "hidden") {
			$("body").css("max-height", "100vh");
			$("body").css("overflow", "hidden");
		} else if (window.innerWidth < 768 && $("body").css("overflow") == "hidden") {
			$("body").css("max-height", "none");
			$("body").css("overflow", "visible");
		}
	}

	closeBurgerHandler(e) {
		var $target = $(e.target);
		var $menu = $(".header_menu");

		if (!$menu.is($target)
			&& $menu.has($target).length === 0) {

			if ($('header').hasClass('_active')) {
				$('header').removeClass('_active');
			}
		}

	}



	showFilterButtonHandler() {
		$(".filter_wrapper").toggleClass("active");

		if (!$("header").hasClass("_active")) {
			$("header").toggleClass("filter_active")

			if (window.innerWidth < 1200) {
				$(".filter").addClass("submit_cancel_fixed");

				$(".filter_submit").addClass("filter_submit_fixed");
				$(".filter_cancel").addClass("fiter_cancel_fixed");

			}
		}

		if (window.innerWidth < 768 && $("body").css("overflow") != "hidden") {
			$("body").css("max-height", "100vh");
			$("body").css("overflow", "hidden");
		}
	}

}