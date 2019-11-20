<?php

namespace worstinme\zoo\elements\images;

use Yii;

class Behavior extends \worstinme\zoo\elements\BaseElementBehavior
{

	public function rules()
	{
		return [
			[$this->attribute,'each', 'rule' => ['string']],
		];
	}

    public function getMultiple() {
        return true;
    }

}
