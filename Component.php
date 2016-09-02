<?php

namespace worstinme\zoo;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use worstinme\zoo\models\Applications;
use worstinme\zoo\backend\models\Widgets;
use worstinme\zoo\backend\models\Config;

class Component extends \yii\base\Component { 

	public $elementsPath = '@app/components/elements';
	public $defaultLang = 'ru';
	public $frontendPath;

	public $cke_editor_toolbar = [
		['Bold', 'Italic','Underline','-','NumberedList', 'BulletedList', '-', 'Link', 'Unlink','Styles','Font','FontSize','Format','TextColor','BGColor','-','Blockquote','CreateDiv','-','Image','Table','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Outdent','Indent','-','RemoveFormat','Source','Maximize']
	];

	public $cke_editor_css;
	public $languages = [];
	public $callbacks = [];

	private $menu;


	public function getMenu() {

		if ($this->menu === null) {
			$this->menu = new \worstinme\zoo\models\Menu;
		}

		return $this->menu;
	}

	public function config($name, $default = null) {

		if (($config = Config::find()->where(['name'=>$name])->one()) !== null) {
			return $config->value;
		}

		return $default;
	}

  	public function getLang() {
  	  	return Yii::$app->request->get('lang',$this->defaultLang);
    }

    public function getApplications() {
    	return Applications::find()->where(['app_alias'=>[$this->frontendPath,'@app']])->indexBy('id')->all();
    }

}