<?php

namespace worstinme\zoo\backend\models;

use worstinme\zoo\elements\BaseElementBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * BackendItemsSearch represents the model behind the search form about `worstinme\zoo\models\BackendItems`.
 */
class BackendItemsSearch extends BackendItems
{

    public $search;
    public $query;
    public $withoutCategory;
    public $language;

    public $isSearch = true;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['search'],'safe'],
            [['element_category'],'safe'],
            ['withoutCategory','integer'],
            [['language'],'string'],
        ];

        /* foreach ($this->getBehaviors() as $behavior) {
            if (is_a($behavior, BaseElementBehavior::className())) {
                /** @var $behavior BaseElementBehavior */
        /*      $rules = array_merge($rules, $behavior->rules());
          }
      } */

        return $rules;
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'search' => Yii::t('zoo', 'Поиск'),
            'withoutCategory'=>'Показать материалы без категорий',
            'language'=>Yii::t('zoo','ITEMS_LANGUAGE'),
        ]);

    }

    public function search($params)
    {

        $this->load($params);

        $this->query = parent::find()
            ->joinWith(['categories'])
            ->where([parent::tableName().'.app_id'=>$this->app_id]);

        if ($this->withoutCategory) {
            $this->query->andWhere('id NOT IN (SELECT DISTINCT item_id FROM {{%items_categories}} WHERE category_id > 0)');
        }

        $this->query->andFilterWhere([Categories::tablename().'.id'=>$this->categoryTree($this->element_category)]);
        $this->query->andFilterWhere(['LIKE',parent::tablename().'.name',$this->search]);
        $this->query->andFilterWhere([parent::tablename().'.lang'=>$this->language]);

        $query = clone $this->query;
        $query->orderBy('created_at DESC');

        return $dataProvider = new ActiveDataProvider([
            'query' => $query->groupBy(BackendItems::tablename().'.id'),
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per-page',30),
            ],
        ]);
    }

    public function itemIds($params)
    {
        $this->load($params);

        if ($this->withoutCategory) {
            $query = BackendItems::find()->select(BackendItems::tablename().'.id')->where([BackendItems::tablename().'.app_id' => $this->app_id ]);
            $query->andWhere(BackendItems::tablename().'.id NOT IN (SELECT DISTINCT item_id FROM {{%items_categories}} WHERE category_id > 0)');
        }
        elseif (!empty($this->category) && count($this->category)) {
            $query = BackendItems::find()->select(BackendItems::tablename().'.id');
            $query->leftJoin(['category'=>'{{%items_categories}}'], "category.item_id = ".BackendItems::tablename().".id");
            $query->andFilterWhere(['category.category_id'=>$this->category]);
        }
        else {
            $query = BackendItems::find()->select(BackendItems::tablename().'.id')->where([BackendItems::tablename().'.app_id' => $this->app_id ]);
        }

        foreach ($this->elements as $element) {

            $e = $element->name;

            if (!in_array($e, $this->attributes) && $element->filter) {

                if ((!is_array($this->$e) && $this->$e !== null) || (is_array($this->$e) && count($this->$e) > 0)) {

                    $query->leftJoin([$e=>'{{%items_elements}}'], $e.".item_id = ".BackendItems::tablename().".id AND ".$e.".element = '".$e."'");
                    $query->andFilterWhere([$e.'.value_string'=>$this->$e]);
                }

            }
        }

        $query->andFilterWhere(['LIKE',BackendItems::tablename().'.name',$this->search]);

        return $query->groupBy(BackendItems::tablename().'.id')->column();
    }

    public function formName()
    {
        return '';
    }
}
