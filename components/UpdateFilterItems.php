<?php

namespace frontend\modules\pensions\components;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\Filter;
use common\models\Slices;
use common\models\MetroStations;
use common\models\Okruga;
use common\models\Rayoni;
use common\models\elastic\ItemsFilterElastic;
use frontend\components\ParamsFromQuery;
use frontend\modules\pensions\models\ElasticItems;
use frontend\modules\pensions\models\RestaurantSpecFilterRel;

use Elasticsearch\ClientBuilder;


class UpdateFilterItems
{
	public static function parseGetQuery($getQuery, $filter_model, $slices_model)
	{
		$return = [];
		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $slices_model);
		$return['params_filter'] = $temp_params->params_filter;

		return $return;
	}

	public static function getAggregateResult($filterState, $params, $elastic_model, $aggregations_arr)
	{
		$items = new ItemsFilterElastic($params['params_filter'], 1, 1, false, 'restaurants', $elastic_model);

		$query = $items->query;

		foreach ($aggregations_arr as $key => $value) {
			$query->addAggregate($key, $value);
		}

		return $query->search();
	}

	public static function getSoloAggregateResult($filterState, $params, $elastic_model, $aggregations_arr){
		$items = new ItemsFilterElastic($params['params_filter'], 1, 1, false, 'restaurants', $elastic_model);

		$query = $items->query;
		foreach ($aggregations_arr as $key => $value) {
			$query->addAggregate($key, $value);
		}
		return $query->search();
	}

	public static function getFilter($filterState)
	{
		$aggregations_arr = self::getAggregateArr();
		$filter_model = Filter::find()->with('items')->orderBy(['sort' => SORT_ASC])->all();
		$slices_model = Slices::find()->all();
		$params = UpdateFilterItems::parseGetQuery($filterState, $filter_model, $slices_model);
		$elastic_model = new ElasticItems;

		$enabledFilterItemsList = [];
		$aggregateResult = UpdateFilterItems::getAggregateResult($filterState, $params, $elastic_model, $aggregations_arr);

		if (isset($aggregateResult['aggregations']['price_group'])) {
			$map = [
				'*-1000.0' => '1000',
				'1000.0-*' => '2000',
			];
			if(!isset($filterState['price'])){
				$result_arr = $aggregateResult['aggregations']['price_group']['buckets'];
			}
			else{
				$aggregations_solo_arr = [
					'price_group' => $aggregations_arr['price_group'],
				];
				$solo_params = $params;
				unset($solo_params['params_filter']['price']);
				$aggregateSoloResult = UpdateFilterItems::getSoloAggregateResult($filterState, $solo_params, $elastic_model, $aggregations_solo_arr);
				$result_arr = $aggregateSoloResult['aggregations']['price_group']['buckets'];
			}
			$tmp = [];
			foreach ($result_arr as $key => $value) {
				if($value['doc_count'] > 0)
					$tmp[$map[$value['key']]] = $value['doc_count'];
			}
			$enabledFilterItemsList['price'] = $tmp;
		}

		$aggregation_nested_arr = [
			'web' => 'network',
			'city' => 'city',
			'district' => 'district',
			'metro' => 'metro',
			'type' => 'type',
			'specials' => 'specials',
			'disease' => 'conditions',
			'pansion_types' => 'pansion_types'
		];

		foreach ($aggregation_nested_arr as $agg_key => $agg_val) {
			if (isset($aggregateResult['aggregations'][$agg_val.'_group'])) {
				if(!isset($filterState[$agg_key])){
					$result_arr = $aggregateResult['aggregations'][$agg_val.'_group'][$agg_val]['buckets'];
				}
				else{
					$aggregations_solo_arr = [
						$agg_val.'_group' => $aggregations_arr[$agg_val.'_group'],
					];
					$solo_params = $params;
					unset($solo_params['params_filter'][$agg_key]);
					$aggregateSoloResult = UpdateFilterItems::getSoloAggregateResult($filterState, $solo_params, $elastic_model, $aggregations_solo_arr);
					$result_arr = $aggregateSoloResult['aggregations'][$agg_val.'_group'][$agg_val]['buckets'];
				}
				$tmp = [];
				foreach ($result_arr as $key => $value) {
					$tmp[$value['key']] = $value['doc_count'];
				}
				$enabledFilterItemsList[$agg_key] = $tmp;				
			}
		}

		return $enabledFilterItemsList;
	}

	private static function getAggregateArr(){
		return [
			'price_group' => [
				'range' => [
					'field' => 'pansion_price',
					'ranges' => [
						['to' => 1000],
						['from' => 1000]
					],
				]
			],
			'city_group' => [
				'nested' => [
					'path' => 'pansion_cities'
				],
				'aggs' => [
					'city' => [
						'terms' => [
							'field' => 'pansion_cities.id',
							'size' => 10000,
						]
					]
				]
			],
			'district_group' => [
				'nested' => [
					'path' => 'pansion_district'
				],
				'aggs' => [
					'district' => [
						'terms' => [
							'field' => 'pansion_district.id',
							'size' => 10000,
						]
					]
				]
			],
			'metro_group' => [
				'nested' => [
					'path' => 'pansion_metro'
				],
				'aggs' => [
					'metro' => [
						'terms' => [
							'field' => 'pansion_metro.id',
							'size' => 10000,
						]
					]
				]
			],
			'conditions_group' => [
				'nested' => [
					'path' => 'pansion_conditions'
				],
				'aggs' => [
					'conditions' => [
						'terms' => [
							'field' => 'pansion_conditions.id',
							'size' => 10000,
						]
					]
				]
			],
			'specials_group' => [
				'nested' => [
					'path' => 'pansion_specials'
				],
				'aggs' => [
					'specials' => [
						'terms' => [
							'field' => 'pansion_specials.id',
							'size' => 10000,
						]
					]
				]
			],
			'type_group' => [
				'nested' => [
					'path' => 'pansion_type'
				],
				'aggs' => [
					'type' => [
						'terms' => [
							'field' => 'pansion_type.id',
							'size' => 10000,
						]
					]
				]
			],
			'network_group' => [
				'nested' => [
					'path' => 'pansion_network'
				],
				'aggs' => [
					'network' => [
						'terms' => [
							'field' => 'pansion_network.id',
							'size' => 10000,
						]
					]
				]
			],
			'pansion_types_group' => [
				'nested' => [
					'path' => 'pansion_types'
				],
				'aggs' => [
					'pansion_types' => [
						'terms' => [
							'field' => 'pansion_types.id',
							'size' => 10000,
						]
					]
				]
			],
		];
	}
}
