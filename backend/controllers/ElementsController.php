<?php
/**
 * @link https://github.com/worstinme/yii2-user
 * @copyright Copyright (c) 2014 Evgeny Zakirov
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace worstinme\zoo\backend\controllers;

use Yii;
use worstinme\zoo\backend\models\Elements;

use yii\web\NotFoundHttpException;

class ElementsController extends Controller
{

    public function actionIndex() {
        
        $app = $this->getApp();

        return $this->render('index', [
            'app' => $app,
        ]);

    }

    // редактирование/создание полей

    public function actionCreate() {

        $app = $this->getApp();

        $model = new Elements;

        $model->app_id = $app->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('backend','Элемент создан. Необходимо его настроить.'));
            $this->redirect(['update-element','app'=>$app->id,'element'=>$model->id]);
        }

        return $this->render('create', [
            'app' => $app,
            'model'=> $model,
        ]);
    }

    public function actionUpdate() {

        $app = $this->getApp();

        if (($model = Elements::findOne(Yii::$app->request->get('element'))) === null) {
            Yii::$app->getSession()->setFlash('success', Yii::t('backend','Элемент не найден'));
            return $this->redirect(['index','app'=>$app->id]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('backend','Настройка элемента сохранены'));
            return $this->redirect(['index','app'=>$app->id]);
        }

        return $this->render('update', [
            'app' => $app,
            'model'=> $model,
        ]);
    }

    public function actionDelete($element) {

        $app = $this->getApp();

        if (Yii::$app->request->isPost) {
            if (($fi= elements::findOne($element)) !== null) {
                $fi->delete();
            }
        }

        return $this->redirect(['elements','app'=>$app->id]);
    }


}