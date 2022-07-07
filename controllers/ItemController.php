<?php

namespace app\modules\pensions\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
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

		// echo '<pre>';
		// print_r($item);
		// exit;

		return $this->render('index.twig', array(
			'item' => $item,
			'queue_id' => $item->pansion_id,
			'other_pansions' => $other_pansions,
			//'seo' => $seo,
			'faq' => $faq,
			'reviews' => $reviews
		));
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}
