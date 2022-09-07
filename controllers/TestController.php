<?php

namespace app\modules\pensions\controllers;

use Yii;
use common\models\GorkoApiTest;
use common\models\Subdomen;
use common\models\Restaurants;
use common\models\Rooms;
use common\models\Pages;
use common\models\SubdomenPages;
use frontend\modules\pensions\models\ElasticItems;
use yii\web\Controller;
use common\components\AsyncRenewRestaurants;
use common\models\PansionMainImage;
use common\models\FilterItems;
use common\models\PansionMain;
use common\models\PansionMainCity;
use common\models\PansionMainDistrict;
use common\models\PansionMainCondition;
use common\models\PansionMainConvenience;
use common\models\PansionMainMetro;
use common\models\PansionMainType;
use common\models\PansionMainEntertainment;
use common\models\PansionMainFeature;
use common\models\PansionMainNetwork;
use common\models\Slices;
use common\models\Pansion;
use backend\models\PansionImage;
use common\models\PansionMainImageNew;
use common\models\siteobject\SiteObject;
use common\models\siteobject\SiteObjectSeo;

class TestController extends Controller
{
	public function actionCreateSeo()
	{
		Pages::createSiteObjects();
		// $page = Pages::findWithRelations()->where(['id' => 1])->one();
		// $page->seoObject->heading = 'Хосписы для лежачих больных в Москве.';
		// $page->save();
		// echo '<pre>';
		//$page = Pages::findWithRelations()->where(['id' => 1])->one();
		// print_r($page->seoObject->heading);
		exit;
	}

