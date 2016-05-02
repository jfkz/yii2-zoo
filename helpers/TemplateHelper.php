<?php

namespace worstinme\zoo\helpers;

use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Html;

class TemplateHelper
{
    public static $types = [
        'column-1' => ['tag'=>false],
        'column-2' => ['tag'=>'div','options'=>['class'=>'uk-grid uk-grid-width-medium-1-2 uk-grid-match'],'itemTag'=>'div'],
        'column-3' => ['tag'=>'div','options'=>['class'=>'uk-grid uk-grid-width-medium-1-3'],'itemTag'=>'div'],
        'double-2-1' => ['tag'=>'div','options'=>['class'=>'uk-grid uk-grid-width-medium-2-3'],'itemTag'=>'div'],
        'double-1-2' => ['tag'=>'div','options'=>['class'=>'uk-grid uk-grid-width-medium-1-3'],'itemTag'=>'div'],
        'triple-3-1' => ['tag'=>'div','options'=>['class'=>'uk-grid uk-grid-width-medium-3-4'],'itemTag'=>'div'],
        'triple-1-3' => ['tag'=>'div','options'=>['class'=>'uk-grid uk-grid-width-medium-1-4'],'itemTag'=>'div'],
        'list' => ['tag'=>'ul','options'=>['class'=>'uk-list'],'itemTag'=>'li'],
        'space' => ['delimiter'=>"\n"],
        'comma'     => ['delimiter'=>', '],
    ];

    public static function types() {
        return array_keys(self::$types);
    }

    public static function render($model,$templateName) {

        if ($model === null) {
            throw new InvalidParamException("wrong model");
        }

        $template = $model->getTemplate($templateName);

        if (is_array($template['rows']) && count($template['rows'])) {

            foreach ($template['rows'] as $position=>$row) {

                self::renderRow($model,$row);

            }
        }

    }

	public static function renderPosition($model,$templateName,$position) {

        if ($model === null) {
            throw new InvalidParamException("wrong model");
        }

        $template = $model->getTemplate($templateName);

        if (is_array($template['rows']) && !empty($template['rows'][$position])) {

            self::renderRow($model,$template['rows'][$position]);
            
        }
	}

    protected static function renderRow($model,$row) {

        if (!empty($row['items'])) {

            $items = static::renderItems($model,$row['items']);

            if (count($items)) {

                $type = !empty($row['type']) && in_array($row['type'],self::types()) ? 
                            self::$types[$row['type']] : 
                            ['tag'=>null,'class'=>null,'delimiter'=>"\n"];

                if (!empty($type['tag']) && !empty($type['options'])) {
                    echo Html::beginTag($type['tag'],$type['options']);
                }
                
                foreach ($items as $elements) {

                    if (!empty($type['itemTag'])) {
                        echo Html::beginTag($type['itemTag']);
                    }
                    
                    foreach ($elements as $element) {
                        echo $element;
                    }

                    if (!empty($type['itemTag'])) {
                        echo Html::endTag($type['itemTag']);
                    } 

                }

                if (!empty($type['tag'])) {
                    echo Html::endTag($type['tag']);
                }

            }

        }

    }

    protected static function renderItems($model,$items,$array=[]) {

        if (is_array($items) && count($items)) {

            foreach ($items as $item) {

                $elements = [];

                if (!empty($item['element'])) {

                    if (($a = self::renderElement($model, $item['element'], !empty($item['params'])?$item['params']:[])) !== null) {
                        $elements[] = $a;
                    }

                    if (!empty($item['items']) && is_array($item['items'])) {
                        
                        foreach ($item['items'] as $it) {
                             if (!empty($it['element'])) {
                                if (($b = self::renderElement($model, $it['element'], !empty($it['params'])?$it['params']:[])) !== null) {
                                    $elements[] = $b;
                                }
                            }
                        }

                    }

                }

                if (count($elements)) {
                    $array[] = $elements;
                }

            }

        }

        return $array;

    }

    protected static function renderElement($model,$element,$params = []) {

        if (!empty($model->elements[$element])) {
            
            return '<div class="element element-'.$element.'">'.Yii::$app->view->render('@worstinme/zoo/elements/'.$model->elements[$element]->type.'/view.php',[
                'model'=>$model,
                'attribute'=>$element,
                'params'=>$params,
            ]).'</div>'; 

        }

        return null;

    }

}