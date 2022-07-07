'use strict';

import Inputmask from 'inputmask';

export default class Filter {
	constructor($filter) {
		let self = this;
		this.$filter = $filter;
		this.state = {};

		this.init(this.$filter);

		self.getFilterAvalable();

		$('[data-filter-select-block]').each(function (i) {
			let selectItem = $(this).find('[data-filter-select-item]');
			let moreButton = $(this).find('[data-filter-select-more]');

			moreButton.on('click', function () {
				selectItem.removeClass('_hide');
				$(this).hide();
				windowSize();
			})
		});


		/* ----------FILTER START---------- */

		let filterWrapper = $('.filter_wrapper')
		$('[data-show-filter]').on('click', function () {
			filterWrapper.addClass('_active');

			windowSize();
		})
		$('[data-filter-close]').on('click', function () {
			filterWrapper.removeClass('_active');
		})

		$(document).on('click', function (e) {
			if (!filterWrapper.is(e.target)
				&& !filterWrapper.children().is(e.target)
				&& !$('[data-show-filter]').is(e.target)
				&& filterWrapper.has(e.target).length === 0) {
				filterWrapper.removeClass('_active');
			}
		});

		function windowSize() {
			let windowHeight = $(window).height();
			let filterHeight = filterWrapper.find('.filter').height();

			if ($(window).width() < '1200' && filterHeight > windowHeight) {
				filterWrapper.addClass('_fixed');

				filterWrapper.on('scroll', function () {
					var scroll = $(this).scrollTop() + windowHeight;

					if (scroll + (+(filterHeight % 1).toFixed(3)) >= filterHeight) {
						filterWrapper.removeClass('_fixed');
					} else {
						filterWrapper.addClass('_fixed');
					}
				})
			} else {
				filterWrapper.removeClass('_fixed');
			}
		}

		$(window).on('load resize', windowSize);

		/* ----------FILTER END---------- */


		//выбранные фильтры под заголовком в листинге
		$('.fast-filters_top').on('click', '[data-fast-filter-remove]', function () {
			console.log(123124);
			let activeFilterItems = $('.filter_select_item._active');
			let filterItem = $(this).closest('.fast-filters__item');
			let textFilterItem = filterItem.find('a').text();
			activeFilterItems.each(function (index, elem) {
				let text = $(elem).find('p').text();

				if (text == $.trim(textFilterItem)) {
					console.log(56756776);
					$(elem).removeClass('_active');
					self.selectStateRefresh($(elem).closest('[data-filter-select-block]'));
				}
			})
			$('[data-filter-button]').click();
			self.getFilterAvalable()
		})
		
		$('.fast-filters_top').on('click', '[data-fast-filter-cancel]', function () {
			$('[data-filter-cancel]').click();
		})



		//КЛИК ПО БЛОКУ С СЕЛЕКТОМ
		this.$filter.find('[data-filter-select-current]').on('click', function () {
			let $parent = $(this).closest('[data-filter-select-block]');
			self.selectBlockClick($parent);
		});

		//КЛИК ПО СТРОКЕ В СЕЛЕКТЕ
		this.$filter.find('[data-filter-select-item]').on('click', function () {
			$(this).toggleClass('_active');
			self.selectStateRefresh($(this).closest('[data-filter-select-block]'));
			self.reloadTotalCount();
			self.getFilterAvalable();
		});

		//КЛИК ПО ЧЕКБОКСУ
		// this.$filter.find('[data-filter-checkbox-item] .filter_check_item').on('click', function () {
		this.$filter.find('[data-filter-checkbox-item]').on('click', function () {
			// $('.filter_checkbox').find('[data-filter-checkbox-item]').not(this).removeClass('_checked'); // Снимаем чекбокс со всех остальных чекбоксов, кроме выбранного
			// $('[data-filter-checkbox-item] .filter_check_item').not(this).removeClass('_checked'); // Снимаем чекбокс со всех остальных чекбоксов, кроме выбранного
			$('[data-filter-checkbox-item]').not(this).removeClass('_checked'); // Снимаем чекбокс со всех остальных чекбоксов, кроме выбранного


			$(this).toggleClass('_checked');
			self.checkboxStateRefresh($(this));
			self.reloadTotalCount();

			self.getFilterAvalable();
		});

		//КЛИК ВНЕ БЛОКА С СЕЛЕКТОМ
		$('body').click(function (e) {
			if (!$(e.target).closest('.filter_select_block').length) {
				self.selectBlockActiveClose();
			}
		});


		//КЛИК ПО КНОПКЕ СБРОСИТЬ
		this.$filter.find('[data-filter-cancel]').on('click', function () {
			$(this).closest('[data-filter-wrapper]').find('[data-filter-select-item]._active').removeClass('_active');
			$(this).closest('[data-filter-wrapper]').find('[data-filter-checkbox-item]._checked').removeClass('_checked');

			let selectBlocks = $('[data-filter-select-block]');
			let checkboxes = $('[data-filter-checkbox-item]');

			//сброс всех данных из селектов
			selectBlocks.each(function () {
				delete self.state[$(this).data('type')];
			});
			//сброс всех данных из чекбоксов
			checkboxes.each(function () {
				delete self.state[$(this).data('type')];
			});

			self.selectStateRefresh($('[data-filter-select-block]'));
			// self.state = {};

			// self.reloadTotalCount();

			self.getFilterAvalable();
		});



		//ИНПУТ
		this.$filter.find('[data-filter-input-block] input').on("keyup", function (event) {
			var selection = window.getSelection().toString();
			if (selection !== '') {
				return;
			}
			if ($.inArray(event.keyCode, [38, 40, 37, 39]) !== -1) {
				return;
			}
			var $this = $(this);
			var input = $this.val();
			input = input.replace(/[\D\s\._\-]+/g, "");
			input = input ? parseInt(input, 10) : 0;

			self.inputStateRefresh($(this).attr('name'), input);
			$this.val(function () {
				return (input === 0) ? "" : input.toLocaleString("ru-RU");
			});
		});
	}

