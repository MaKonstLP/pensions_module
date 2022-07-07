<?php

namespace app\modules\pensions\controllers;

use common\models\blog\BlogPost;
use common\models\blog\BlogTag;
use common\models\Seo;
use common\models\Pages;
use frontend\modules\pensions\components\Breadcrumbs;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\widgets\LinkPager;
use yii\web\Controller;
use Yii;

class BlogController extends Controller
{

	public function actionIndex()
	{

		// return $this->render('index.twig');

		Pages::createSiteObjects();
		$this->view->params['menu'] = 'blog';
		// if (Yii::$app->params['subdomen_alias'] != '') {
		// 	throw new \yii\web\NotFoundHttpException();
		// }
		$query = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'type' => 1]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 100,
				'forcePageParam' => false,
				'totalCount' => $query->count()
			],
		]);

		$seo = (new Seo('blog', $dataProvider->getPagination()->page + 1))->seo;
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(1);
		$this->setSeo($seo);

		$topPosts = (clone $query)->where(['featured' => true])->limit(5)->all();

		$listConfig = [
			'dataProvider' => $dataProvider,
			'itemView' => '_list-item.twig',
			'layout' => "{items}\n<div class='pagination_wrapper items_pagination' data-pagination-wrapper>{pager}</div>",
			'pager' => [
				'class' => LinkPager::class,
				'disableCurrentPageButton' => true,
				'nextPageLabel' => 'Следующая →',
				'prevPageLabel' => '← Предыдущая',
				'maxButtonCount' => 4,
				'activePageCssClass' => '_active',
				'pageCssClass' => 'items_pagination_item',
			],

		];

		// return $this->render('index.twig', compact('listConfig', 'topPosts', 'seo'));
		return $this->render('index.twig', [
			'listConfig' => $listConfig,
			'topPosts' => $topPosts,
			'seo' => $seo,
			// 'city_dec' => Yii::$app->params['subdomen_dec'],
			// 'breadcrumbs' => $seo['breadcrumbs'],
			// 'pagination' => $pagination,
		]);
	}

	public function actionPost($alias)
	{

		// return $this->render('post.twig');

		$this->view->params['menu'] = 'blog';

		// if (Yii::$app->params['subdomen_alias'] != '') {
		// 	throw new \yii\web\NotFoundHttpException();
		// }

		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => $alias])->one();
		if (empty($post)) {
			return new NotFoundHttpException();
		}


		// echo '<pre>';
		// print_r($post);
		// exit;
		$seo = ArrayHelper::toArray($post->seoObject);
		$this->setSeo($seo);

		$similarPosts = BlogPost::findWithMedia()
			->with('blogPostTags')->where(['published' => true, 'type' => 1])
			->andWhere(['!=', 'id', $post->id])
			->orderBy(['published_at' => SORT_DESC])
			->limit(3)
			->all();


		// function rusDateFormat($number = null, $monthName = null)
		// {
		// 	$num = array('-01-', '-02-', '-03-', '-04-', '-05-', '-06-', '-07-', '-08-', '-09-', '-10-', '-11-', '-12-');
		// 	$month = array(' января ', ' февраля ', ' марта ', ' апреля ', ' мая ', ' июня ', ' июля ', ' августа ', ' сентября ', ' октября ', ' ноября ', ' декабря ');
		// 	if ($number) return str_replace($num, $month, $number);
		// 	else if ($monthName) return str_replace($month, $num, $monthName);
		// 	else return null;
		// }

		//foreach ($similarPosts as $similarPost) {
		//	$date = date_create($similarPost['published_at']);
		//	$dateFormat =  date_format($date, 'd-m-Y');
		//	$rusDate =  rusDateFormat($dateFormat);
		//	$similarPost['published_at'] = $rusDate;
		//}


		// echo ('<pre>');
		// print_r($post);
		// exit;

		return $this->render('post.twig', [
			'post' => $post,
			'seo' => $seo,
			// 'breadcrumbs' => $seo['breadcrumbs'],
			'similarPosts' => $similarPosts
		]);
	}

	public function actionPreview($id)
	{
		if (Yii::$app->params['subdomen_alias'] != '') {
			throw new \yii\web\NotFoundHttpException();
		}
		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'id' => $id])->one();
		if (empty($post)) {
			return new NotFoundHttpException();
		}
		$seo = ArrayHelper::toArray($post->seoObject);
		$this->setSeo($seo);
		return $this->render('post.twig', compact('post'));
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}
