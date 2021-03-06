<?php

use yii\helpers\Html;
use yii\widgets\Menu;

$app = Yii::$app->controller->app;

$subnav = [];

if (Yii::$app->controller->id == 'elements') {
    $subnav[] = ['label' => Yii::t('zoo', 'NAV_CREATE_ELEMENT'), 'url' => ['/zoo/elements/create', 'app' => $app->id]];
}

if (Yii::$app->controller->id == 'items') {
    $subnav[] = ['label' => Yii::t('zoo', 'NAV_CREATE_ITEM'), 'url' => ['/zoo/items/create', 'app' => $app->id]];
}

if (Yii::$app->controller->id == 'categories') {
    $subnav[] = ['label' => Yii::t('zoo', 'NAV_CREATE_CATEGORY'), 'url' => ['/zoo/categories/update', 'app' => $app->id]];
}

$mainnav = array_filter([
    ['label' => Yii::t('zoo', 'NAV_APPLICATION_ITEMS'),'url' => ['/zoo/items/index', 'app' => $app->id]],
    $app->categoriesEnable ? ['label' => Yii::t('zoo', 'NAV_APPLICATION_CATEGORIES'),'url' => ['/zoo/categories/index', 'app' => $app->id]] : null,
    ['label' => Yii::t('zoo', 'NAV_APPLICATION_ELEMENTS'),'url' => ['/zoo/elements/index', 'app' => $app->id]],
    ['label' => Yii::t('zoo', 'NAV_APPLICATION_SETTINGS'),'url' => ['/zoo/applications/view', 'app' => $app->id]],
]);


if (isset($items) && is_array($items) && count($items)) {
    $mainnav = array_merge($items, $subnav);
}

?>

<div class="subnav uk-navbar-container">
    <div class="uk-container uk-container-expand">
        <nav class="uk-navbar" uk-navbar>
            <div class="uk-navbar-left">
                <span class="uk-navbar-item" href="#"><i class="uk-margin-small-right" uk-icon="icon: thumbnails"></i><?=$app->title?></span>
                <?= Menu::widget([
                    'encodeLabels' => false,
                    'options' => ['class' => 'uk-navbar-nav uk-hidden-small'],
                    'activeCssClass' => 'uk-active',
                    'submenuTemplate' => "\n<div class=\"uk-navbar-dropdown\">\n<ul class=\"uk-nav uk-navbar-dropdown-nav\">\n{items}\n</ul>\n</div>\n",
                    'items' => $mainnav,
                ]); ?>
                <?= Menu::widget([
                    'options' => ['class' => 'uk-navbar-nav uk-hidden-small'],
                    'activeCssClass' => 'uk-active',
                    'submenuTemplate' => "\n<div class=\"uk-navbar-dropdown\">\n<ul class=\"uk-nav uk-navbar-dropdown-nav\">\n{items}\n</ul>\n</div>\n",
                    'encodeLabels' => false,
                    'items' => $subnav,
                ]); ?>
            </div>

        </nav>
    </div>
</div>