	init() {
		let self = this;

		this.$filter.find('[data-filter-select-block]').each(function () {
			self.selectStateRefresh($(this));
		});

		this.$filter.find('[data-filter-checkbox-item]').each(function () {
			self.checkboxStateRefresh($(this));
		});
	}

	filterListingSubmit(page = 1) {
		let self = this;
		self.state.page = page;

		console.log('self.state:');
		console.log(self.state);

		let data = {
			'filter': JSON.stringify(self.state)
		}

		this.promise = new Promise(function (resolve, reject) {
			self.reject = reject;
			self.resolve = resolve;
		});

		$.ajax({
			type: 'get',
			url: '/ajax/filter/',
			data: data,
			success: function (response) {
				response = $.parseJSON(response);
				self.resolve(response);
			},
			error: function (response) {

			}
		});
	}

	filterMainSubmit() {
		let self = this;
		let data = {
			'filter': JSON.stringify(self.state)
		}

		this.promise = new Promise(function (resolve, reject) {
			self.reject = reject;
			self.resolve = resolve;
		});

		$.ajax({
			type: 'get',
			url: '/ajax/filter-main/',
			data: data,
			success: function (response) {
				if (response) {
					//console.log(response);
					self.resolve('/ploshhadki/' + response);
					// self.resolve(response);
				}
				else {
					//console.log(response);
					self.resolve(self.filterListingHref());
				}
			},
			error: function (response) {

			}
		});
	}

	selectBlockClick($block) {
		if ($block.hasClass('_active')) {
			this.selectBlockClose($block);
		}
		else {
			this.selectBlockOpen($block);
		}
	}

	selectBlockClose($block) {
		$block.removeClass('_active');
	}

	selectBlockOpen($block) {
		this.selectBlockActiveClose();
		$block.addClass('_active');
	}

	selectBlockActiveClose() {
		this.$filter.find('[data-filter-select-block]._active').each(function () {
			$(this).removeClass('_active');
		});
	}

	selectStateRefresh($block) {
		let self = this;
		let blockType = $block.data('type');
		let $items = $block.find('[data-filter-select-item]._active');
		let selectText = '-';

		if ($items.length > 0) {
			self.state[blockType] = '';
			$items.each(function () {
				if (self.state[blockType] !== '') {
					self.state[blockType] += ',' + $(this).data('value');
					selectText = 'Выбрано (' + $items.length + ')';
				}
				else {
					self.state[blockType] = $(this).data('value');
					selectText = $(this).text();
				}
			});
		}
		else {
			delete self.state[blockType];
		}

		$block.find('[data-filter-select-current] p').text(selectText);
	}

