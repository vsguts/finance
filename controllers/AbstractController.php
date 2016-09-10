<?php

namespace app\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Attachment;

class AbstractController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->session->open();
    }

    public function behaviors()
    {
        return [
            'ajax' => [
                'class' => 'app\behaviors\AjaxFilter',
            ],
        ];
    }

    public function redirect($url, $force = false, $statusCode = 302)
    {
        $params = Yii::$app->request->queryParams;

        // Meta redirect
        if (headers_sent() || ob_get_contents()) {
            $url = !empty($params['_return_url']) ? $params['_return_url'] : $url;
            $url = Url::to($url);
            $this->ech(Html::tag('meta', '', ['http-equiv' => 'Refresh', 'content' => '1;URL=' . $url . '']));
            $this->ech(Html::a(__('Continue'), $url));
        }

        if (!empty($params['_return_url']) && !$force) {
            return Yii::$app->getResponse()->redirect($params['_return_url'], $statusCode);
        }

        return parent::redirect($url, $statusCode);
    }

    protected function startLongProcess()
    {
        set_time_limit(0);
        ob_start('ob_gzhandler');
    }

    protected function ech($string)
    {
        echo($string);
        ob_flush();
    }

    /**
     * Send notice
     * @param  mixed  $text Text
     * @param  string $type text|error|info|warning
     */
    protected function notice($text, $type = 'success')
    {
        if (is_array($text)) {
            foreach ($text as $_text) {
                $this->notice($_text, $type);
            }
        } else {
            Yii::$app->session->addFlash($type, $text);
        }
    }

    protected function delete($object, array $id, $redirect_to_referrer = true)
    {
        $ok_message = false;
        if ($object::deleteAll(['id' => $id])) {
            if (count($id) > 1) {
                $ok_message = __('Items have been deleted successfully.');
            } else {
                $ok_message = __('Item has been deleted successfully.');
            }
        }

        if ($ok_message) {
            Yii::$app->session->setFlash('success', $ok_message);
        }

        if (
            $redirect_to_referrer
            && $referrer = Yii::$app->request->referrer
        ) {
            return $this->redirect($referrer);
        }

        return $this->redirect(['index']);
    }

    protected function download($path)
    {
        // basename workaround: Spaces in UTF-8
        $pos = strrpos($path, '/');
        $filename = substr($path, $pos + 1);
        
        return Yii::$app->response->sendFile($path, $filename);
    }

    /**
     * Finds a model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string  $class Model class
     * @param integer $id    Primary key
     * @return mixed the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($class, $id)
    {
        if ($primaryKey = $class::primaryKey()) {
            $tableName = $class::tableName();
            $field = $tableName . '.' . $primaryKey[0];
            $model = $class::find()->where([$field => $id])->permission()->one();
            if ($model) {
                return $model;
            }
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
