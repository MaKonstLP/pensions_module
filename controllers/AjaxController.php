<?php
namespace app\modules\pensions\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use frontend\modules\pensions\components\UpdateFilterItems;

class AjaxController extends Controller
{

  public function actionAjaxUpdateFilter()
  {
    $newFilterItemState = UpdateFilterItems::getFilter(json_decode($_GET['filter'], true));
    return json_encode($newFilterItemState, JSON_FORCE_OBJECT);
  }
}
