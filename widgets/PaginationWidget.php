<?php

// namespace app\modules\pensions\widgets;
namespace frontend\modules\pensions\widgets;

use Yii;
use yii\bootstrap\Widget;

class PaginationWidget extends Widget
{

	public $total;
	public $current;

	public function run()
	{
		$buttons = '<div class="items_pagination">';
		if ($this->total > 1) {
			if ($this->total > 4) {
				if ($this->current <= 2) {
					for ($i = 1; $i <= 3; ++$i) {
						$buttons .= $this->renderPageButton($i, '', $i == $this->current ? '_active' : '');
					}
					$buttons .= $this->renderPageButton($this->total, '_last', '');
				} elseif ($this->current >= ($this->total - 1)) {
					$buttons .= $this->renderPageButton(1, '_first', '');
					for ($i = $this->total - 2; $i <= $this->total; ++$i) {
						$buttons .= $this->renderPageButton($i, '', $i == $this->current ? '_active' : '');
					}
				} else {
					$buttons .= $this->renderPageButton(1, '_first', '');
					for ($i = $this->current - 1; $i <= $this->current + 1; ++$i) {
						$buttons .= $this->renderPageButton($i, '', $i == $this->current ? '_active' : '');
					}
					$buttons .= $this->renderPageButton($this->total, '_last', '');
				}
			} else {
				for ($i = 1; $i <= $this->total; ++$i) {
					$buttons .= $this->renderPageButton($i, '', $i == $this->current ? '_active' : '');
				}
			}
			$buttons .= '</div>';
			return $buttons;
		} else {
			return '';
		}
	}

	private function renderPageButton($page, $class, $active)
	{
		return '<div class="items_pagination_item ' . $active . ' ' . $class . '" data-page-id="' . $page . '" data-listing-pagitem></div>';
	}
}
