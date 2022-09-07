import Animation from './animation.js';
//import modal from './modal';
import { status, json } from './utilities';
import Inputmask from 'inputmask';
var animation = new Animation;

export default class Form {
	constructor(form) {
		this.$form = $(form);
		this.$formWrap = this.$form.parents('.form_wrapper');
		this.$submitButton = this.$form.find('button[type="submit"]');
		this.$policy = this.$form.find('[name="policy"]');
		this.to = (this.$form.attr('action') == undefined || this.$form.attr('action') == '') ? this.to : this.$form.attr('action');
		let im_phone = new Inputmask('+7 (999) 999-99-99', {
			clearIncomplete: true,
		});
		im_phone.mask($(this.$form).find('[name="phone"]'));

		$('[data-form-choose-success]').on('click', function () {
			let form = $(this).closest('.form');

			form.find('.form_wrapper').show();
			// form.find('.form__success').hide();
			form.removeClass('_success');
			form.find('.form-choose__first-step').addClass('_active');
			form.find('.form-choose__second-step').removeClass('_active');
		})


		this.bind();
	}

	/* ----------FORM SELECT START---------- */
	initCustomFormSelect() {

		let selectHeader = $('.new-select');
		let selectItem = $('.new-select__item span');

		selectHeader.on('click', function () {

			let select = $(this).closest('.select');

			selectHeader.not(this).closest('.select').removeClass('is-active');
			selectHeader.not(this).closest('.select').find('.new-select__list').slideUp(450);

			if (select.hasClass('is-active')) {
				select.removeClass('is-active');
				select.find('.new-select__list').slideUp(450);
			} else {
				select.addClass('is-active');
				select.find('.new-select__list').slideDown(450);
			}
		})

		selectItem.on('click', function () {
			let text = $(this).text(),
				select = $(this).closest('.select'),
				currentText = select.find('.new-select'),
				input = select.find('input');
			currentText.text(text);
			input.attr('value', text);
			select.removeClass('is-active');
			select.find('.new-select__list').slideUp(450);
		})
	}
	/* ----------FORM SELECT END---------- */


	bind() {

		this.$form.find('[data-dynamic-placeholder]').each(function () {
			$(this).on('blur', function () {
				if ($(this).val() == '')
					$(this).removeClass('form_input_filled');
				else
					$(this).addClass('form_input_filled');
			})
		})

		this.$form.find('[data-required]').each((i, el) => {
			$(el).on('blur', (e) => {
				this.checkField($(e.currentTarget));
				this.checkValid();
			});

			$(el).on('change', (e) => {
				// console.log('input change');
				this.checkValid();
				// this.checkField($(e.currentTarget));
				// this.checkValid();
			});
		});

		this.$form.on('submit', (e) => {
			this.sendIfValid(e);
		});

		this.$form.on('click', 'button.disabled', function (e) {
			e.preventDefault();
			return false;
		});

		this.$policy.on('click', (e) => {
			var $el = $(e.currentTarget);

			if ($el.prop('checked'))
				$el.removeClass('_invalid');
			else
				$el.addClass('_invalid');

			this.checkValid();
		});

		// $('[data-action="form_checkbox"]').on('click', (e) => {
		// 	let $el = $(e.currentTarget);
		// 	let $input = $el.siblings('input');
		// 	console.log(1);

		// 	if (!$(e.target).hasClass('_link')) {
		// 		console.log(2);
		// 		$el.toggleClass("_active");
		// 		$input.prop("checked", !$input.prop("checked"));
		// 		e.stopImmediatePropagation();
		// 	}


		// });

		this.$formWrap.find('[data-success] [data-success-close]').on('click', (e) => {
			this.$formWrap.find('[data-success]').addClass('_hide');
		});

		this.$form.find('[data-form-privacy]').on('click', (e) => {
			let $el = $(e.currentTarget);

			if (!$(e.target).hasClass('_link')) {
				$el.toggleClass('_active');

				if ($el.hasClass('_active')) {
					this.$submitButton.removeClass('disabled');
				}
				else {
					this.$submitButton.addClass('disabled');
				}
			}
		});

	}

	checkValid() {
		this.$submitButton.removeClass('disabled');
		if (this.$form.find('.form_input_invalid').length > 0) {
			this.$submitButton.addClass('disabled');
		}
	}

	checkField($field) {
		var valid = true;
		var name = $field.attr('name');
		var pattern_email = /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i;

		if ($field.val() == '') {
			valid = false;
		} else {
			if (name === 'phone' && $field.val().indexOf('_') >= 0) {
				valid = false;
				var custom_error = 'Неверный формат телефона';
			}

			if (name === 'email' && !(pattern_email.test($field.val()))) {
				valid = false;
				var custom_error = 'Неверный формат электронной почты';
			}

			if (name === 'policy' && $field.prop('checked'))
				valid = true;
		}
		if (valid) {
			$field.removeClass('_invalid');

			if ($field.parent().find('.form_input_error').length > 0)
				$field.parent().find('.form_input_error').html('');

		} else {
			$field.addClass('_invalid');

			if (name === 'name') {
				var custom_error = 'Укажите Ваше имя';
			} else if (name === 'phone') {
				var custom_error = 'Укажите Ваш номер телефона';
			} else if (name === 'email')
				var custom_error = 'Пожалуйста, укажите почту';

			var form_error = $field.data('error') || 'Заполните поле';
			var error_message = custom_error || form_error;

			if ($field.siblings('.form_input_error').length == 0) {
				// $field.parent('.input_wrapper').append('<div class="form_input_error">' + error_message + '</div>');
				$field.parent('.form__input-wrap').append('<div class="form_input_error">' + error_message + '</div>');
			} else {
				$field.siblings('.form_input_error').html(error_message);
			}
		}
	}

