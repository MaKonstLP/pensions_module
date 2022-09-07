<?php

namespace app\modules\pensions\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\elastic\RestaurantElastic;
use frontend\modules\pensions\components\Breadcrumbs;
use common\models\elastic\ItemsWidgetElastic;
use frontend\modules\pensions\models\ElasticItems;
use common\models\Seo;
use backend\models\Faq;
use backend\models\Review;
use frontend\components\Declension;
use frontend\components\ParamsFromQuery;

class ItemController extends Controller
{

	public function actionIndex($slug)
	{

		// return $this->render('index.twig', array());


		$elastic_model = new ElasticItems;
		$item = ElasticItems::find()->query([
			'bool' => [
				'must' => [
					['match' => ['pansion_slug' => $slug]],
				],
			]
		])->one();
		if (empty($item)) {
			throw new NotFoundHttpException();
		}

		$territory = [];
		$leisure = [];
		$service_staff = [];
		$extra_facilities = [];

		foreach ($item['pansion_conveniencies'] as $value) {
			if ($value['id'] == 12) {
				$territory[] = $value['name'];
			} elseif ($value['id'] == 8) {
				$territory[] = $value['name'];
			} elseif ($value['id'] == 2) {
				$leisure[] = $value['name'];
			} elseif ($value['id'] == 3) {
				$leisure[] = $value['name'];
			} elseif ($value['id'] == 11) {
				$leisure[] = $value['name'];
			} elseif ($value['id'] == 13) {
				$leisure[] = $value['name'];
			} elseif ($value['id'] == 4) {
				$service_staff[] = $value['name'];
			} elseif ($value['id'] == 7) {
				$service_staff[] = $value['name'];
			} elseif ($value['id'] == 9) {
				$service_staff[] = $value['name'];
			} elseif ($value['id'] == 14) {
				$service_staff[] = $value['name'];
			} elseif ($value['id'] == 15) {
				$service_staff[] = $value['name'];
			} elseif ($value['id'] == 18) {
				$service_staff[] = $value['name'];
			} elseif ($value['id'] == 17) {
				$service_staff[] = $value['name'];
			} elseif ($value['id'] == 20) {
				$service_staff[] = $value['name'];
			} elseif ($value['id'] == 10) {
				$extra_facilities[] = $value['name'];
			}
		}


		//if(!$item) exit;
		// echo ('<pre>');
		// print_r($item);
		// exit;

		//$seo = new Seo('item', 1, 0, $item, 'rest');
		//$seo = $seo->seo;
		//$this->setSeo($seo);

		//$item = ApiItem::getData($item->restaurants->gorko_id);

		//$seo['h1'] = $item->restaurant_name;
		//$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(3);
		//$seo['desc'] = $item->restaurant_name;
		//$seo['address'] = $item->restaurant_address;
		//
		//$other_rooms = $item->rooms;

		$other_pansions = ElasticItems::find()
			->limit(40)
			->query([
				'bool' => [
					// 		'must' => [
					// 			['match' => ['restaurant_district' => $item->restaurant_district]]
					// 		],
					'must_not' => [
						['match' => ['id' => $item->id]]
					],
				],
			])
			->all();

		shuffle($other_pansions);
		$other_pansions = array_slice($other_pansions, 0, 5);

		$faq = Faq::find()->all();
		shuffle($faq);
		$faq = array_slice($faq, 0, 3);

		$reviews = Review::find()->where(['active' => 1, 'pansion_id' => $item->pansion_id])->all();





		//срезы Город
		// $slice_cities = [];
		$pansion_cities = $item['pansion_cities'];
		/* if (isset($pansion_cities) && !empty($pansion_cities)) {
			foreach ($pansion_cities as $key => $city) {
				$temp = [
					'city' => $city['alias'],
					'page' => 1
				];
				$slice_url = ParamsFromQuery::isSlice($temp);
				if (!empty($slice_url)) {
					$slices_arr = [];
					$slices_arr['name'] = str_replace('-', ' ', $slice_url);
					$slices_arr['url'] = $slice_url;
					array_push($slice_cities, $slices_arr);
				}
			}
		} */



		//срезы Сеть пансионата
		$slice_network = [];
		$pansion_network = $item['pansion_network'];
		if (isset($pansion_network) && !empty($pansion_network)) {
			$slice_network['name'] = $pansion_network[0]['name'];
			$slice_network['url'] = '?web=' . $pansion_network[0]['alias'];
		}

		$slice_specials = [];
		$slice_main_type = [];
		$slice_cities = [];
		$pansion_main_type = $item['pansion_main_type'];
		if (isset($pansion_main_type) && !empty($pansion_main_type)) {
			//срез Главный тип пансионата
			$slice_main_type['name'] = $pansion_main_type['name'];
			$slice_main_type['url'] = str_replace(' ', '-', $pansion_main_type['name']);

			//срезы Главный тип + Город
			if (isset($pansion_cities) && !empty($pansion_cities)) {
				foreach ($pansion_cities as $key => $city) {
					$temp = [
						'pansion_types' => $pansion_main_type['alias'],
						'city' => $city['alias'],
						'page' => 1
					];
					$slice_url = ParamsFromQuery::isSlice($temp);
					if (!empty($slice_url)) {
						$slices_arr = [];
						$slices_arr['name'] = str_replace('-', ' ', $slice_url);
						$slices_arr['url'] = $slice_url;
						array_push($slice_cities, $slices_arr);
					}
				}
			}
			//срезы Главный тип + Заболевание
			$pansion_conditions = $item['pansion_conditions'];
			if (isset($pansion_conditions) && !empty($pansion_conditions)) {
				foreach ($pansion_conditions as $key => $condition) {
					$temp = [
						'pansion_types' => $pansion_main_type['alias'],
						'disease' => $condition['alias'],
						'page' => 1
					];
					$slice_url = ParamsFromQuery::isSlice($temp);
					if (!empty($slice_url)) {
						$slices_arr = [];
						$slices_arr['name'] = str_replace('-', ' ', $slice_url);
						$slices_arr['url'] = $slice_url;
						array_push($slice_specials, $slices_arr);
					}
				}
			}
			//срезы Главный тип + Цена
			if (isset($item['pansion_price']) && !empty($item['pansion_price'])) {
				if ($item['pansion_price'] > 999) {
					$price = 2000;
				} else {
					$price = 1000;
				}
				$temp = [
					'pansion_types' => $pansion_main_type['alias'],
					'price' => $price,
					'page' => 1
				];
				$slice_url = ParamsFromQuery::isSlice($temp);
				if (!empty($slice_url)) {
					$slices_arr = [];
					if ($price == 2000) {
						$slices_arr['name'] = str_replace('-Дорогие', ' дорогие ', $slice_url);
					} else {
						$slices_arr['name'] = str_replace('-Недорогие', ' недорогие ', $slice_url);
					}
					$slices_arr['url'] = $slice_url;
					array_push($slice_specials, $slices_arr);
				}
			}
		}

		// echo ('<pre>');
		// print_r($slices);
		// exit;



		// echo '<pre>';
		// print_r($item);
		// exit;

		return $this->render('index.twig', array(
			'item' => $item,
			'territory' => $territory,
			'leisure' => $leisure,
			'service_staff' => $service_staff,
			'extra_facilities' => $extra_facilities,
			'queue_id' => $item->pansion_id,
			'other_pansions' => $other_pansions,
			//'seo' => $seo,
			'faq' => $faq,
			'reviews' => $reviews,
			'slice_main_type' => $slice_main_type,
			'slice_specials' => $slice_specials,
			'slice_cities' => $slice_cities,
			'slice_network' => $slice_network,
		));
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}
