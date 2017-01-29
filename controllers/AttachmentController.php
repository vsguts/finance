<?php

namespace app\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use app\models\Attachment;

class AttachmentController extends AbstractController
{

    protected $permissionsSchema = [
        'transaction' => [
            'view' => 'transaction_view',
            'manage' => 'transaction_edit',
        ],
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionDelete($id, $_return_url = null)
    {
        $model = $this->findModel($id, Attachment::className());
        $this->checkPermission($model, true);
        if ($model->delete()) {
            if ($model->object) {
                $model->object->updateHasAttachmentsFlags();
            }
            Yii::$app->session->setFlash('success', __('Item has been deleted successfully.'));
        }
        return $this->redirect($_return_url);
    }

    public function actionDownload($id)
    {
        $model = $this->findModel($id, Attachment::className());
        $this->checkPermission($model);
        return $this->download($model->getPath());
    }


    protected function checkPermission($model, $manage = false)
    {
        if (!empty($this->permissionsSchema[$model->table])) {
            $permission = $manage
                ? $this->permissionsSchema[$model->table]['manage']
                : $this->permissionsSchema[$model->table]['view'];
        } else {
            $suffix = $manage ? 'manage' : 'view';
            $permission = $model->table . '_' . $suffix;
        }

        if (!Yii::$app->user->can($permission)) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }

}
