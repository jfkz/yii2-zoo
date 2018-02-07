<?php

namespace worstinme\zoo\components;

use worstinme\zoo\backend\models\Categories;
use worstinme\zoo\backend\models\Items;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\UrlRuleInterface;
use yii\base\BaseObject;

class UrlRule3 extends BaseObject implements UrlRuleInterface
{
    /** @var string ZOO application id */
    public $app_id;

    /** @var string Application`s frontend host */
    public $host;

    /** @var string Application`s url name */
    public $app_url;

    /** @var string Application`s url name */
    public $empty_lang;

    /** @var string Application`s url name */
    public $suffix;

    /** @var string Application`s url name */
    public $item_suffix = '';

    public $cache_duration = 3600;

    public function getApp()
    {
        return Yii::$app->zoo->getApplication($this->app_id);
    }

    public function init()
    {
        if ($this->app_url === null) {
            $this->app_url = $this->app_id;
        }

        parent::init(); // TODO: Change the autogenerated stub
    }

    //TODO: продумать переопредление классов Items и т.п.

    public function createUrl($manager, $route, $params)
    {
        $url = false;

        if (strpos($route, $this->app_id . '/') === 0) {

            Yii::beginProfile('url', __METHOD__);

            if ($route === $this->app_id . '/item') {

                if (isset($params['lang'], $params['id'])) {

                    // ссылка на материал

                    $cache_key = $route . '_' . $params['id'] . '_' . $params['lang'];

                    if (($url = Yii::$app->cache->get($cache_key)) === false) {

                        if (($model = Items::findOne(['lang' => $params['lang'], 'id' => $params['id'], 'app_id' => $this->app_id])) !== null) {

                            $parts = [];

                            if ($params['lang'] != $this->empty_lang) {
                                $parts[] = $params['lang'];
                            }

                            if (!empty($this->app_url)) {
                                $parts[] = $this->app_url;
                            }

                            if (($cats = $this->getParentCategoryUrl($model)) !== null) {
                                $parts[] = $cats;
                            }

                            $parts[] = $model->alias;

                            $url = implode("/", $parts);

                            $url .= $this->item_suffix !== null ? $this->item_suffix : ($this->suffix !== null ? $this->suffix : $manager->suffix);

                            // Yii::$app->cache->set($cache_key, $url, 30);
                        }

                    }

                    if ($url !== false) {

                        unset($params['id']);
                        unset($params['lang']);

                        if (!empty($params) && ($query = http_build_query($params)) !== '') {
                            $url .= '?' . $query;
                        }

                        Yii::endProfile('url', __METHOD__);

                        return $this->AppHost . '/' . $url;

                    }

                }

            } elseif ($route === $this->app_id . '/category') {

                if (isset($params['lang'], $params['id'])) {

                    // ссылка на материал

                    $cache_key = $route . '_' . $params['id'] . '_' . $params['lang'];

                    if (($url = Yii::$app->cache->get($cache_key)) === false) {

                        if (($model = Categories::findOne(['lang' => $params['lang'], 'id' => $params['id'], 'app_id' => $this->app_id])) !== null) {

                            $parts = [];

                            if ($params['lang'] != $this->empty_lang) {
                                $parts[] = $params['lang'];
                            }

                            if (!empty($this->app_url)) {
                                $parts[] = $this->app_url;
                            }

                            if (($cats = $this->getParentCategoryUrl($model)) !== null) {
                                $parts[] = $cats;
                            }

                            $parts[] = $model->alias;

                            $url = implode("/", $parts);

                            $url .= $this->suffix !== null ? $this->suffix : $manager->suffix;

                            // Yii::$app->cache->set($cache_key, $url, 30);
                        }

                    }

                    if ($url !== false) {

                        unset($params['id']);
                        unset($params['lang']);

                        if (!empty($params) && ($query = http_build_query($params)) !== '') {
                            $url .= '?' . $query;
                        }

                        $url = $this->AppHost . '/' . $url;

                        Yii::endProfile('url', __METHOD__);

                        return $url;

                    }

                }

            } elseif (isset($params['lang'])) {

                $p = explode("/", $route);

                if (count($p) == 2) {

                    $cache_key = $route . '_' . $params['lang'];

                    if (($url = Yii::$app->cache->get($cache_key)) === false) {

                        $parts = [];

                        if ($params['lang'] != $this->empty_lang) {
                            $parts[] = $params['lang'];
                        }

                        if (!empty($this->app_url)) {
                            $parts[] = $this->app_url;
                        }

                        if ($p[1] !== 'index') {
                            $parts[] = $p[1];
                        }

                        $url = implode("/", $parts);

                        if ($url !== '') {
                            $url .= $this->suffix !== null ? $this->suffix : $manager->suffix;
                        }

                        // Yii::$app->cache->set($cache_key, $url, 30);

                    }

                    if ($url !== false) {

                        unset($params['lang']);

                        if (!empty($params) && ($query = http_build_query($params)) !== '') {
                            $url .= '?' . $query;
                        }

                        $url = $this->AppHost . '/' . $url;

                        Yii::endProfile('url', __METHOD__);

                        return $url;

                    }

                }

            }


        }

        Yii::endProfile('url', __METHOD__);

        return false;

    }

