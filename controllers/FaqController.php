<?php

namespace app\modules\pensions\controllers;

use Yii;
use yii\web\Controller;
use common\models\Seo;
use backend\models\Faq;

class FaqController extends Controller
{

	public function actionIndex()
	{

		// return $this->render('index.twig', array());

		$this->view->params['menu'] = 'faq';
		$seo = $this->getSeo('faq');
		$this->setSeo($seo);

		$faq = Faq::find()->all();

		return $this->render('index.twig', array(
			'seo' => $seo,
			'faq' => $faq
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
