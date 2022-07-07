<?php

namespace frontend\modules\pensions\models;

use Yii;
use common\models\Restaurants;
use common\models\PansionMain;
use common\models\Pansion;
use common\models\RestaurantsTypes;
use yii\helpers\ArrayHelper;
use common\models\Subdomen;
use common\models\RestaurantsSpec;
use common\models\RestaurantsSpecial;
use common\models\RestaurantsExtra;
use common\widgets\ProgressWidget;
use common\models\elastic\ItemsFilterElastic;
use common\models\FilterItems;
use common\models\PansionTypeVia;

class ElasticItems extends \yii\elasticsearch\ActiveRecord
{
	public function attributes()
	{
		return [
			'id',
			'pansion_id',
			'pansion_name',
			'pansion_url',
			'pansion_price',
			'pansion_price_old',
			'pansion_address',
			'pansion_latitude',
			'pansion_longitude',
			'pansion_armed_bed',
			'pansion_review_yandex',
			'pansion_our',
			'pansion_district',
			'pansion_metro',
			'pansion_network',
			'pansion_type',
			'pansion_meal',
			'pansion_cities',
			'pansion_conditions',
			'pansion_conveniencies',
			'pansion_entertainments',
			'pansion_features',
			'pansion_highways',
			'pansion_images',
			'pansion_rooms',
			'pansion_specials',
			'pansion_slug',
			'pansion_types',
			'pansion_rev_ya',
			// 'pansion_rev_ya_id',
			// 'pansion_rev_ya_rate',
			// 'pansion_rev_ya_count'
		];
	}

	public static function index()
	{
		return 'pansions';
	}

	public static function type()
	{
		return 'items';
	}

