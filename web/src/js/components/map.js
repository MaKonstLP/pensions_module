"use strict";
import Filter from './filter';

export default class YaMapAll {
	constructor(filter) {
		let self = this;
		var fired = false;
		this.filter = filter;

		this.myMap = false;
		this.objectManager = false;
		this.myBalloonLayout = false;
		this.myBalloonContentLayout = false;

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

		function load_other() {
			setTimeout(function () {
				self.init();
			}, 100);

		}
	}

	script(url) {
		if (Array.isArray(url)) {
			let self = this;
			let prom = [];
			url.forEach(function (item) {
				prom.push(self.script(item));
			});
			return Promise.all(prom);
		}

		return new Promise(function (resolve, reject) {
			let r = false;
			let t = document.getElementsByTagName('script')[0];
			let s = document.createElement('script');

			s.type = 'text/javascript';
			s.src = url;
			s.async = true;
			s.onload = s.onreadystatechange = function () {
				if (!r && (!this.readyState || this.readyState === 'complete')) {
					r = true;
					resolve(this);
				}
			};
			s.onerror = s.onabort = reject;
			t.parentNode.insertBefore(s, t);
		});
	}





	refresh(filter) {
		let self = this;
		let data = {
			subdomain_id: $('[data-map-api-subid]').data('map-api-subid'),
			filter: JSON.stringify(filter.state)
		};

		$.ajax({
			type: "POST",
			url: "/api/map_all/",
			data: data,
			success: function (response) {
				let serverData = response;

				self.objectManager = new ymaps.ObjectManager(
					{
						geoObjectBalloonLayout: self.myBalloonLayout,
						geoObjectBalloonContentLayout: self.myBalloonContentLayout,
						geoObjectHideIconOnBalloonOpen: false,
						geoObjectBalloonOffset: [0, 0],
						clusterize: true,
						clusterDisableClickZoom: false,
						clusterBalloonItemContentLayout: self.myBalloonContentLayout,
						clusterIconColor: "#2B34B9",
						// geoObjectIconColor: "#cccccc",

						// Кастомная иконка на карте START
						// iconColor: "green",
						// Необходимо указать данный тип макета.
						geoObjectIconLayout: 'default#image',
						// Своё изображение иконки метки.
						geoObjectIconImageHref: '/image/icons/map_geo_icon.svg',
						// Размеры метки.
						geoObjectIconImageSize: [30, 40],
						// Смещение левого верхнего угла иконки относительно
						// её "ножки" (точки привязки).
						geoObjectIconImageOffset: [-15, -35],
						// Кастомная иконка на карте END
					});
				self.objectManager.add(serverData);
				self.myMap.geoObjects.removeAll();
				self.myMap.geoObjects.add(self.objectManager);
				self.myMap.setBounds(self.objectManager.getBounds());
			},
			error: function (response) {
			}
		});
	}