	checkFields() {
		var valid = true;

		this.$form.find('[data-required]').each((i, el) => {
			this.checkField($(el));
			if ($(el).hasClass('_invalid'))
				valid = false;
		});

		if (valid) {
			this.$submitButton.removeClass('disabled');
		} else {
			this.$form.find('._invalid')[0].focus();
			this.$submitButton.addClass('disabled');
		}

		return valid;
	}

	reset() {
		this.$form[0].reset();
		this.$form.find('input').removeClass('form_input_valid form_input_filled');
	}

	beforeSend() {
		this.$submitButton.addClass('button__pending');
	}

	success(data, formType) {
		// let name = this.$formWrap.hasClass('header_form_popup_message').text();
		// var popupMessage = this.attr('name');

		//modal.append(data);
		//modal.show();
		switch (formType) {
			case 'main':
				//ym(66603799,'reachGoal','feedback');
				//dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Order', 'eventAction' : 'Feedback'});
				// gtag('event', 'static_form', {'event_category': 'send_form'});
				break;

			case 'item':
				// $('.object_book_email._form').removeClass('_form').addClass('_success');
				// ym(86538649,'reachGoal','static_form');
				//dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Order', 'eventAction' : 'Roomorder'});
				// gtag('event', 'static_form', {'event_category': 'send_form'});
				break;
			case 'header':
				// ym(86538649,'reachGoal','podobrat_zal');
				//dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Order', 'eventAction' : 'Quickorder'});
				// gtag('event', 'podobrat_zal', { 'event_category': 'send_form' });
				break;
			case 'item-reserve':
				// ym(86538649,'reachGoal','zabronirovat');
				//dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Order', 'eventAction' : 'Quickorder'});
				// gtag('event', 'zabronirovat', {'event_category': 'send_form'});
				break;
			case 'book':
				//ym(66603799,'reachGoal','roominfo');
				//dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Search', 'eventAction' : 'Roominfo'});
				// $('.object_book_email._form').removeClass('_form').addClass('_success');
				break;
		}
		this.$submitButton.removeClass('button__pending');

		// this.$formWrap.closest('.form').find('.form_wrapper').hide();
		// this.$formWrap.closest('.form').find('.form_wrapper_overlay').show();
		// this.$formWrap.closest('.form').find('.form__success').show();
		// this.$formWrap.closest('.form').find('.form__success_overlay').css({ 'display': 'flex' });
		this.$formWrap.closest('.form').addClass('_success');


		$('.contacts__form').hide();
		$('.contacts__form-success').show();

		$('.faq__form').hide();
		$('.faq__form-success').show();

		//сброс селектов
		if (this.$formWrap.closest('.form').hasClass('form-choose')) {
			this.$formWrap.closest('.form').find('input[name="location"]').attr('value', '');
			this.$formWrap.closest('.form').find('input[name="location"]').siblings('.new-select').text('Местоположение');
			this.$formWrap.closest('.form').find('input[name="disease"]').attr('value', '');
			this.$formWrap.closest('.form').find('input[name="disease"]').siblings('.new-select').text('Болезнь');
			this.$formWrap.closest('.form').find('input[name="condition"]').attr('value', '');
			this.$formWrap.closest('.form').find('input[name="condition"]').siblings('.new-select').text('Требования');
		}

		this.reset();
		// this.$formWrap.find('[data-success] [data-success-name]').text(data.payload.name);
		// this.$formWrap.find('[data-success] [data-success-phone]').text(data.payload.phone);

		this.$formWrap.find('[data-success]').removeClass('_hide');
		this.$formWrap.find('[data-success]').show();
	}

	error() {
		// this.$submitButton.removeClass('button__pending');
		//modal.showError();
	}

	sendIfValid(e) {
		e.preventDefault();
		if (!this.checkFields()) return;
		if (this.disabled) return;

		this.disabled = true;
		this.beforeSend();

		var formData = new FormData(this.$form[0]);


		var formType = this.$form.data('type');
		formData.append('type', formType);
		var formUrl = window.location.href;
		formData.append('url', formUrl);
		var formTitle = this.$form.data('title');
		formData.append('title', formTitle);
		// var cityID = $('[data-city-id]').data('city-id');
		// formData.append('cityID', cityID);

		if (this.$form.data('type') == 'listing-book') {
			var formPansionName = this.$form.data('pansion-name');
			formData.append('pansion-name', formPansionName);
			var formPansionUrl = this.$form.data('pansion-url');
			formData.append('pansion-url', formPansionUrl);
		}

		for (var pair of formData.entries()) {
			console.log(pair[0] + ', ' + pair[1]);
		}

		fetch(this.to, {
			method: 'POST',
			body: formData
		})
			.then(status)
			.then(json)
			.then(data => {
				this.success(data, formType);
				// this.reset();
				this.disabled = false;
			})
			.catch(() => {
				this.error();
				this.disabled = false;
			});
	}
}