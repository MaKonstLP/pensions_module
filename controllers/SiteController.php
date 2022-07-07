<?php

namespace app\modules\pensions\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use common\widgets\FilterWidget;
use common\models\Pages;
use common\models\Filter;
use common\models\Slices;
use common\models\elastic\ItemsFilterElastic;
use frontend\modules\pensions\models\ElasticItems;
use common\models\Seo;

use common\models\PansionMain;

class SiteController extends Controller
{
	//public function getViewPath()
	//{
	//    return Yii::getAlias('@app/modules/svadbanaprirode/views/site');
	//}

	public function actionIndex()
	{
		// ElasticItems::refreshIndex();
		// exit;

		$test = PansionMain::find()->with('cities')->all();
		// echo ('<pre>');
		// print_r($test);
		// exit;

		// return $this->render('index.twig', []);

		$filter_model = Filter::find()->with('items')->where(['active' => 1])->orderBy(['sort' => SORT_ASC])->all();
		$slices_model = Slices::find()->all();
		$seo = $this->getSeo('index');
		$this->setSeo($seo);

		$filter = FilterWidget::widget([
			'filter_active' => [],
			'filter_model' => $filter_model
		]);

		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic([], 10, 1, false, 'restaurants', $elastic_model);
		// $mainWidget = $this->renderPartial('//components/generic/profitable_offer.twig', [
		// 	'items' => $items->items,
		// 	'city_rod' => Yii::$app->params['subdomen_rod'],
		// ]);

		// echo ('<pre>');
		// print_r($items);
		// exit;
		echo 1;exit;

		return $this->render('index.twig', [
			'filter' => $filter,
			'total' => $items->total,
			// 'mainWidget' => $mainWidget,
			'seo' => $seo,
			'subid' => isset(Yii::$app->params['subdomen_id']) ? Yii::$app->params['subdomen_id'] : false
		]);
	}

	public function actionError()
	{
		$items = ElasticItems::find()
		->limit(100)
		->all();
		shuffle($items);
		$items = array_slice($items, 0, 3);

		return $this->render('error.twig', [
			'items' => $items,
		]);
	}

	public function actionRobots()
	{
		header('Content-type: text/plain');
		echo 'User-agent: *
Disallow: /';
		exit;
	}

	private function getSeo($type, $page = 1, $count = 0)
	{
		$seo = new Seo($type, $page, $count);

		return $seo->seo;
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}