	checkboxStateRefresh($item) {
		let blockType = $item.closest('[data-type]').data('type');
		if ($item.hasClass('_checked')) {
			this.state[blockType] = $item.find('[data-value]').data('value');
		}
		else {
			delete this.state[blockType];
		}
	}

	inputStateRefresh(type, val) {
		if (val > 0) {
			this.state[type] = val;
		}
		else {
			delete this.state[type];
		}
	}

	filterListingHref() {
		if (Object.keys(this.state).length > 0) {
			var href = '/ploshhadki/?';
			$.each(this.state, function (key, value) {
				href += '&' + key + '=' + value;
			});
		}
		else {
			var href = '/ploshhadki/';
		}

		return href;
	}

	//ОБНОВЛЕНИЕ КОЛИЧЕСТВА ПЛОЩАДОК В КНОПКЕ "ПОКАЗАТЬ __"
	reloadTotalCount(page = 1) {
		this.filterCountItemsRefresh(page);

		this.promise.then(
			response => {
				if (response.total == 0) {
					$('[data-filter-button]').html('Показать (0)');
					$('[data-filter-button]').addClass('_disabled');
				} else {
					$('[data-filter-button]').html('Показать (' + response.total + ')');
					$('[data-filter-button]').removeClass('_disabled');
				}
			}
		);
	}

	filterCountItemsRefresh(page = 1) {
		let self = this;
		self.state.page = page;

		let data = {
			'filter': JSON.stringify(self.state)
		}

		this.promise = new Promise(function (resolve, reject) {
			self.reject = reject;
			self.resolve = resolve;
		});

		$.ajax({
			type: 'get',
			url: '/ajax/get-total/',
			data: data,
			success: function (response) {
				response = $.parseJSON(response);
				self.resolve(response);
			},
			error: function (response) {
			}
		});
	}


	refreshFilterItems(disabledItemsList) {
		var self = this;

		for (var filter in disabledItemsList) {
			$(`[data-filter-select-block][data-type='${filter}'] [data-filter-select-item]`).addClass('_disabled');
			$(`[data-filter-select-block][data-type='${filter}'] [data-filter-select-item] span`).html('');

			$(`[data-filter-checkbox-item][data-type='${filter}'] .filter_check_item`).addClass('_disabled');
			$(`[data-filter-checkbox-item][data-type='${filter}'] .filter_check_item span`).html('');

			var currentArray = disabledItemsList[filter];

			if (typeof currentArray === 'string') {
				currentArray = currentArray.split(',');
				for (var item in currentArray) {
					$(`[data-value='${currentArray[item]}']`).removeClass('_disabled');
				}
			} else if (typeof currentArray === 'object') {
				let keys = Object.keys(currentArray)

				for (var i = 0, l = keys.length; i < l; i++) {
					// console.log(keys[i] + ' is ' + currentArray[keys[i]]);
					// keys[i] - ключ
					// currentArray[keys[i]] - а это свойство, доступное по этому ключу

					$(`[data-filter-select-block][data-type='${filter}'] [data-id='${keys[i]}']`).removeClass('_disabled');
					$(`[data-filter-select-block][data-type='${filter}'] [data-id='${keys[i]}'] span`).html(currentArray[keys[i]]);

					// $(`[data-filter-checkbox-item][data-type='${filter}'] [data-value='${keys[i]}']`).removeClass('_disabled');
					// $(`[data-filter-checkbox-item][data-type='${filter}'] [data-value='${keys[i]}'] span`).html(currentArray[keys[i]]);

					if (currentArray[keys[i]] == '0') {
						$(`[data-filter-checkbox-item][data-type='${filter}'] [data-value='${keys[i]}'] span`).html('');
					} else {
						$(`[data-filter-checkbox-item][data-type='${filter}'] [data-value='${keys[i]}']`).removeClass('_disabled');
						$(`[data-filter-checkbox-item][data-type='${filter}'] [data-value='${keys[i]}'] span`).html(currentArray[keys[i]]);
					}
				}
			}
		}
	}

	getFilterAvalable() {
		var self = this;

		var data = {
			'filter': JSON.stringify(self.state),
		}

		$.ajax({
			type: 'get',
			url: '/ajax/ajax-update-filter/',
			data: data,
			success: function (response) {
				self.refreshFilterItems(JSON.parse(response));
				console.log(JSON.parse(response));
			},
			error: function (response) {
				console.log('error');
			}
		});
	}
}