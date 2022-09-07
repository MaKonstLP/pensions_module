<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\modules\pensions\assets\AppAsset;
use common\models\Subdomen;
use common\models\blog\BlogPost;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">
	<link rel="preload" href="/fonts/GolosText-Regular.woff" as="font" type="font/woff" crossorigin>
	<link rel="preload" href="/fonts/GolosText-Medium.woff" as="font" type="font/woff" crossorigin>
	<link rel="preload" href="/fonts/GolosText-DemiBold.woff" as="font" type="font/woff" crossorigin>
	<!-- <link rel="icon" type="image/png" href="/img/ny_ball.png"> -->
	<title><?php echo $this->title ?></title>
	<?php $this->head() ?>
	<?php if (!empty($this->params['desc'])) echo "<meta name='description' content='" . $this->params['desc'] . "'>"; ?>
	<?php if (!empty($this->params['kw'])) echo "<meta name='keywords' content='" . $this->params['kw'] . "'>"; ?>
	<?php if (!empty($this->params['robots'])) echo "<meta name='robots' content='noindex'/>"; ?>
	<?= Html::csrfMetaTags() ?>
	<!-- Google Tag Manager -->
	<script>
		(function(w, d, s, l, i) {
			w[l] = w[l] || [];
			w[l].push({
				'gtm.start': new Date().getTime(),
				event: 'gtm.js'
			});
			var f = d.getElementsByTagName(s)[0],
				j = d.createElement(s),
				dl = l != 'dataLayer' ? '&l=' + l : '';
			j.async = true;
			j.src =
				'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
			f.parentNode.insertBefore(j, f);
		})(window, document, 'script', 'dataLayer', 'GTM-NTGCPCP');
	</script>
	<!-- End Google Tag Manager -->

</head>