    public function parseRequest($manager, $request)
    {

        if ($this->app->app_id === null || $this->app->app_id == Yii::$app->id) {

        //    Yii::$app->cache->flush();

            Yii::beginProfile('parse for ' . $this->app_id, __METHOD__);

            $pathInfo = $request->getPathInfo();

            if (($cache = Yii::$app->cache->get($pathInfo)) === false) {

                $languages = [];

                if (count(Yii::$app->zoo->languages)) {
                    foreach (Yii::$app->zoo->languages as $language => $value) {
                        $languages[$language] = $language == $this->empty_lang ? '' : $language;
                    }
                } else {
                    $languages = [Yii::$app->language => ''];
                }

                foreach ($languages as $lang => $language) {

                    $parts = [];

                    if ($language !== '') {
                        $parts[] = $language;
                    }

                    if (!empty($this->app_url)) {
                        $parts[] = $this->app_url;
                    }

                    $left_part = implode("/", $parts);

                    if ($left_part === '' || strpos($pathInfo, $left_part) === 0) {

                        $left_part_length = strlen($left_part);

                        $right_path = substr($pathInfo, $left_part_length, strlen($pathInfo));

                        $suffix = (string)($this->suffix === null ? $manager->suffix : $this->suffix);

                        if (($left_part === '' && $right_path === '') || $right_path === $suffix) {

                            Yii::endProfile('parse for ' . $this->app_id, __METHOD__);

                            return [
                                $this->app_id . '/index',
                                ['lang' => $lang],
                            ];

                        }

                        $right_path_length = strlen($right_path);

                        if ($right_path_length < 1) {
                            continue;
                        }

                        if ($left_part !== '') {
                            if ($right_path[0] === '/') {
                                $right_path = substr($right_path, 1);
                                $right_path_length--;
                            } else {
                                continue;
                            }
                        } elseif ($right_path[0] === '/') {
                            continue;
                        }

                        $item_suffix = (string)($this->item_suffix === null ? $suffix : $this->item_suffix);

                        $item_suffix_length = strlen($item_suffix);

                        if ($right_path_length > $item_suffix_length) {

                            $append = substr($right_path, -$item_suffix_length, $item_suffix_length);

                            if ($append === $item_suffix) {

                                if (substr($path = substr($right_path, 0, $right_path_length - $item_suffix_length), -1) !== '/') {

                                    $parts = explode("/", $path);

                                    $query = (new Query())->select('i.id')
                                        ->from(['i' => Items::tableName()])
                                        ->where(['i.alias' => array_pop($parts), 'i.lang' => $lang, 'i.app_id' => $this->app_id])
                                        ->groupBy('i.id');

                                    if (($part = array_pop($parts)) !== null) {
                                        $query
                                            ->leftJoin(['ic' => '{{%items_categories}}'], 'ic.item_id = i.id')
                                            ->leftJoin(['c0' => '{{%categories}}'], 'c0.id = ic.category_id')
                                            ->andWhere(['c0.alias' => $part]);
                                    }

                                    $i = 0;
                                    while (($part = array_pop($parts)) !== null) {
                                        $pi = $i;
                                        $i++;
                                        $query
                                            ->leftJoin(["c$i" => '{{%categories}}'], "c$i.id = c$pi.parent_id")
                                            ->andWhere(["c$i.alias" => $part]);;
                                    }

                                    if (($id = $query->scalar()) !== false) {

                                        $data = [
                                            $this->app_id . '/item',
                                            ['lang' => $lang, 'id' => $id],
                                        ];

                                        Yii::$app->cache->set($pathInfo,$data, $this->cache_duration);

                                        Yii::endProfile('parse for ' . $this->app_id, __METHOD__);

                                        return $data;

                                    }

                                }

                            }

                        }

                        $suffix_length = strlen($suffix);

                        if ($right_path_length > $suffix_length) {

                            if ($item_suffix !== $suffix) {
                                $append = substr($right_path, -$suffix_length, $suffix_length);
                            }

                            if ($append === $suffix) {
                                if (substr($path = substr($right_path, 0, $right_path_length - $suffix_length), -1) !== '/') {

                                    $parts = explode("/", $path);

                                    $query = (new Query())->select('c0.id')
                                        ->from(['c0' => Categories::tableName()])
                                        ->where(['c0.alias' => array_pop($parts), 'c0.lang' => $lang, 'c0.app_id' => $this->app_id]);

                                    $i = 0;
                                    while (($part = array_pop($parts)) !== null) {
                                        $pi = $i;
                                        $i++;
                                        $query
                                            ->leftJoin(["c$i" => '{{%categories}}'], "c$i.id = c$pi.parent_id")
                                            ->andWhere(["c$i.alias" => $part]);
                                    }

                                    if (($id = $query->scalar()) !== false) {

                                        $data = [
                                            $this->app_id . '/category',
                                            ['lang' => $lang, 'id' => $id],
                                        ];

                                        Yii::$app->cache->set($pathInfo,$data, $this->cache_duration);

                                        Yii::endProfile('parse for ' . $this->app_id, __METHOD__);

                                        return $data;

                                    }
                                }
                            }

                        }

                        // правила для ссылок на другие экшены приложения
                    }

                }

            }

            Yii::endProfile('parse for ' . $this->app_id, __METHOD__);

            return $cache;

        }

        return false; // this rule does not apply
    }

    protected function getAppHost()
    {
        if ($this->app->app_id !== null && $this->app->app_id != Yii::$app->id) {
            if ($this->host === null) {
                throw new InvalidConfigException('Application`s host should be defined');
            }
            return rtrim($this->host, "/");
        }
        return null;
    }

    protected function getParentCategoryUrl($model, $url = null)
    {
        if ($model !== null && $model->parentCategory) {
            return $this->getParentCategoryUrl($model->parentCategory, $model->parentCategory->alias . ($url !== null ? '/' . $url : null));
        }
        return $url;
    }
}