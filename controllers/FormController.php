<?php

namespace app\modules\pensions\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Html;
use frontend\modules\pensions\models\ElasticItems;
use frontend\modules\pensions\models\FormRequest;
use backend\models\Review;

class FormController extends Controller
{
	//public function getViewPath()
	//{
	//    return Yii::getAlias('@app/modules/svadbanaprirode/views/site');
	//}

	public function beforeAction($action)
	{
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}

	public function actionSend()
	{

		//сохранение заявок в базу
		if (isset($_POST) && !empty($_POST)) {
			$form_request = new FormRequest();
			$form_request->text = json_encode($_POST, JSON_FORCE_OBJECT);
			$form_request->date = date("Y.m.d");
			$form_request->save();
		}

		//сохранение отзывов со страницы пансионата
		if ($_POST['type'] == 'item-review') {
			$review = new Review();
			$review->title = $_POST['pansion-name'];
			$review->text = $_POST['comment'];
			$review->author = $_POST['name'];
			$review->date = date("d.m.Y");
			$review->rating = $_POST['rating'];
			$review->pansion_id = $_POST['pansion-id'];
			$review->active = 0;
			$review->save();
		}
		// return json_encode($_POST, JSON_FORCE_OBJECT);

		$to = ['mh@liderpoiska.ru', 'medpension@gmail.com'];

		// if ($_POST['type'] == 'main' or $_POST['type'] == 'header') {
		// 	$subj = "Заявка на выбор зала.";
		// } else {
		// 	$subj = "Заявка на бронирование зала.";
		// }
		$subj = 'Заявка с сайта';

		$msg  = "";

		$post_string_array = [
			'title'		=>	'Заголовок формы',
			'url'			=>	'Адрес страницы, с которой отправлена форма',
			'pansion-name' => 'Название учреждения',
			'pansion-url' 	=> 'Адрес страницы учреждения',
			'name'		=>	'Имя',
			'email'		=>	'Email',
			'phone'		=>	'Телефон',
			'date-in'	=>	'Дата заезда',
			'date-out'	=>	'Дата выезда',
			'location'	=>	'Местоположение',
			'disease'	=>	'Заболевание',
			'condition'	=>	'Требования',
			'comment'	=>	'Комментарий',
			'rating'		=>	'Оценка',
			'physical'	=>	'Физическое состояние',
			'mental'		=>	'Психическое состояние',
			'capacity'	=>	'Количество мест в номере',
			
		];

		foreach ($post_string_array as $key => $value) {
			if (isset($_POST[$key]) && $_POST[$key] != '') {
				$msg .= $value . ': ' . $_POST[$key] . '<br/>';
			}
		}

		$message = $this->sendMail($to, $subj, $msg);

		if ($message) {
			$responseMsg = empty($responseMsg) ? 'Успешно отправлено!' : $responseMsg;
			$resp = [
				'error' => 0,
				'msg' => $responseMsg,
				'name' => isset($_POST['name']) ? $_POST['name'] : '',
				'phone' => isset($_POST['phone']) ? $_POST['phone'] : '',
			];
		} else {
			$resp = ['error' => 1, 'msg' => 'Ошибка']; //.serialize($_POST)
		}
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $resp;
	}

	public function sendMail($to, $subj, $msg)
	{
		$message = Yii::$app->mailer->compose()
			->setFrom(['hospices@yandex.ru' => 'Учреждения для лежачих больных'])
			->setTo($to)
			->setSubject($subj)
			->setCharset('utf-8')
			//->setTextBody('Plain text content')
			->setHtmlBody($msg);
		
		return $message->send();
	}
}