	/**
	 * @return array This model's mapping
	 */
	public static function mapping()
	{
		return [
			static::type() => [
				'properties' => [
					'id'                           => ['type' => 'integer'],
					'pansion_id'                   => ['type' => 'integer'],
					'pansion_name'                 => ['type' => 'text'],
					'pansion_slug'                 => ['type' => 'text'],
					'pansion_url'                  => ['type' => 'text'],
					'pansion_price'                => ['type' => 'integer'],
					'pansion_price_old'            => ['type' => 'integer'],
					'pansion_address'              => ['type' => 'text'],
					'pansion_latitude'             => ['type' => 'text'],
					'pansion_longitude'            => ['type' => 'text'],
					'pansion_armed_bed'            => ['type' => 'integer'],
					'pansion_review_yandex'        => ['type' => 'text'],
					'pansion_our'                  => ['type' => 'integer'],
					'pansion_district'             => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_metro'                => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_network'              => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_type'                 => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_meal'                 => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_cities'               => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_conditions'           => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_conveniencies'        => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_entertainments'       => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_features'             => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_highways'             => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_images'               => ['type' => 'nested', 'properties' => [
						'path'               		=> ['type' => 'text'],
						'path_thumb'				=> ['type' => 'text'],
						'path_catalog'				=> ['type' => 'text'],
						'path_swiper'				=> ['type' => 'text'],
					]],
					'pansion_rooms'                => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_specials'        	   => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_types'        	   => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'integer'],
						'name'                      => ['type' => 'text'],
					]],
					'pansion_rev_ya'  	      => ['type' => 'nested', 'properties' => [
						'id'                        => ['type' => 'long'],
						'rate'                      => ['type' => 'text'],
						'count'                     => ['type' => 'text'],
						'active'                    => ['type' => 'integer'],
					]],
					// 'pansion_rev_ya_id'       		 => ['type' => 'integer'],
					// 'pansion_rev_ya_rate'       	 => ['type' => 'text'],
					// 'pansion_rev_ya_count'         => ['type' => 'text'],
				]
			],
		];
	}

	/**
	 * Set (update) mappings for this model
	 */
	public static function updateMapping()
	{
		$db = static::getDb();
		$command = $db->createCommand();
		$command->setMapping(static::index(), static::type(), static::mapping());
	}

	/**
	 * Create this model's index
	 */
	public static function createIndex()
	{
		$db = static::getDb();
		$command = $db->createCommand();
		$command->createIndex(static::index(), [
			'settings' => [
				'number_of_replicas' => 0,
				'number_of_shards' => 1,
			],
			'mappings' => static::mapping(),
		]);
	}

	/**
	 * Delete this model's index
	 */
	public static function deleteIndex()
	{
		$db = static::getDb();
		$command = $db->createCommand();
		$command->deleteIndex(static::index(), static::type());
	}

	public static function refreshIndex($params)
	{
		$connection = new \yii\db\Connection($params['main_connection_config']);
		$connection->open();
		Yii::$app->set('db', $connection);

		$res = self::deleteIndex();
		$res = self::updateMapping();
		$res = self::createIndex();

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
			->limit(100000)
			->all();

		$connection_local = new \yii\db\Connection($params['site_connection_config']);
		$connection_local->open();

		$all_res = '';

		$pens_count = count($pansions);
		$pens_iter = 0;
		foreach ($pansions as $pansion) {
			$res = self::addRecord($pansion, $connection, $connection_local);
			echo ProgressWidget::widget(['done' => $pens_iter++, 'total' => $pens_count]);
		}
		echo 'Обновление индекса ' . self::index() . ' ' . self::type() . ' завершено<br>' . $all_res;
	}

	public static function updateFilter($params)
	{
		$connection = new \yii\db\Connection($params['site_connection_config']);
		$connection->open();
		Yii::$app->set('db', $connection);

		$filter_items = FilterItems::find()
			->with(['filter'])
			->all();

		foreach ($filter_items as $filter_item) {
			$elastic_model = new self();
			$items = new ItemsFilterElastic([$filter_item->filter->alias => [0 => $filter_item->value]], 1, 1, false, 'restaurants', $elastic_model, false, false, false, true);
			if ($items->total) {
				$filter_item->active = 1;
			} else {
				$filter_item->active = 0;
			}
			$filter_item->save();
			echo $items->total . '
';
		}
	}

	public static function getTransliterationForUrl($name)
	{
		$name = preg_replace('/[^ a-zа-яё\d]/ui', '', $name);
		$latin = array('-', "Sch", "sch", 'Yo', 'Zh', 'Kh', 'Ts', 'Ch', 'Sh', 'Yu', 'ya', 'yo', 'zh', 'kh', 'ts', 'ch', 'sh', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', '', 'Y', '', 'E', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', '', 'y', '', 'e');
		$cyrillic = array(' ', "Щ", "щ", 'Ё', 'Ж', 'Х', 'Ц', 'Ч', 'Ш', 'Ю', 'я', 'ё', 'ж', 'х', 'ц', 'ч', 'ш', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Ь', 'Ы', 'Ъ', 'Э', 'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'ь', 'ы', 'ъ', 'э');
		return trim(
			preg_replace(
				"/(.)\\1+/",
				"$1",
				strtolower(
					str_replace(
						" ",
						"-",
						$name
					)
				)
			),
			'-'
		);
	}

	public static function addRecord($pansion, $connection, $connection_local)
	{
		$isExist = false;

		try {
			// $record = self::get($restaurant->id);
			$record = self::get($pansion->id);
			if (!$record) {
				$record = new self();
				// $record->setPrimaryKey($restaurant->id);
				$record->setPrimaryKey($pansion->id);
			} else {
				$isExist = true;
			}
		} catch (\Exception $e) {
			$record = new self();
			// $record->setPrimaryKey($restaurant->id);
			$record->setPrimaryKey($pansion->id);
		}

		Yii::$app->set('db', $connection);

		$record->id = $pansion->id;
		$record->pansion_id = $pansion->pansion_id;
		$record->pansion_name = $pansion->name;
		$record->pansion_url = $pansion->url;
		$record->pansion_price = $pansion->price;
		$record->pansion_price_old = $pansion->price_old;
		$record->pansion_address = $pansion->address;
		$record->pansion_latitude = $pansion->latitude;
		$record->pansion_longitude = $pansion->longitude;
		$record->pansion_armed_bed = $pansion->armed_bed;
		$record->pansion_review_yandex = $pansion->review_yandex;
		$record->pansion_our = $pansion->our;

		//Районы
		$districts = [];
		foreach ($pansion->districts as $district) {
			$district_arr = [];
			$district_arr['id'] = $district->district_id;
			$district_arr['name'] = $district->name;
			array_push($districts, $district_arr);
		}
		$record->pansion_district = $districts;

		//Метро
		$metros = [];
		foreach ($pansion->metros as $metro) {
			$metro_arr = [];
			$metro_arr['id'] = $metro->metro_id;
			$metro_arr['name'] = $metro->name;
			array_push($metros, $metro_arr);
		}
		$record->pansion_metro = $metros;

		//Сеть пансионата
		$networks = [];
		foreach ($pansion->networks as $network) {
			$network_arr = [];
			$network_arr['id'] = $network->network_id;
			$network_arr['name'] = $network->name;
			array_push($networks, $network_arr);
		}
		$record->pansion_network = $networks;

		//Тип пансионата
		$sorts = [];
		foreach ($pansion->otherTypes as $type) {
			$type_arr = [];
			$type_arr['id'] = $type->type_id;
			$type_arr['name'] = $type->name;
			array_push($sorts, $type_arr);
		}
		$record->pansion_type = $sorts;

		//Приемы пищи
		$meals = [];
		foreach ($pansion->meals as $meal) {
			$meal_arr = [];
			$meal_arr['id'] = $meal->meal_id;
			$meal_arr['name'] = $meal->name;
			array_push($meals, $meal_arr);
		}
		$record->pansion_meal = $meals;

		//Города
		$cities = [];
		foreach ($pansion->cities as $key => $city) {
			$city_arr = [];
			$city_arr['id'] = $city->city_id;
			$city_arr['name'] = $city->name;
			array_push($cities, $city_arr);
		}
		$record->pansion_cities = $cities;

		//Состояние пожилого
		$conditions = [];
		foreach ($pansion->conditions as $condition) {
			$condition_arr = [];
			$condition_arr['id'] = $condition->condition_id;
			$condition_arr['name'] = $condition->name;
			array_push($conditions, $condition_arr);
		}
		$record->pansion_conditions = $conditions;

		//Удобства
		$conveniencies = [];
		foreach ($pansion->conveniencies as $convenience) {
			$convenience_arr = [];
			$convenience_arr['id'] = $convenience->convenience_id;
			$convenience_arr['name'] = $convenience->name;
			array_push($conveniencies, $convenience_arr);
		}
		$record->pansion_conveniencies = $conveniencies;

		//Развлечения
		$entertainments = [];
		foreach ($pansion->entertainments as $entertainment) {
			$entertainment_arr = [];
			$entertainment_arr['id'] = $entertainment->entertainment_id;
			$entertainment_arr['name'] = $entertainment->name;
			array_push($entertainments, $entertainment_arr);
		}
		$record->pansion_entertainments = $entertainments;

		//Особенности
		$features = [];
		foreach ($pansion->features as $feature) {
			$feature_arr = [];
			$feature_arr['id'] = $feature->feature_id;
			$feature_arr['name'] = $feature->name;
			array_push($features, $feature_arr);
		}
		$record->pansion_features = $features;

		//Шоссе
		$highways = [];
		foreach ($pansion->highways as $highway) {
			$highway_arr = [];
			$highway_arr['id'] = $highway->highway_id;
			$highway_arr['name'] = $highway->name;
			array_push($highways, $highway_arr);
		}
		$record->pansion_highways = $highways;

		//Картинки пансионата
		// $images = [];
		// foreach ($pansion->images as $image) {
		// 	if (!$image->img_d)
		// 		continue;
		// 	$image_arr = [];
		// 	$file_type = '.' . pathinfo($image->img_path, PATHINFO_EXTENSION);
		// 	$file_name_trim = basename($image->img_path, $file_type);
		// 	$image_arr['path'] = '/img_d/webp/' . $pansion->pansion_id . '/' . $file_name_trim . '.webp';
		// 	$image_arr['path_thumb'] = '/img_d/webp-thumb/' . $pansion->pansion_id . '/' . $file_name_trim . '.webp';
		// 	$image_arr['path_catalog'] = '/img_d/webp-thumb-catalog/' . $pansion->pansion_id . '/' . $file_name_trim . '.webp';
		// 	$image_arr['path_swiper'] = '/img_d/webp-thumb-swiper/' . $pansion->pansion_id . '/' . $file_name_trim . '.webp';
		// 	array_push($images, $image_arr);
		// }
		// $record->pansion_images = $images;

		//Количество мест в комнате
		$rooms = [];
		foreach ($pansion->rooms as $room) {
			$room_arr = [];
			$room_arr['id'] = $room->room_id;
			$room_arr['size'] = $room->room_size;
			array_push($rooms, $room_arr);
		}
		$record->pansion_rooms = $rooms;

		//Особенности для фильтра
		$specials = [];
		foreach ($pansion->features as $feature) {
			$feature_specials_arr = [
				1 => 1,
				3 => 2,
				4 => 3,
				5 => 4,
				6 => 5
			];
			if (array_key_exists($feature->feature_id, $feature_specials_arr)) {
				$special_arr = [];
				$special_arr['id'] = $feature_specials_arr[$feature->feature_id];
				$special_arr['name'] = $feature->name;
				array_push($specials, $special_arr);
			}
		}
		foreach ($pansion->conditions as $condition) {
			$condition_specials_arr = [
				1658 => 6,
				571  => 7
			];
			if (array_key_exists($condition->condition_id, $condition_specials_arr)) {
				$special_arr = [];
				$special_arr['id'] = $condition_specials_arr[$condition->condition_id];
				$special_arr['name'] = $condition->name;
				array_push($specials, $special_arr);
			}
		}
		foreach ($pansion->meals as $meal) {
			$meal_specials_arr = [4 => 8];
			if (array_key_exists($meal->meal_id, $meal_specials_arr)) {
				$special_arr = [];
				$special_arr['id'] = $meal_specials_arr[$meal->meal_id];
				$special_arr['name'] = $meal->name;
				array_push($specials, $special_arr);
			}
		}
		if ($pansion->armed_bed == 1) {
			$specials[] = ['id' => 9, 'name' => 'Армед кровати для лежачих'];
		}
		$record->pansion_specials = $specials;

		//Основной тип пансионата
		$types = [];
		$main_types = PansionTypeVia::find()
			->where(['pansion_id' => $pansion->pansion_id])
			->with('type')
			->all();
		foreach ($main_types as $key => $type) {
			$types_arr = [];
			$types_arr['id'] = $type->type_id;
			$types_arr['name'] = $type->type->name;
			array_push($types, $types_arr);
		}
		$record->pansion_types = $types;

		//Отзывы с Яндекса из общей базы
		$reviews = [];
		$reviews['id'] = $pansion->rev_ya_id;
		$reviews['rate'] = $pansion->rev_ya_rate;
		$reviews['count'] = $pansion->rev_ya_count;
		//$record->pansion_rev_ya = $reviews;

		//подключение к локальной БД
		Yii::$app->set('db', $connection_local);

		$pansion_local = Pansion::find()
			->where(['pansion_id' => $pansion->pansion_id])
			->one();

		//Отзывы с Яндекса "активность" из локальной базы
		$reviews['active'] = $pansion_local->rev_ya_active;
		$record->pansion_rev_ya = $reviews;


		//Картинки пансионата
		$images = [];
		// foreach ($pansion->images as $image) {
		foreach ($pansion_local->images as $image) {
			if (!$image->img_d)
				continue;
			$image_arr = [];
			// $file_type = '.' . pathinfo($image->img_path, PATHINFO_EXTENSION);
			$file_type = '.' . pathinfo($image->img_d, PATHINFO_EXTENSION);
			// $file_name_trim = basename($image->img_path, $file_type);
			$file_name_trim = basename($image->img_d, $file_type);
			$image_arr['path'] = '/img_d/webp/' . $pansion->pansion_id . '/' . $file_name_trim . '.webp';
			$image_arr['path_thumb'] = '/img_d/webp-thumb/' . $pansion->pansion_id . '/' . $file_name_trim . '.webp';
			$image_arr['path_catalog'] = '/img_d/webp-thumb-catalog/' . $pansion->pansion_id . '/' . $file_name_trim . '.webp';
			$image_arr['path_swiper'] = '/img_d/webp-thumb-swiper/' . $pansion->pansion_id . '/' . $file_name_trim . '.webp';
			array_push($images, $image_arr);
		}
		$record->pansion_images = $images;

		// restaurant slug
		if ($row = (new \yii\db\Query())->select('slug')->from('pansion_slug')->where(['pansion_id' => $pansion->pansion_id])->one()) {
			$record->pansion_slug = $row['slug'];
		} else {
			$record->pansion_slug = self::getTransliterationForUrl($pansion->name);
			\Yii::$app->db->createCommand()->insert('pansion_slug', ['pansion_id' => $pansion->pansion_id, 'slug' =>  $record->pansion_slug])->execute();
		}

		try {
			if (!$isExist) {
				$result = $record->insert();
			} else {
				$result = $record->update();
			}
		} catch (\Exception $e) {
			$result = $e;
			echo $result;
		}

		return $result;
	}
}
