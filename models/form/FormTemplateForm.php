<?php

namespace app\models\form;

use app\models\behaviors\TimestampConvertBehavior;
use app\models\FormTemplate;
use yii\base\Model;

class FormTemplateForm extends Model
{
    public $model;
    public $template;
    public $data;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampConvertBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'template'], 'required'],
            [['data'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'template' => __('Template'),
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $model = FormTemplate::find()->where(['model' => $this->model, 'template' => $this->template])->one();
        if (!$model) {
            $model = new FormTemplate;
        }

        $this->data = $this->prepareData();

        $model->load($this->attributes, '');
        return $model->save();
    }

    protected function prepareData()
    {
        parse_str($this->data, $data);
        $data = $data[$this->model];
        return array_filter($data);
    }

}
