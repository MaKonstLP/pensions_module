<?php

namespace app\modules\pensions\controllers;

use common\models\blog\BlogPost;
use common\models\blog\BlogTag;
use common\models\Seo;
use common\models\Pages;
use frontend\modules\pensions\components\Breadcrumbs;
use frontend\modules\pensions\widgets\LoadmoreWidget;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\widgets\LinkPager;
use yii\web\Controller;
use Yii;

class ServiceController extends Controller
{
	protected $per_page = 9;

	public function actionIndex()
	{

		// return $this->render('index.twig', []);

		Pages::createSiteObjects();
		$this->view->params['menu'] = 'service';
		// if (Yii::$app->params['subdomen_alias'] != '') {
		// 	throw new \yii\web\NotFoundHttpException();
		// }
		$query = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'type' => 2]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 100,
				'forcePageParam' => false,
				'totalCount' => $query->count()
			],
		]);

		$seo = (new Seo('service', $dataProvider->getPagination()->page + 1))->seo;
		// $seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(1);
		// $seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs('ideas');
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

		$current_page = $_GET['page'] ?? 1;
		// $pagination = LoadmoreWidget::widget([
		// 	'total' => $query->count(),
		// 	'current_page' => $current_page,
		// 	'current' => $current_page * $this->per_page,
		// 	'per_page' => $this->per_page,
		// ]);

		return $this->render('index.twig', [
			'listConfig' => $listConfig,
			'topPosts' => $topPosts,
			'seo' => $seo,
			// 'city_dec' => Yii::$app->params['subdomen_dec'],
			// 'breadcrumbs' => $seo['breadcrumbs'],
			// 'pagination' => $pagination,
		]);
	}

	public function actionService($alias)
	{
		if($alias == 'test')
			return $this->render('service.twig', []);

		$this->view->params['menu'] = 'service';

		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => $alias, 'type' => 2])->one();
		if (empty($post)) {
			// return new NotFoundHttpException();
			throw new NotFoundHttpException();
		}

		$seo = ArrayHelper::toArray($post->seoObject);

		$this->setSeo($seo);

		$similarPosts = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'type' => 2])->andWhere(['!=', 'id', $post->id])->orderBy(['published_at' => SORT_DESC])->limit(3)->all();

		// echo '<pre>';
		// $post->html = 0;
		// print_r($post->getFilesData('image'));
		// exit;

		return $this->render('post.twig', [
			'post' => $post,
			'seo' => $seo,
			'similarPosts' => $similarPosts
		]);
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}