	showRestaurantOnMap(pansionCoordinates, pansionMyBalloonHeader, pansionMyBalloonBody, pansionId, pansionSlug) {
		let self = this;

		self.objectCoordinates = pansionCoordinates;
		self.myBalloonHeader = pansionMyBalloonHeader;
		self.myBalloonBody = pansionMyBalloonBody;
		// self.myBalloonCapacity = restaurantMyBalloonCapacity;
		// self.myBalloonImage = restaurantMyBalloonImage;
		// self.myBalloonLowestPrice = restaurantMyBalloonLowestPrice;
		self.myBalloonSlug = pansionSlug;
		self.myBalloonId = pansionId;


		console.log(self.objectCoordinates);

		self.myBalloonLayout = ymaps.templateLayoutFactory.createClass(
			`<div class="balloon_layout">
				<a class="close" href="#">
					<div></div>
					<div></div>
				</a>
				<div class="arrow"></div>
				<div class="balloon_inner">
					<div class="balloon_wrapper">
						<div class="balloon_content">
							<div class="balloon_text">

								<div class="balloon_header">
									{{properties.balloonContentHeader}}
								</div>

								<div class="balloon_address">
									<span>{{properties.balloonContentBody}}</span>
								</div>

								<a href="/Каталог/{{properties.balloonContentSlug}}/" class="balloon_btn _btn-transparent">Подробнее</a>

							</div>
						</div>
					</div>
				</div>
			</div>`, {
			build: function () {
				this.constructor.superclass.build.call(this);

				this._$element = $('.balloon_layout', this.getParentElement());

				this._$element.find('.close')
					.on('click', $.proxy(this.onCloseClick, this));
			},

			clear: function () {
				this._$element.find('.close')
					.off('click');

				this.constructor.superclass.clear.call(this);
			},

			onCloseClick: function (e) {
				e.preventDefault();

				this.events.fire('userclose');
			},

			getShape: function () {
				if (!this._isElement(this._$element)) {
					return myBalloonLayout.superclass.getShape.call(this);
				}

				var position = this._$element.position();

				return new ymaps.shape.Rectangle(new ymaps.geometry.pixel.Rectangle([
					[position.left, position.top], [
						position.left + this._$element[0].offsetWidth,
						position.top + this._$element[0].offsetHeight + this._$element.find('.arrow')[0].offsetHeight
					]
				]));
			},

			_isElement: function (element) {
				return element && element[0] && element.find('.arrow')[0];
			}
		});

		self.object = new ymaps.Placemark(self.objectCoordinates, {
			balloonContentHeader: self.myBalloonHeader,
			balloonContentBody: self.myBalloonBody,
			// balloonContentCapacity: self.myBalloonCapacity,
			// balloonContentImage: self.myBalloonImage,
			// balloonContentLowestPrice: self.myBalloonLowestPrice,
			balloonContentSlug: self.myBalloonSlug,
			balloonContentId: self.myBalloonId,
		}, {
			// iconColor: "green",

			// Кастомная иконка на карте START
			// iconColor: "green",
			// Необходимо указать данный тип макета.
			iconLayout: 'default#image',
			// Своё изображение иконки метки.
			iconImageHref: '/image/icons/map_geo_icon.svg',
			// Размеры метки.
			iconImageSize: [30, 40],
			// Смещение левого верхнего угла иконки относительно
			// её "ножки" (точки привязки).
			iconImageOffset: [-15, -35],
			// Кастомная иконка на карте END

			balloonLayout: self.myBalloonLayout,
			hideIconOnBalloonOpen: false,
			balloonOffset: [0, 0],
		});

		self.myMap.geoObjects.removeAll();
		self.myMap.geoObjects.add(self.object);
		self.myMap.setCenter(self.objectCoordinates, 15);
		self.object.balloon.open("", "", { closeButton: false });
	}







