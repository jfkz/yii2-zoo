<?php

use yii\helpers\Html;

echo Html::activeInput('number', $model, $element->attributeName,['class'=>'uk-input','step'=>'0.01']);
