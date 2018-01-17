<?php

namespace worstinme\zoo\elements\select_multiple;

use Yii;

class Element extends \worstinme\zoo\elements\BaseElementBehavior
{

    public function rules()
    {
        return [
            [$this->attribute, 'safe'],
            //[$attributes,'required'],
        ];
    }

    public $multiple = true;

    public $value_field = 'value_int';

    public function LoadAttributesFromElements($attribute)
    {
        $value = [];

        foreach ($this->owner->itemsElements as $element) {
            if ($element->element == $attribute) {

                if ($element->{$this->value_field} !== null)

                    $value[] = [
                        'id' => $element->id,
                        'value_text' => $element->value_text,
                        'value_int' => $element->value_int,
                        'value_string' => $element->value_string,
                        'value_float' => $element->value_float,
                    ];
            }
        }

        return $this->owner->values[$attribute] = $value;
    }

    public function setValue($value)
    {

        /*if (!isset($this->owner->values[$attribute])) {
            $this->loadAttributesFromElements($attribute);
        }*/

        $va = [];

        if (is_array($value)) {

            foreach ($value as $key => $v) {


                if ($v !== null) {

                    $a = [
                        'value_text' => null,
                        'value_int' => null,
                        'value_string' => null,
                        'value_float' => null,
                    ];

                    $a[$this->value_field] = $v;

                    $va[] = $a;
                }
            }

        }

        $this->owner->values[$attribute] = $va;

        return true;
    }
}