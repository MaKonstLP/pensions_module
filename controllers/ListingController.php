<?php

namespace app\modules\pensions\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
// use frontend\widgets\FilterWidget;
// use common\widgets\FilterWidget;
use frontend\modules\pensions\widgets\FilterWidget;
// use frontend\widgets\PaginationWidget;
use frontend\modules\pensions\widgets\PaginationWidget;
use frontend\components\ParamsFromQuery;
// use frontend\components\QueryFromSlice;
use frontend\modules\pensions\components\QueryFromSlice;
use frontend\modules\pensions\components\Breadcrumbs;
use common\models\Pages;
use frontend\components\RoomsFilter;
use common\models\Filter;
use common\models\FilterItems;
use common\models\Slices;
use common\models\GorkoApi;
use common\models\elastic\ItemsFilterElastic;
use frontend\modules\pensions\models\ElasticItems;
use common\models\Seo;
use backend\models\Faq;
use backend\models\Review;
use frontend\components\Declension;
use common\models\blog\BlogPost;

use common\models\PansionMain;
use common\models\Pansion;


class ListingController extends Controller
{
	protected $per_page = 12;
	// protected $per_page = 200;

	public $filter_model,
		$slices_model;

	public function beforeAction($action)
	{
		$this->filter_model = Filter::find()->with('items')->where(['active' => 1])->orderBy(['sort' => SORT_ASC])->all();
		$this->slices_model = Slices::find()->all();

		return parent::beforeAction($action);
	}