<body>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NTGCPCP" height="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	<?php $this->beginBody() ?>

	<div class="main_wrap">

		<header class="header">
			<div class="header__top">
				<div class="header__top-conatiner container">
					<a href="/" class="header__logo logo">
						<div class="logo__img">
							<img src="/image/icons/logo.svg" alt="">
						</div>
						<p class="logo__text">учреждения <span>для лежачих больных</span></p>
					</a>
					<ul class="header__top-menu" data-da="header__bot-container,1,1200">
						<li class="header__top-menu-item">
							<a href="/О-нас/">О нас</a>
						</li>
						<li class="header__top-menu-item">
							<a href="/Вопросы-ответы/">Вопросы-ответы</a>
						</li>
						<li class="header__top-menu-item">
							<a href="/Контакты/">Контакты</a>
						</li>
					</ul>
					<div class="header__top-phone">
						<a href="tel:+74999386439">+7 (499) 938 64 39</a>
						<span>Бесплатная консультация 24/7</span>
					</div>
					<div class="header__top-callback _btn" data-callback>Заказать звонок</div>
					<div class="header__top-burger" data-header-top-burger>
						<span></span>
						<span></span>
						<span></span>
					</div>
				</div>
			</div>

			<div class="header__bot">
				<div class="header__bot-container container">
					<ul class="header__bot-menu">
						<li class="header__bot-menu-item">
							<a href="/Хосписы/">Хосписы</a>
						</li>
						<li class="header__bot-menu-item">
							<a href="/Пансионаты/">Пансионаты</a>
						</li>
						<li class="header__bot-menu-item">
							<a href="/Дома-престарелых/">Дома престарелых</a>
						</li>
						<li class="header__bot-menu-item">
							<a href="/Дома-интернаты/">Дома интернаты</a>
						</li>
						<li class="header__bot-menu-item">
							<a href="/Реабилитационные-центры/">Реабилитационные центры</a>
						</li>
						<li class="header__bot-menu-item">
							<a href="/Стационары/">Стационары</a>
						</li>
						<li class="header__bot-menu-item">
							<a href="/Паллиативные-центры/">Паллиативные центры</a>
						</li>
						<li class="header__bot-menu-item header__bot-menu-item_service" data-header-submenu-burger>
							<span>Услуги</span>
							<div class="header__bot-menu-burger">
								<span></span>
								<span></span>
								<span></span>
							</div>
							<ul class="header__bot-submenu">
								<?php $services = BlogPost::find()->where(['published' => true, 'type' => 2])->all(); ?>
								<?php foreach ($services as $service) : ?>
									<li class="header__bot-submenu-item">
										<a href="/Услуги/<?= $service['alias'] ?>"><?= $service['name'] ?></a>
									</li>
								<?php endforeach; ?>
								<!-- <li class="header__bot-submenu-item">
									<a href="/service/test">Болезнь Альцгеймера</a>
								</li> -->
							</ul>
						</li>
					</ul>
					<div class="header__bot-phone">
						<a href="tel:+74999386439">+7 (499) 938 64 39</a>
						<span>Бесплатная консультация 24/7</span>
					</div>
					<div class="header__bot-callback _btn" data-callback>Заказать звонок</div>
				</div>
			</div>
		</header>

		<div class="content_wrap">
			<?= $content ?>
		</div>

		<footer class="footer">
			<div class="footer__top">
				<div class="footer__top-container container">
					<a href="/" class="footer__logo logo">
						<div class="logo__img">
							<img src="/image/icons/logo.svg" alt="">
						</div>
						<p class="logo__text">учреждения <span>для лежачих больных</span></p>
					</a>
					<div class="footer__top-phone">
						<p class="footer__top-phone-label">Телефон горячей линии:</p>
						<a href="tel:+74999386439">+7 (499) 938 64 39</a>
					</div>
					<div class="footer__top-mail">
						<p class="footer__top-mail-label">Техническая поддержка:</p>
						<a href="mailto:hospices@yandex.ru">hospices@yandex.ru</a>
					</div>
					<div class="footer__top-callback _btn" data-callback>Заказать звонок</div>
				</div>
			</div>

			<div class="footer__bot">
				<div class="footer__bot-container container">
					<ul class="footer__bot-list">
						<li class="footer__bot-item">
							<a href="/Хосписы/">Хосписы</a>
						</li>
						<li class="footer__bot-item">
							<a href="/Пансионаты/">Пансионаты</a>
						</li>
						<li class="footer__bot-item">
							<a href="/Дома-престарелых/">Дома престарелых</a>
						</li>
						<li class="footer__bot-item">
							<a href="/Дома-интернаты/">Дома-интернаты</a>
						</li>
						<li class="footer__bot-item">
							<a href="/Стационары/">Стационары</a>
						</li>
						<li class="footer__bot-item">
							<a href="/Паллиативные-центры/">Паллиативные центры</a>
						</li>
						<li class="footer__bot-item">
							<a href="/Реабилитационные-центры/">Реабилитационные центры</a>
						</li>
						<li class="footer__bot-item">
							<a href="/Услуги/">Услуги</a>
						</li>
						<li class="footer__bot-item">
							<a href="/Статьи/">Статьи</a>
						</li>
						<li class="footer__bot-item">
							<a href="/Контакты/">Контакты</a>
						</li>
					</ul>
					<div class="footer__bot-logos">
						<div class="footer__bot-soc-logo footer__bot-logo">
							<img src="/image/icons/soc_msk_logo.svg" alt="">
						</div>
						<div class="footer__bot-zdrav-logo footer__bot-logo">
							<img src="/image/icons/msk_zdrav_logo.svg" alt="">
						</div>
						<div class="footer__bot-nadzor-logo footer__bot-logo">
							<img src="/image/icons/msk_nadzor_logo.svg" alt="">
						</div>
					</div>
					<div class="footer__bot-links">
						<p>© 2022 Все права защищены</p>
						<a href="/user-agreement/">Пользовательское соглашение</a>
						<a href="/privacy-policy/">Политика конфиденциальности</a>
					</div>
				</div>
			</div>
		</footer>

		<?= $this->render('//components/generic/form_callback.twig') ?>
	</div>

	<?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>