	init() {
		let self = this;
		this.script('//api-maps.yandex.ru/2.1/?lang=ru_RU').then(() => {
			const ymaps = global.ymaps;

			ymaps.ready(function () {
				let map = document.querySelector(".map");
				// let myMap = new ymaps.Map(map, { center: [55.76, 37.64], zoom: 15 });
				self.myMap = new ymaps.Map(map, { center: [55.76, 37.64], zoom: 15 });
				// myMap.behaviors.disable('scrollZoom');
				self.myMap.behaviors.disable('scrollZoom');

				// let myBalloonLayout = ymaps.templateLayoutFactory.createClass(
				self.myBalloonLayout = ymaps.templateLayoutFactory.createClass(
					`<div class="balloon_layout">
						<a class="close" href="#">
							<div></div>
							<div></div>
						</a>
						<div class="arrow"></div>
						<div class="balloon_inner">
							$[[options.contentLayout]]
						</div>
					</div>`, {
					build: function () {
						this.constructor.superclass.build.call(this);

						this._$element = $('.balloon_layout', this.getParentElement());

						this._$element.find('.close')
							.on('click', $.proxy(this.onCloseClick, this));
					},

					clear: function () {
						this._$element.find('.close')
							.off('click');

						this.constructor.superclass.clear.call(this);
					},

					onCloseClick: function (e) {
						e.preventDefault();

						this.events.fire('userclose');
					},

					getShape: function () {
						if (!this._isElement(this._$element)) {
							// return myBalloonLayout.superclass.getShape.call(this);
							return self.myBalloonLayout.superclass.getShape.call(this);
						}

						var position = this._$element.position();

						return new ymaps.shape.Rectangle(new ymaps.geometry.pixel.Rectangle([
							[position.left, position.top], [
								position.left + this._$element[0].offsetWidth,
								position.top + this._$element[0].offsetHeight + this._$element.find('.arrow')[0].offsetHeight
							]
						]));
					},

					_isElement: function (element) {
						return element && element[0] && element.find('.arrow')[0];
					}
				}
				);

				// let myBalloonContentLayout = ymaps.templateLayoutFactory.createClass(
				self.myBalloonContentLayout = ymaps.templateLayoutFactory.createClass(
					`<div class="balloon_wrapper">
						<div class="balloon_content">
							<div class="balloon_text">

								<div class="balloon_header">
									{{properties.organization}}
								</div>

								<div class="balloon_address">
									<span>{{properties.address}}</span>
								</div>

								<a href="/Каталог/{{properties.pansion_slug}}" class="balloon_btn _btn-transparent">Подробнее</a>

							</div>
						</div>
					</div>`
				);

				// let objectManager = new ymaps.ObjectManager(
				self.objectManager = new ymaps.ObjectManager(
					{
						geoObjectBalloonLayout: self.myBalloonLayout,
						geoObjectBalloonContentLayout: self.myBalloonContentLayout,
						geoObjectHideIconOnBalloonOpen: false,
						geoObjectBalloonOffset: [0, 0],
						clusterize: true,
						clusterDisableClickZoom: false,
						clusterBalloonItemContentLayout: self.myBalloonContentLayout,
						clusterIconColor: "#2B34B9",
						// geoObjectIconColor: "green"

						// Кастомная иконка на карте START
						// iconColor: "green",
						// Необходимо указать данный тип макета.
						geoObjectIconLayout: 'default#image',
						// Своё изображение иконки метки.
						geoObjectIconImageHref: '/image/icons/map_geo_icon.svg',
						// Размеры метки.
						geoObjectIconImageSize: [30, 40],
						// Смещение левого верхнего угла иконки относительно
						// её "ножки" (точки привязки).
						geoObjectIconImageOffset: [-15, -35],
						// Кастомная иконка на карте END
					}
				);

				let serverData = null;
				let data = {
					subdomain_id: $('[data-map-api-subid]').data('map-api-subid'),
					filter: JSON.stringify(self.filter.state)
				};

				$.ajax({
					type: "POST",
					url: "/api/map_all/",
					data: data,
					/* success: function (response) {
						serverData = response;

						objectManager.add(serverData);
						//console.log(`objectManager length: ${objectManager.objects.getLength()}`);
						myMap.geoObjects.add(objectManager);
						//console.log(`objectManager: ${objectManager.getBounds()}`);
						myMap.setBounds(objectManager.getBounds());
					},
					error: function (response) {

					} */
					success: function (response) {
						serverData = response;
						self.objectManager.add(serverData);
						// $('.filter_submit_button').on('click', function () {
						// ym(64598434, 'reachGoal', 'map_open');
						// $(this).closest('.map_container').addClass('_active');
						// self.myMap.geoObjects.add(self.objectManager);
						// self.myMap.setBounds(self.objectManager.getBounds());
						// });
						self.myMap.geoObjects.add(self.objectManager);
						self.myMap.setBounds(self.objectManager.getBounds());
					},
					error: function (response) {
					}
				});
			});
		});
	}
}