<?php

namespace frontend\modules\pensions\widgets;

use Yii;
use yii\bootstrap\Widget;
// use backend\models\Filter;
use common\models\Filter;

class FilterWidget extends Widget
{
	public $filter_active;
	public $filter_model;
	public $total = 0;
	public $subdomen_filter_active;

	public function run()
	{
		$filter = [];
		$required_filters = [];
		$filter_model = $this->filter_model;
		foreach ($filter_model as $filter_row) {
			
			$alias = $filter_row->alias;
			$filter[$alias] = [];
			$filter[$alias]['type'] = $filter_row->type;
			$filter[$alias]['sort'] = $filter_row->sort;
			$filter[$alias]['name'] = $filter_row->name;
			$filter[$alias]['active'] = null;
			foreach ($filter_row->items as $filter_item) {
				// $filter[$alias]['items'][$filter_item->value] = $filter_item->text;
				$filter[$alias]['items'][$filter_item->value]['name'] = $filter_item->text;
				$filter[$alias]['items'][$filter_item->value]['id'] = json_decode($filter_item->api_arr, true)[0]['value'];
			}
		}

		foreach ($this->filter_active as $key => $value_arr) {
			foreach ($value_arr as $value) {
				$filter[$key]['active'][$value] = 1;
			}
		}

		$subdomen_filter_active_arr = [];
		if ($this->subdomen_filter_active) {
			foreach ($this->subdomen_filter_active as $key => $value) {
				$subdomen_filter_active_arr[$key] = explode(',', $value);
			}
		}


		// echo ('<pre>');
		// print_r($filter);
		// exit;
		return $this->render('//components/filter/filter.twig', [
			'filters'                 => $filter,
			'filter_active'           => $this->filter_active,
			'total'                   => $this->total,
			'subdomen_filter_active'  => $subdomen_filter_active_arr
		]);
	}
}
