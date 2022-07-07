<?php

namespace frontend\modules\pensions\models;

use common\models\siteobject\BaseSiteObject;
use Yii;
use yii\db\ActiveRecord;

// class PansionMain extends BaseSiteObject
class FormRequest extends ActiveRecord
{
	// метод который возвращает имя таблицы в базе данных с которой нужно работать в данном случае "form_request"
	public static function tableName()
	{
		return 'form_request';
	}
}