	public function actionImageget()
	{
		$connection = new \yii\db\Connection([
			'username' => 'root',
			'password' => 'GxU25UseYmeVcsn5Xhzy',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pensions'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);

		$images = PansionMainImage::find()->limit(100)->all();

		foreach ($images as $key => $image) {
			$imgurl = $image->img_path;
			$imagename = basename($imgurl);
			if (file_exists('/var/www/pensions_network/frontend/web/img_d/' . $imagename)) continue;
			if (!file_exists('/var/www/pensions_network/frontend/web/img_d/' . $image->pansion_id)) {
				mkdir('/var/www/pensions_network/frontend/web/img_d/' . $image->pansion_id);
			}
			if (!file_exists('/var/www/pensions_network/frontend/web/img_d/' . $image->pansion_id . '/' . $imagename)) {
				$handle = curl_init($imgurl);
				curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
				$response = curl_exec($handle);
				$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
				curl_close($handle);
				if ($httpCode == 200)
					copy($imgurl, '/var/www/pensions_network/frontend/web/img_d/' . $image->pansion_id . '/' . $imagename);
			}
		}
		echo count($images);
		exit;
	}

	public function actionSendmessange()
	{
		$to = ['zadrotstvo@gmail.com'];
		$subj = "Тестовая заявка";
		$msg = "Тестовая заявка";
		$message = $this->sendMail($to, $subj, $msg);
		var_dump($message);
		exit;
	}

	public function actionIndex()
	{
		// $subdomen_model = Subdomen::find()
		// 	//->where(['id' => 57])
		// 	->all();

		// foreach ($subdomen_model as $key => $subdomen) {
		// 	GorkoApiTest::renewAllData([
		// 		[
		// 			'params' => 'city_id='.$subdomen->city_id.'&type_id=1&event=17',
		// 			'watermark' => '/var/www/pmnetwork/pmnetwork/frontend/web/img/ny_ball.png',
		// 			'imageHash' => 'newyearpmn'
		// 		]				
		// 	]);
		// }

		function transliterate($textcyr = null, $textlat = null)
		{
			$cyr = array(
				'ж',  'ч',  'щ',    'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я',  'э', 'ы',
				'Ж',  'Ч',  'Щ',    'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я',  'Э', 'Ы'
			);
			$lat = array(
				'zh', 'ch', 'shch', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', '',   '', 'ya', 'e', 'y',
				'Zh', 'Ch', 'Shch', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', '',   '', 'Ya', 'E', 'Y'
			);
			if ($textcyr) return str_replace($cyr, $lat, $textcyr);
			else if ($textlat) return str_replace($lat, $cyr, $textlat);
			else return null;
		}

		//Город
		// $pansion_cities = PansionMainCity::find()
		// 	// ->where(['>', 'id', 4])
		// 	->all();

		// foreach ($pansion_cities as $city) {
		// 	$test = new FilterItems();
		// 	$test->filter_id = 2;
		// 	$test->value = strtolower(transliterate($city['name']));
		// 	$test->text = $city['name'];
		// 	$test->api_arr = "{\"0\":{\"key\":\"pansion_cities.id\",\"value\":\"$city[city_id]\"}}";
		// 	$test->save();
		// }


		//Район
		/* $pansion_districts = PansionMainDistrict::find()
			// ->where(['>', 'id', 4])
			->all();

		foreach ($pansion_districts as $district) {
			$test = new FilterItems();
			$test->filter_id = 3;
			$test->value = strtolower(transliterate($district['name']));
			$test->text = $district['name'];
			$test->api_arr = "{\"0\":{\"key\":\"pansion_district.id\",\"value\":\"$district[district_id]\"}}";
			$test->save();
		} */


		//Метро
		/* $pansion_metros = PansionMainMetro::find()
			->all();

		foreach ($pansion_metros as $metro) {
			$test = new FilterItems();
			$test->filter_id = 4;
			$test->value = strtolower(transliterate($metro['name']));
			$test->text = $metro['name'];
			$test->api_arr = "{\"0\":{\"key\":\"pansion_metro.id\",\"value\":\"$metro[metro_id]\"}}";
			$test->save();
		} */


		//Заболевание или диагноз
		/* $pansion_conditions = PansionMainCondition::find()
			->all();

		foreach ($pansion_conditions as $condition) {
			$test = new FilterItems();
			$test->filter_id = 5;
			$test->value = strtolower(transliterate($condition['name']));
			$test->text = $condition['name'];
			$test->api_arr = "{\"0\":{\"key\":\"pansion_conditions.id\",\"value\":\"$condition[condition_id]\"}}";
			$test->save();
		} */


		//Тип
		/* $pansion_types = PansionMainType::find()
			->all();

		foreach ($pansion_types as $type) {
			$test = new FilterItems();
			$test->filter_id = 6;
			$test->value = strtolower(transliterate($type['name']));
			$test->text = $type['name'];
			$test->api_arr = "{\"0\":{\"key\":\"pansion_type.id\",\"value\":\"$type[type_id]\"}}";
			$test->save();
		} */


		//Особенности
		/* $pansion_entertainments = PansionMainEntertainment::find()
			->all();
		foreach ($pansion_entertainments as $entertainment) {
			$test = new FilterItems();
			$test->filter_id = 7;
			$test->value = strtolower(transliterate($entertainment['name']));
			$test->text = $entertainment['name'];
			$test->api_arr = "{\"0\":{\"key\":\"pansion_entertainments.id\",\"value\":\"$entertainment[entertainment_id]\"}}";
			$test->save();
		}

		$pansion_features = PansionMainFeature::find()
			->all();
		foreach ($pansion_features as $feature) {
			$test = new FilterItems();
			$test->filter_id = 7;
			$test->value = strtolower(transliterate($feature['name']));
			$test->text = $feature['name'];
			$test->api_arr = "{\"0\":{\"key\":\"pansion_features.id\",\"value\":\"$feature[feature_id]\"}}";
			$test->save();
		} */


		//Сеть
		/* $pansion_networks = PansionMainNetwork::find()
			->all();

		foreach ($pansion_networks as $network) {
			$test = new FilterItems();
			$test->filter_id = 8;
			$test->value = strtolower(transliterate($network['name']));
			$test->text = $network['name'];
			$test->api_arr = "{\"0\":{\"key\":\"pansion_network.id\",\"value\":\"$network[network_id]\"}}";
			$test->save();
		} */



		//сохранение срезов в БД
		// $cities = FilterItems::find()
		// ->where(['filter_id' => 2])
		// ->all();

		// foreach ($cities as $city) {
		// 	$test = new Slices();
		// 	$test->alias = $city['text'];
		// 	$test->params = '{"city":"' . $city['value'] . '"}';
		// 	$test->save();
		// }

		// foreach ($cities as $city) {
		// 	$test = new Pages();
		// 	$test->type = $city['text'];
		// 	$test->name = $city['text'];
		// 	$test->save();
		// }

		// echo ('<pre>');
		// print_r($cities);
		// exit;

		// $slices = Slices::find()->where(['<', 'id', 41])->all();
		// foreach ($slices as $slice) {
		// 	$slice->type = 'Город';
		// 	$slice->save();
		// }


		// $pansions_main = PansionMain::find()->all();
		// foreach ($pansions_main as $pansion_main) {
		// 	$pansion = new Pansion();
		// 	$pansion->id = $pansion_main['id'];
		// 	$pansion->pansion_id = $pansion_main['pansion_id'];
		// 	$pansion->save();
		// }

		//добавление картинок с сайтов vse-pansiony.ru и vse-pansionaty.ru
		// $images_new = PansionMainImageNew::find()->where(['>', 'id', '2074'])->all();
		/* foreach ($images_new as $image) {
			$test = new PansionMainImage;
			$test->pansion_id = $image['pansion_id'];
			$test->img_path = $image['img_path'];
			$test->save();
		} */


		//сохранение срезов Главный тип + Город
		// $main_types = Slices::find()
		// 	->where(['type' => 'Тип заведения'])
		// 	->all();

		// $cities = Slices::find()
		// 	->where(['type' => 'Город'])
		// 	->all();

		// foreach ($main_types as $key => $main_type) {
		// 	$type_params = json_decode($main_type->params, true);

		// 	foreach ($cities as $key => $city) {
		// 		$city_params = json_decode($city->params, true);
		// 		$test = new Slices;
		// 		$test->type = "$main_type->type + $city->type";
		// 		$test->alias = "$main_type->alias-в-$city->h1";
		// 		$test->params = '{"pansion_types":"' . $type_params["pansion_types"] . '", "city":"' . $city_params["city"] . '"}';
		// 		$test->save();
		// 	}
		// }

		//сохранение страниц Главный тип + Город
		// $cities_types = Slices::find()
		// 	->where(['type' => 'Тип заведения + Город'])
		// 	->all();

		// foreach ($cities_types as $key => $value) {
		// 	$test = new Pages;
		// 	$test->type = $value->alias;
		// 	$test->name = $value->alias;
		// 	$test->save();
		// }

		//сохранение срезов Главный тип + Цена
		// $main_types = Slices::find()
		// 	->where(['type' => 'Тип заведения'])
		// 	->all();

		// $prices = Slices::find()
		// 	->where(['type' => 'Цена'])
		// 	->all();

		// foreach ($main_types as $key => $main_type) {
		// 	$type_params = json_decode($main_type->params, true);

		// 	foreach ($prices as $key => $price) {
		// 		$price_params = json_decode($price->params, true);

		// 		$test = new Slices;
		// 		$test->type = "$main_type->type + $price->type";
		// 		$test->alias = "$main_type->alias-$price->alias";
		// 		$test->params = '{"pansion_types":"' . $type_params["pansion_types"] . '", "price":"' . $price_params["price"] . '"}';
		// 		$test->save();
		// 	}
		// }

		//сохранение страниц Главный тип + Цена
		// $prices_types = Slices::find()
		// 	->where(['type' => 'Тип заведения + Цена'])
		// 	->all();

		// foreach ($prices_types as $key => $value) {
		// 	$test = new Pages;
		// 	$test->type = $value->alias;
		// 	$test->name = $value->alias;
		// 	$test->save();
		// }


		//сохранение срезов Главный тип + Тип
		// $main_types = Slices::find()
		// 	->where(['type' => 'Тип заведения'])
		// 	->all();

		// $types = Slices::find()
		// 	->where(['type' => 'Тип'])
		// 	->all();

		// foreach ($main_types as $key => $main_type) {
		// 	$type_params = json_decode($main_type->params, true);

		// 	foreach ($types as $key => $type) {
		// 		$params = json_decode($type->params, true);

		// 		$test = new Slices;
		// 		$test->type = "$main_type->type + $type->type";
		// 		$test->alias = "$main_type->alias-$type->alias";
		// 		$test->params = '{"pansion_types":"' . $type_params["pansion_types"] . '", "type":"' . $params["type"] . '"}';
		// 		$test->save();
		// 	}
		// }

		//сохранение страниц Главный тип + Тип
		// $main_types_types = Slices::find()
		// 	->where(['type' => 'Тип заведения + Тип'])
		// 	->all();

		// foreach ($main_types_types as $key => $value) {
		// 	$test = new Pages;
		// 	$test->type = $value->alias;
		// 	$test->name = $value->alias;
		// 	$test->save();
		// }


		//добавление SEO для страниц Главный тип + Город
		// 76-355
		/* 	$typesList = [
			'Хосписы' => 'хосписов',
			'Пансионаты' => 'пансионатов',
			'Дома престарелых' => 'домов престарелых',
			'Дома интернаты' => 'домов интернатов',
			'Реабилитационные центры' => 'реабилитационных центров',
			'Стационары' => 'стационаров',
			'Паллиативные центры' => 'паллиативных центров',
		];
		$pages = Pages::find()->where(['between', 'id', 76, 355])->all();
		foreach ($pages as $page) {
			$names_arr = explode('-в-',$page['type']);
			$names_arr[0] = str_replace('-', ' ', $names_arr[0]);
			$site_object = SiteObject::find()->where(['table_name' => 'pages', 'row_id' => $page['id']])->one();
			$object_seo = SiteObjectSeo::find()->where(['site_object_id' => $site_object['id']])->one();

			$object_seo->heading = "$names_arr[0] для пожилых в $names_arr[1]";
			$object_seo->description = 'Каталог лучших ' . $typesList[$names_arr[0]] .' для пожилых в '.$names_arr[1].' ◾ &#127973;Адреса на карте ◾ Бесплатное бронирование ◾ ⭐Рейтинги учреждений, отзывы и цены';
			$object_seo->title = "$names_arr[0] для пожилых в $names_arr[1] с ценами";
			$object_seo->save();
		} */

		//добавление SEO для страниц Главный тип + Цена
		// 356-369
		// $typesList = [
		// 	'Хосписы' => 'хосписов',
		// 	'Пансионаты' => 'пансионатов',
		// 	'Дома престарелых' => 'домов престарелых',
		// 	'Дома интернаты' => 'домов интернатов',
		// 	'Реабилитационные центры' => 'реабилитационных центров',
		// 	'Стационары' => 'стационаров',
		// 	'Паллиативные центры' => 'паллиативных центров',
		// 	'Недорогие' => 'до 1000₽',
		// 	'Дорогие' => 'от 1000₽',
		// ];
		// $pages = Pages::find()->where(['between', 'id', 356, 369])->all();
		// foreach ($pages as $page) {
		// 	$names_arr = explode(' ',$page['name']);

		// 	$names_arr[0] = str_replace('-', ' ', $names_arr[0]);
		// 	$site_object = SiteObject::find()->where(['table_name' => 'pages', 'row_id' => $page['id']])->one();
		// 	$object_seo = SiteObjectSeo::find()->where(['site_object_id' => $site_object['id']])->one();

		// 	if ($names_arr[1] == 'Недорогие') {
		// 		$object_seo->heading = $names_arr[0].' в Москве и Московской области '. $typesList[$names_arr[1]];
		// 		$object_seo->description = 'Каталог недорогих '. $typesList[$names_arr[0]] .' для пожилых людей в Москве и Московской области ◾ &#127973;Адреса на карте ◾ Бесплатное бронирование ◾ ⭐Рейтинги учреждений, отзывы и цены';
		// 		$object_seo->title = $names_arr[0] .' '. mb_strtolower($names_arr[0]).' в Москве и Московской области до 1000₽ за сутки';
		// 	} else {
		// 		$object_seo->heading = 'Элитные '.mb_strtolower($names_arr[0]).' в Москве и Московской области '. $typesList[$names_arr[1]];
		// 		$object_seo->description = 'Каталог элитных '. $typesList[$names_arr[0]] .' для пожилых людей в Москве и Московской области ◾ &#127973;Адреса на карте ◾ Бесплатное бронирование ◾ ⭐Рейтинги учреждений, отзывы и цены';
		// 		$object_seo->title = 'Элитные '. mb_strtolower($names_arr[0]).' в Москве и Московской области до 1000₽ за сутки';
		// 	}
		// 	$object_seo->save();
		// }


		//добавление SEO для страниц Главный тип + Тип
		// id:370-383
		// $typesList = [
		// 	'Хосписы' => 'хосписов',
		// 	'Пансионаты' => 'пансионатов',
		// 	'Дома престарелых' => 'домов престарелых',
		// 	'Дома интернаты' => 'домов интернатов',
		// 	'Реабилитационные центры' => 'реабилитационных центров',
		// 	'Стационары' => 'стационаров',
		// 	'Паллиативные центры' => 'паллиативных центров',
		// ];
		// $pages = Pages::find()->where(['between', 'id', 370, 383])->all();
		// foreach ($pages as $page) {
		// 	$names_arr = explode(' ',$page['name']);

		// 	$names_arr[0] = str_replace('-', ' ', $names_arr[0]);
		// 	$site_object = SiteObject::find()->where(['table_name' => 'pages', 'row_id' => $page['id']])->one();
		// 	$object_seo = SiteObjectSeo::find()->where(['site_object_id' => $site_object['id']])->one();

		// 	if ($names_arr[1] == 'Государственные') {
		// 		$object_seo->heading = 'Государственные '.mb_strtolower($names_arr[0]).' для пожилых в Москве и Московской области';
		// 		$object_seo->description = 'Каталог государственных '. $typesList[$names_arr[0]] .' для пожилых людей в Москве и Московской области ◾ &#127973;Адреса на карте ◾ Бесплатное бронирование ◾ ⭐Рейтинги учреждений, отзывы и цены';
		// 		$object_seo->title = 'Государственные '.mb_strtolower($names_arr[0]).' для пожилых в Москве и Московской области с ценами';
		// 	} else {
		// 		$object_seo->heading = 'Частные '.mb_strtolower($names_arr[0]).' для пожилых в Москве и Московской области';
		// 		$object_seo->description = 'Каталог частных '. $typesList[$names_arr[0]] .' для пожилых людей в Москве и Московской области ◾ &#127973;Адреса на карте ◾ Бесплатное бронирование ◾ ⭐Рейтинги учреждений, отзывы и цены';
		// 		$object_seo->title = 'Частные (платные) '.mb_strtolower($names_arr[0]).' для пожилых в Москве и Московской области с ценами';
		// 	}
		// 	$object_seo->save();
		// }


		//добавление дефолтной сортировки для изображений пансионата
		// $pansions = Pansion::find()->all();
		// foreach ($pansions as $key => $pansion) {
		// 	$images = PansionImage::find()->where(['pansion_id' => $pansion['pansion_id']])->all();

		// 	foreach ($images as $key => $image) {
		// 		$image->img_sort = $key;
		// 		$image->save();
		// 	}
		// }

		//добавление названий пансионата в локальную таблицу
		// $pansions_main = PansionMain::find()->all();
		// foreach ($pansions_main as $key => $pansion_main) {
		// 	$pansion = Pansion::find()->where(['pansion_id' => $pansion_main['pansion_id'] ])->one();
		// 	$pansion->name =  $pansion_main['name'];
		// 	$pansion->save();
		// }

		//создание срезов для "Заболеваний"
		// $filter_items = FilterItems::find()->where(['filter_id' => 5])->all();
		// foreach ($filter_items as $key => $filter_item) {
		// 	$filter_item->value = str_replace(' ', '_', strtolower(transliterate($filter_item['text'])));
		// 	$filter_item->save();
		// }

		// $diseases = FilterItems::find()->where(['filter_id' => 5])->all();
		// foreach ($diseases as $disease) {
		// 	$test = new Slices();
		// 	$test->type = 'Заболевание';
		// 	$test->alias = $disease['text'];
		// 	$test->params = '{"disease":"' . $disease['value'] . '"}';
		// 	$test->save();
		// }

		//сохранение страниц Заболевание
		/* $diseases = Slices::find()
			->where(['type' => 'Заболевание'])
			->all();

		foreach ($diseases as $key => $value) {
			$test = new Pages;
			$test->type = $value->alias;
			$test->name = $value->alias;
			$test->save();
		} */

		//добавление SEO для страниц Заболевание
		// 385-395
		// $pages = Pages::find()->where(['between', 'id', 385, 395])->all();
		// $slices = Slices::find()->where(['type' => 'Заболевание'])->all();

		// foreach ($pages as $key => $page) {
		// 	$disease = $slices[$key]['h1'];
		// 	$site_object = SiteObject::find()->where(['table_name' => 'pages', 'row_id' => $page['id']])->one();
		// 	$object_seo = SiteObjectSeo::find()->where(['site_object_id' => $site_object['id']])->one();

		// 	$object_seo->heading = "Хосписы для пожилых $disease";
		// 	$object_seo->description = "Каталог хосписов для пожилых людей $disease в Москве и МО ◾ &#127973;Адреса на карте ◾ Бесплатное бронирование ◾ ⭐Рейтинги учреждений, отзывы и цены";
		// 	$object_seo->title = "Хосписы для пожилых $disease в Москве и Московской области с ценами";
		// 	$object_seo->save();
		// }

		//сохранение срезов Главный тип + Заболевание
		/* $main_types = Slices::find()
			->where(['type' => 'Тип заведения'])
			->all();

		$diseases = Slices::find()
			->where(['type' => 'Заболевание'])
			->all();

		foreach ($main_types as $key => $main_type) {
			$type_params = json_decode($main_type->params, true);

			foreach ($diseases as $key => $disease) {
				$params = json_decode($disease->params, true);
				$disease_name = str_replace(' ', '-', $disease->h1);

				$test = new Slices;
				$test->type = "$main_type->type + $disease->type";
				$test->alias = "$main_type->alias-для-пожилых-$disease_name";
				$test->h1 = "$main_type->alias для пожилых $disease->h1";
				$test->params = '{"pansion_types":"' . $type_params["pansion_types"] . '", "disease":"' . $params["disease"] . '"}';
				$test->save();
			}
		} */

		//сохранение страниц Главный тип + Заболевание
		/* $main_types_diseases = Slices::find()
			->where(['type' => 'Тип заведения + Заболевание'])
			->all();

		foreach ($main_types_diseases as $key => $value) {
			$test = new Pages;
			$test->type = $value->alias;
			$test->name = $value->h1;
			$test->save();
		} */

		//добавление SEO для страниц Главный тип + Заболевание
		// 396-472
		/* $typesList = [
			'Хосписы' => 'хосписов',
			'Пансионаты' => 'пансионатов',
			'Дома престарелых' => 'домов престарелых',
			'Дома интернаты' => 'домов интернатов',
			'Реабилитационные центры' => 'реабилитационных центров',
			'Стационары' => 'стационаров',
			'Паллиативные центры' => 'паллиативных центров',
		];
		$pages = Pages::find()->where(['between', 'id', 396, 472])->all();
		foreach ($pages as $page) {
			$types_arr = explode(' ', $page['name']);
			$types_arr[0] = str_replace('-', ' ', $types_arr[0]);
			$disease_arr = explode(' для пожилых ', $page['name']);
			$site_object = SiteObject::find()->where(['table_name' => 'pages', 'row_id' => $page['id']])->one();
			$object_seo = SiteObjectSeo::find()->where(['site_object_id' => $site_object['id']])->one();

			$object_seo->heading = str_replace('-', ' ', $page['name']);
			$object_seo->description = 'Каталог лучших ' . $typesList[$types_arr[0]] .' для пожилых людей '.$disease_arr[1].' в Москве и МО ◾ &#127973;Адреса на карте ◾ Бесплатное бронирование ◾ ⭐Рейтинги учреждений, отзывы и цены';
			$object_seo->title = str_replace('-', ' ', $page['name']) . " в Москве и Московской области с ценами";
			$object_seo->save();
		} */



		echo 111;
	}

	public function actionAll()
	{
		$subdomen_model = Subdomen::find()
			->where(['id' => 57])
			->all();

		foreach ($subdomen_model as $key => $subdomen) {
			GorkoApiTest::showAllData([
				[
					'params' => 'city_id=' . $subdomen->city_id . '&type_id=1&event=17',
					'watermark' => '/var/www/pmnetwork/pmnetwork/frontend/web/img/ny_ball.png',
					'imageHash' => 'newyearpmn'
				]
			]);
		}
	}

	public function actionOne()
	{
		$queue_id = Yii::$app->queue->push(new AsyncRenewRestaurants([
			'gorko_id' => 418147,
			'dsn' => Yii::$app->db->dsn,
			'watermark' => '/var/www/pmnetwork/pmnetwork/frontend/web/img/ny_ball.png',
			'imageHash' => 'newyearpmn'
		]));
	}

	public function actionTest()
	{
		GorkoApiTest::showOne([
			[
				'params' => 'city_id=4088&type_id=1&type=30,11,17,14&is_edit=1',
				'watermark' => '/var/www/pmnetwork/pmnetwork/frontend/web/img/ny_ball.png'
			]
		]);
	}

	public function actionSubdomencheck()
	{
		$subdomen_model = Subdomen::find()->all();

		foreach ($subdomen_model as $key => $subdomen) {
			$restaurants = Restaurants::find()->where(['city_id' => $subdomen->city_id])->all();
			if (count($restaurants) > 9) {
				$subdomen->active = 1;
			} else {
				$subdomen->active = 0;
			}
			$subdomen->save();
		}
	}

	public function actionRenewelastic()
	{
		ElasticItems::refreshIndex();
	}

	public function actionSoftrenewelastic()
	{
		ElasticItems::softRefreshIndex();
	}

	public function actionCreateindex()
	{
		ElasticItems::softRefreshIndex();
	}

	public function actionTetest()
	{
		$room_where = [
			'rooms.active' => 1,
			'restaurants.city_id' => 4400
		];
		$current_room_models = Rooms::find()
			->joinWith('restaurants')
			->select('rooms.gorko_id')
			->where($room_where)
			->asArray()
			->all();

		print_r(count($current_room_models));
		exit;
	}

	public function actionImgload()
	{
		//header("Access-Control-Allow-Origin: *");
		$curl = curl_init();
		$file = '/var/www/pmnetwork/pmnetwork_konst/frontend/web/img/favicon.png';
		$mime = mime_content_type($file);
		$info = pathinfo($file);
		$name = $info['basename'];
		$output = curl_file_create($file, $mime, $name);
		$params = [
			//'mediaId' => 55510697,
			'url' => 'https://lh3.googleusercontent.com/XKtdffkbiqLWhJAWeYmDXoRbX51qNGOkr65kMMrvhFAr8QBBEGO__abuA_Fu6hHLWGnWq-9Jvi8QtAGFvsRNwqiC',
			'token' => '4aD9u94jvXsxpDYzjQz0NFMCpvrFQJ1k',
			'watermark' => $output,
			'hash_key' => 'svadbanaprirode'
		];
		curl_setopt($curl, CURLOPT_URL, 'https://api.gorko.ru/api/v2/tools/mediaToSatellite');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_ENCODING, '');
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);


		echo '<pre>';
		$response = curl_exec($curl);

		print_r(json_decode($response));
		curl_close($curl);

		//echo '<pre>';

		//echo '<pre>';





	}

	private function sendMail($to, $subj, $msg)
	{
		$message = Yii::$app->mailer->compose()
			->setFrom(['svadbanaprirode@yandex.ru' => 'Свадьба на природе'])
			->setTo($to)
			->setSubject($subj)
			//->setTextBody('Plain text content')
			->setHtmlBody($msg);
		//echo '<pre>';
		//print_r($message);
		//exit;
		if (count($_FILES) > 0) {
			foreach ($_FILES['files']['tmp_name'] as $k => $v) {
				$message->attach($v, ['fileName' => $_FILES['files']['name'][$k]]);
			}
		}
		return $message->send();
	}
}
