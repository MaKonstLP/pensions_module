<?php

namespace app\modules\pensions\controllers;

use Yii;
use yii\web\Controller;
use common\models\Seo;
use frontend\modules\pensions\models\ElasticItems;

class AboutController extends Controller
{

	public function actionIndex()
	{
		$items = ElasticItems::find()
		->limit(100)
		->all();
		shuffle($items);
		$items = array_slice($items, 0, 3);

		// $this->view->params['menu'] = 'about';
		$seo = $this->getSeo('about');
		$this->setSeo($seo);

		return $this->render('index.twig', array(
			'items' => $items,
			'seo' => $seo,
		));
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
