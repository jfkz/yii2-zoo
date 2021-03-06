<?php

namespace worstinme\zoo\backend\models;

use worstinme\zoo\elements\BaseElementBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\behaviors\TimestampBehavior;

class BackendItems extends \worstinme\zoo\models\Items
{
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        $rules = [
            [['state','flag','bolt'],'boolean'],
           //
        ];

        foreach ($this->getBehaviors() as $behavior) {
            if (is_a($behavior, BaseElementBehavior::className())) {
                /** @var $behavior BaseElementBehavior */
                $rules = array_merge($rules, $behavior->rules(), $behavior->rulesRequired());
            }
        }

        return $rules;
    }

    public function scenarios()
    {
        $scenarios = [
            'default'=>['state','flag','bolt'],
            'states'=>['state','flag','bolt'],
        ];

        foreach ($this->getBehaviors() as $behavior) {
            if (is_a($behavior, BaseElementBehavior::className())) {
                /** @var $behavior BaseElementBehavior */
                $scenarios = ArrayHelper::merge($scenarios, $behavior->scenarios);
            }
        }

        return $scenarios;
    }

    public function attributeLabels()
    {
        $labels = [
          'state' => Yii::t('zoo', 'LABEL_STATE'),
          'flag' => Yii::t('zoo', 'LABEL_FLAG'),
        ];
        if ($this->app) {
            foreach ($this->app->elements as $key => $element) {
                $labels[$key] = $element->label;
            }
            foreach ($this->app->systemElements as $key => $element) {
                $labels[$key] = $element->label;
            }
        }
        return $labels;
    }

    public function getRenderedElements()
    {
        $renderedElements = [];

        foreach ($this->behaviors as $behavior) {
            if (is_a($behavior, BaseElementBehavior::className())) {
                if ($behavior->isRendered) {
                    $renderedElements[] = $behavior->attribute;
                }
            }
        }

        return $renderedElements;
    }

}
