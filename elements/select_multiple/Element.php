<?php

namespace worstinme\zoo\elements\select_multiple;

use Yii;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

class Element extends \worstinme\zoo\elements\BaseElement
{

    public $iconClass = 'uk-icon-align-left';
    public $_multiple = true;

    public function getRules()
    {
        return [
            ['variant', 'each', 'rule'=>['string','max'=>255]],
            ['variantsParams','string','max'=>255],
            ['variantsType', 'integer'],
        ];
    }

    public function getLabels()
    {
        return [
            'variant' => Yii::t('backend', 'Варианты значений для выбора из списка'),
            'variantsType' => Yii::t('backend', 'Типы вариантов'),
            'variantsParams' => Yii::t('backend', 'Ключ массива значений из Yii::$app->params[key]'),
        ];
    }

    public function getConfigView() {
        return '@worstinme/zoo/elements/select_multiple/_settings';
    }

    public function getVariantsType(){
        return !empty($this->paramsArray['variantsType'])?$this->paramsArray['variantsType']:null;
    }

    public function setVariantsType($s){
        $params = $this->paramsArray;
        $params['variantsType'] = $s;
        return $this->paramsArray = $params;
    }

    public function getVariantsTypes() {
        return [
            'Yii::$app->params',
            'Введенные варианты',
            '-- Массив из настроек ZOO --',
        ];
    }

    public function getVariantsParams(){
        return !empty($this->paramsArray['variantsParams'])?$this->paramsArray['variantsParams']:null;
    }

    public function setVariantsParams($s){
        $params = $this->paramsArray;
        $params['variantsParams'] = $s;
        return $this->paramsArray = $params;
    }

    public function getVariants() {
        if ($this->variantsType == 0 && !empty($this->variantsParams) && !empty(Yii::$app->params[$this->variantsParams]) && is_array(Yii::$app->params[$this->variantsParams])) {
            return Yii::$app->params[$this->variantsParams];
        }
        elseif(is_array($this->variant)) {
            return $this->variant;
        }
        else {
            return [];
        }
    }

    public function getVariant()
    {
        return !empty($this->paramsArray['variant'])?$this->paramsArray['variant']:[];
    }

    public function setVariant($a)
    {
        $params = $this->paramsArray;

        if (is_array($a) && count($a)) {
	        foreach ($a as $key => $value) {
	        	if (empty($value)) {
	        		unset($a[$key]);
	        	}
	        }
        }

        $params['variant'] = $a;

        return $this->paramsArray = $params;
    }


}
