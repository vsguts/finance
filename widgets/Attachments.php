<?php

namespace app\widgets;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Url;

class Attachments extends InputWidget
{
    public $object_type = 'main';

    public function run()
    {
        $attachments = $this->model->getAttachments($this->object_type);
        if (!$attachments) {
            return '';
        }

        $name = Html::getInputName($this->model, $this->attribute);

        echo Html::beginTag('table', ['class' => 'table']);
        $headers = [
            Html::tag('th', __('Filename')),
            Html::tag('th', __('File size')),
            Html::tag('th', ''),
        ];
        echo Html::tag('tr', implode(' ', $headers));
        foreach ($attachments as $attachment) {
            $download_url = Url::to(['attachment/download', 'id' => $attachment->id]);
            $resturn_url = isset(Yii::$app->request->queryParams['_return_url'])
                ? Yii::$app->request->queryParams['_return_url']
                : Url::to();
            $delete_url = Url::to(['attachment/delete', 'id' => $attachment->id, '_return_url' => $resturn_url]);
            $delete_link = Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']), $delete_url, [
                'data-method' => 'post',
                'data-confirm' => __('Are you sure you want to delete this item?'),
                'title' => __('Delete'),
            ]);

            $columns = [
                Html::tag('td', Html::a($attachment->filename, $download_url)),
                Html::tag('td', Yii::$app->formatter->asShortSize($attachment->filesize)),
                Html::tag('td', $delete_link),
            ];
            echo Html::tag('tr', implode(PHP_EOL, $columns));
        }
        echo Html::endTag('table');
    }

}
