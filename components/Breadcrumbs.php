<?php

namespace frontend\modules\pensions\components;

use Yii;

class Breadcrumbs {
	public static function get_breadcrumbs($level) {

		$url = $_SERVER['REQUEST_URI'];

		switch ($level) {
			case 1:	
				$breadcrumbs=[
					'/' => 'Выпускной вечер '.(date("Y")+1),
					'/ploshhadki/' => 'Рестораны',
				];
				break;
			case 2:
				$breadcrumbs=[
					'/' => 'Выпускной вечер '.(date("Y")+1),
					'/ploshhadki/' => 'Рестораны',
				];
				break;
			case 3:
				$breadcrumbs=[
					'/' => 'Выпускной вечер '.(date("Y")+1),
					'/ploshhadki/' => 'Рестораны',
					$url => 'Залы',
				];
				break;
		}
		return $breadcrumbs;
	}
}