	public function actionSlice($slice)
	{
		$slice_obj = new QueryFromSlice($slice);

		if ($slice_obj->flag) {
			$this->view->params['menu'] = $slice;
			$params = $this->parseGetQuery($slice_obj->params, Filter::find()->with('items')->orderBy(['sort' => SORT_ASC])->all(), $this->slices_model);
			isset($_GET['page']) ? $params['page'] = $_GET['page'] : $params['page'];

			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 1)[0];
			if ($params['page'] > 1) {
				$canonical .= $params['canonical'];
			}

			if (!empty($slice_obj->slices_top)) {
				$custom_slice = true;
				$slices_top = $slice_obj->slices_top;
			} else {
				$custom_slice = false;
				$slices_top = $params['params_filter'];
			}

			$slices_bot = $slice_obj->slices_bot;
			// if ($slice_obj->type == 'Тип заведения + Город' || $slice_obj->type == 'Тип заведения') {
			if (isset($slice_obj->params['pansion_types']) && !empty($slice_obj->params['pansion_types'])) {
				$slices_bot = [];
				$cities_bot = ['moskva', 'himki', 'lyubercy', 'zelenograd', 'korolev', 'mytishchi', 'balashiha', 'podolsk', 'troick'];

				foreach ($cities_bot as $city) {
					$temp_params = [
						'pansion_types' => $slice_obj->params['pansion_types'],
						'city' => $city,
						'page' => 1
					];
					$slice_url_temp = ParamsFromQuery::isSlice($temp_params);
					$slices_bot[] = $slice_url_temp;
				}
			}

			$noindex = '';
			$page_noindex = Pages::find()->where(['type' => str_replace('/', '', $params['listing_url']), 'noindex' => 1])->one();
			if ($page_noindex) {
				$noindex = true;
			}

			$content_text = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => str_replace('/', '', $params['listing_url']), 'type' => 3])->one();
			if (empty($content_text)) {
				$content_text = '';
			}

			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	Breadcrumbs::get_breadcrumbs(2),
				$canonical 		= 	$canonical,
				$type 			=	$slice,
				// $slices_top		=	$slice_obj->slices_top,
				$slices_top,
				// $slices_bot		=	$slice_obj->slices_bot,
				$slices_bot,
				$slice_type	 	=  $slice_obj->type,
				$slices_text	=	$params['params_text'],
				$custom_slice,
				$noindex,
				$content_text,
			);
		} else {
			return $this->goHome();
		}
	}

	public function actionIndex()
	{
		$getQuery = $_GET;
		unset($getQuery['q']);
		if (count($getQuery) > 0) {
			$params = $this->parseGetQuery($getQuery, $this->filter_model, $this->slices_model);
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
			if ($params['page'] > 1) {
				$canonical .= $params['canonical'];
			}

			$slices_bot = [];
			if (isset($params['params_filter']['pansion_types']) && !empty($params['params_filter']['pansion_types'])) {
				$cities_bot = ['moskva', 'himki', 'lyubercy', 'zelenograd', 'korolev', 'mytishchi', 'balashiha', 'podolsk', 'troick'];

				foreach ($cities_bot as $city) {
					$temp_params = [
						'pansion_types' => $params['params_filter']['pansion_types'][0],
						'city' => $city,
						'page' => 1
					];
					$slice_url_temp = ParamsFromQuery::isSlice($temp_params);
					$slices_bot[] = $slice_url_temp;
				}
			}

			$content_text = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => 'listing', 'type' => 3])->one();
			if (empty($content_text)) {
				$content_text = '';
			}

			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	Breadcrumbs::get_breadcrumbs(1),
				$canonical 		=	$canonical,
				$type				=	'listing',
				$slices_top		=	$params['params_filter'],
				$slices_bot,
				false,
				$slices_text	=	$params['params_text'],
				false,
				false,
				$content_text,
			);
		} else {
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

			$content_text = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => 'index', 'type' => 3])->one();
			if (empty($content_text)) {
				$content_text = '';
			}

			return $this->actionListing(
				$page 			=	1,
				$per_page		=	$this->per_page,
				$params_filter	= 	[],
				$breadcrumbs 	= 	Breadcrumbs::get_breadcrumbs(1),
				$canonical 		= 	$canonical,
				$type = false,
				$slices_top		=	'index',
				false,
				false,
				false,
				false,
				false,
				$content_text,
			);
		}
	}

	public function actionListing($page, $per_page, $params_filter, $breadcrumbs, $canonical, $type = false, $slices_top = false, $slices_bot = false, $slice_type = false, $slices_text = false, $custom_slice = false, $noindex = false, $content_text = false)
	{
		/* 	$connection = new \yii\db\Connection([
			'username' => 'root',
			'password' => 'GxU25UseYmeVcsn5Xhzy',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pensions'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);
		$pansions = PansionMain::find()
			->with('districts')
			->with('metros')
			->with('networks')
			->with('otherTypes')
			->with('meals')
			->with('cities')
			->with('conditions')
			->with('conveniencies')
			->with('features')
			->with('highways')
			->with('images')
			->with('rooms')
			->with('entertainments')
			->with('maintype')
			->where(['id' => 1])
			->all();
		}

		// $pansions = Pansion::find()->with('images')->all();
		echo ('<pre>');
		print_r(count($pansions));
		echo ('<br>');
		print_r($pansions);
		exit; */



		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params_filter, $per_page, $page, false, 'restaurants', $elastic_model);

		if ($page > 1) {
			$seo['text_top'] = '';
			$seo['text_bottom'] = '';
		}

		$filter = FilterWidget::widget([
			'filter_active'	=> $params_filter,
			'filter_model'		=> $this->filter_model,
			'total'				=> $items->total,
		]);

		$pagination = PaginationWidget::widget([
			'total' => $items->pages,
			'current' => $page,
		]);


		if ($slice_type == 'Город')
			$type = 'city';
		// $seo_type = $type ? $type : 'listing';
		$seo_type = $type ? $type : 'index';

		$seo = $this->getSeo($seo_type, $page, $items->total);
		$seo['breadcrumbs'] = $breadcrumbs;
		$this->setSeo($seo, $page, $canonical);

		// echo ('<pre>');
		// print_r($seo_type);
		// print_r($seo);
		// exit;

		if ($seo_type == 'listing' and count($params_filter) > 0) {
			$seo['text_top'] = '';
			$seo['text_bottom'] = '';
		}

		$main_flag = ($seo_type == 'listing' and count($params_filter) == 0);

		$faq = Faq::find()->all();
		shuffle($faq);
		$faq = array_slice($faq, 0, 3);

		$reviews = Review::find()->where(['active' => 1])->all();

		$filter_items_cities = FilterItems::find()->where(['active' => 1, 'filter_id' => 2])->all();
		$filter_items_diseasies = FilterItems::find()->where(['active' => 1, 'filter_id' => 5])->all();

		if ($noindex) {
			$this->view->params['robots'] = 'noindex';
		}

		// echo ('<pre>');
		// print_r($items->items);
		// exit;

		return $this->render('index.twig', array(
			'items' => $items->items,
			'filter' => $filter,
			'pagination' => $pagination,
			'seo' => $seo,
			'total' => $items->total,
			'menu' => $type,
			'main_flag' => $main_flag,
			'custom_slice' => $custom_slice,
			'slices_top' => $slices_top,
			'slices_bot' => $slices_bot,
			'slices_text' => $slices_text,
			'faq' => $faq,
			'reviews' => $reviews,
			'cities' => $filter_items_cities,
			'diseasies' => $filter_items_diseasies,
			'seo_type' => $seo_type,
			'content_text' => $content_text,
		));
	}

	public function actionAjaxGetTotal()
	{
		$params = $this->parseGetQuery(json_decode($_GET['filter'], true), $this->filter_model, $this->slices_model);
		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params['params_filter'], 1, $params['page'], false, 'restaurants', $elastic_model);
		return json_encode([
			'total' => $items->total,
		]);
	}

	public function actionAjaxFilter()
	{
		$params = $this->parseGetQuery(json_decode($_GET['filter'], true), $this->filter_model, $this->slices_model);
		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params['params_filter'], $this->per_page, $params['page'], false, 'restaurants', $elastic_model);

		$pagination = PaginationWidget::widget([
			'total' => $items->pages,
			'current' => $params['page'],
		]);

		substr($params['listing_url'], 0, 1) == '?' ? $breadcrumbs = Breadcrumbs::get_breadcrumbs(1) : $breadcrumbs = Breadcrumbs::get_breadcrumbs(2);
		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true));

		if ($slice_url) {
			$slice_obj = new QueryFromSlice($slice_url);

			$slices_bot_arr = $slice_obj->slices_bot;
			if (isset($slice_obj->params['pansion_types']) && !empty($slice_obj->params['pansion_types'])) {
				$slices_bot_arr = [];
				$cities_bot = ['moskva', 'himki', 'lyubercy', 'zelenograd', 'korolev', 'mytishchi', 'balashiha', 'podolsk', 'troick'];

				foreach ($cities_bot as $city) {
					$temp_params = [
						'pansion_types' => $slice_obj->params['pansion_types'],
						'city' => $city,
						'page' => 1
					];
					$slice_url_temp = ParamsFromQuery::isSlice($temp_params);
					$slices_bot_arr[] = $slice_url_temp;
				}
			}

			// $slices_bot = $this->renderPartial('//components/generic/slices_bot.twig', array('slices_bot' => $slice_obj->slices_bot));
			$slices_bot = $this->renderPartial('//components/generic/slices_bot.twig', array('slices_bot' => $slices_bot_arr));

			if (!empty($slice_obj->slices_top)) {
				$slices_top = $this->renderPartial('//components/generic/slices_top.twig', array('slices_top' => $slice_obj->slices_top, 'custom_slice' => true));
			} else {
				$slices_top = $this->renderPartial('//components/generic/slices_top.twig', array('slices_top' => $params['params_filter'], 'slices_text' => $params['params_text']));
			}

			if ($slice_obj->type == 'Город')
				$slice_url = 'city';
		} elseif (empty($params['params_filter'])) {
			$slices_top = $this->renderPartial('//components/generic/slices_top.twig', array('slices_top' => 'index'));
			$slices_bot = $this->renderPartial('//components/generic/slices_bot.twig', array('slices_bot' => ''));
		} else {
			$slices_top = $this->renderPartial('//components/generic/slices_top.twig', array('slices_top' => $params['params_filter'], 'slices_text' => $params['params_text']));

			$slices_bot_arr = [];
			if (isset($params['params_filter']['pansion_types']) && !empty($params['params_filter']['pansion_types'])) {
				$cities_bot = ['moskva', 'himki', 'lyubercy', 'zelenograd', 'korolev', 'mytishchi', 'balashiha', 'podolsk', 'troick'];

				foreach ($cities_bot as $city) {
					$temp_params = [
						'pansion_types' => $params['params_filter']['pansion_types'][0],
						'city' => $city,
						'page' => 1
					];
					$slice_url_temp = ParamsFromQuery::isSlice($temp_params);
					$slices_bot_arr[] = $slice_url_temp;
				}
			}

			$slices_bot = $this->renderPartial('//components/generic/slices_bot.twig', array('slices_bot' => $slices_bot_arr));
		}




		$seo_type = $slice_url ? $slice_url : 'listing';
		if (count($params['params_filter']) == 0) {
			$seo_type = 'index';
		}
		$seo = $this->getSeo($seo_type, $params['page'], $items->total);
		$seo['breadcrumbs'] = $breadcrumbs;

		// echo ('<pre>');
		// print_r($seo_type);
		// exit;

		$title = $this->renderPartial('//components/generic/title.twig', array(
			'seo' => $seo,
			'total' => $items->total,
			'seo_type' => $seo_type
		));

		if ($params['page'] == 1) {
			$text_top = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_top']));
			$text_bottom = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_bottom']));
		} else {
			$text_top = '';
			$text_bottom = '';
		}

		if ($seo_type == 'listing' and count($params['params_filter']) > 0) {
			$text_top = '';
			$text_bottom = '';
		}

		$filter_items_cities = FilterItems::find()->where(['active' => 1, 'filter_id' => 2])->all();
		$filter_items_diseasies = FilterItems::find()->where(['active' => 1, 'filter_id' => 5])->all();

		$content_text = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => $seo_type, 'type' => 3])->one();
		if (empty($content_text)) {
			$content_text = '';
		}

		return  json_encode([
			'listing' => $this->renderPartial('//components/generic/listing.twig', array(
				'items' => $items->items,
				'img_alt' => $seo['img_alt'],
				'page' => 'listing',
				'cities' => $filter_items_cities,
				'diseasies' => $filter_items_diseasies
			)),
			'pagination' => $pagination,
			'url' => $params['listing_url'],
			'title' => $title,
			'text_top' => $text_top,
			'text_bottom' => $text_bottom,
			'seo_title' => $seo['title'],
			'total' => $items->total,
			'slices_top' => $slices_top,
			'slices_bot' => $slices_bot,
			'seo' => $seo,
			'content_text' => $this->renderPartial('//components/generic/listing_content.twig', array('content_text' => $content_text))
		]);
	}

	public function actionAjaxFilterSlice()
	{
		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true));

		return $slice_url;
	}

	private function parseGetQuery($getQuery, $filter_model, $slices_model)
	{
		$return = [];
		if (isset($getQuery['page'])) {
			$return['page'] = $getQuery['page'];
		} else {
			$return['page'] = 1;
		}

		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $this->slices_model);

		//print_r($temp_params);exit;
		$return['params_api'] = $temp_params->params_api;
		$return['params_filter'] = $temp_params->params_filter;
		$return['params_text'] = $temp_params->params_text;
		$return['listing_url'] = $temp_params->listing_url;
		$return['canonical'] = $temp_params->canonical;
		return $return;
	}

	private function getSeo($type, $page, $count = 0)
	{

		$seo = new Seo($type, $page, $count);

		return $seo->seo;
	}

	private function setSeo($seo, $page, $canonical)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		if ($page != 1) {
			$this->view->params['canonical'] = $canonical;
		}
		$this->view->params['kw'] = $seo['keywords'];
	}
}

//class ListingController extends Controller
//{
//	public function actionIndex(){
//		GorkoApi::renewAllData([
//			'city_id=4400&type_id=1&event=15',
//			'city_id=4400&type_id=1&event=17'
//		]);
//		return 1;
//	}	
//}