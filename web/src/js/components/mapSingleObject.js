"use strict";

export default class YaMapSingleObject {
	constructor() {
		let self = this;
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

	init() {
		this.script('//api-maps.yandex.ru/2.1/?lang=ru_RU').then(() => {
			const ymaps = global.ymaps;
			ymaps.ready(function () {
				let map = document.querySelector(".map");
				let myMap = new ymaps.Map(map, { center: [55.76, 37.64], zoom: 15, controls: [] },
					{ suppressMapOpenBlock: true });

				myMap.behaviors.disable('scrollZoom');

				let zoomControl = new ymaps.control.ZoomControl({
					options: {
						size: "small",
						position: {
							top: 10,
							right: 10
						}

					}
				});

				let geolocationControl = new ymaps.control.GeolocationControl({
					options: {
						noPlacemark: true,
						position: {
							top: 10,
							left: 10
						}
					}
				});

				myMap.controls.add(zoomControl);
				myMap.controls.add(geolocationControl);

				let objectCoordinates = [$("#map").attr("data-mapDotX"), $("#map").attr("data-mapDotY")];
				let myBalloonHeader = $("#map").attr("data-name");
				let myBalloonBody = $("#map").attr("data-address");
				// let myBalloonCapacity = $("#map").attr("data-capacity");
				// let myBalloonImage = $("#map").attr("data-image");
				// let myBalloonLowestPrice = $("#map").attr("data-lowest-price");

				let myBalloonLayout = ymaps.templateLayoutFactory.createClass(
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
				}
				);

				let object = new ymaps.Placemark(objectCoordinates, {
					balloonContentHeader: myBalloonHeader,
					balloonContentBody: myBalloonBody,
					// balloonContentCapacity: myBalloonCapacity,
					// balloonContentImage: myBalloonImage,
					// balloonContentLowestPrice: myBalloonLowestPrice
				}, {
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

					balloonLayout: myBalloonLayout,
					hideIconOnBalloonOpen: false,
					balloonOffset: [0, 0],
				});

				myMap.geoObjects.add(object);
				myMap.setCenter(objectCoordinates);
				object.balloon.open("", "", { closeButton: false });

			});
		});
	}
}