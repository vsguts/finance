<?php

namespace app\models;

/**
 * This is the model class for table "form_template".
 *
 * @property integer $id
 * @property string $model
 * @property string $template
 * @property string $data
 */
class FormTemplate extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'form_template';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => 'app\models\behaviors\EncoderBehavior',
                'fields' => 'data',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'template', 'data'], 'required'],
            [['model'], 'string', 'max' => 64],
            [['template'], 'string', 'max' => 128],
            [['model', 'template'], 'unique', 'targetAttribute' => ['model', 'template'], 'message' => 'The combination of Model and Template has already been taken.'],
            [['data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => __('ID'),
            'model' => __('Model'),
            'template' => __('Template'),
            'data' => __('Data'),
        ];
    }

    /**
     * Common methods
     *
     * @param $model
     * @param $template_id
     * @param array $lose_fields
     */
    
    public static function loadTemplate($model, $template_id, $lose_fields = [])
    {
        if ($template_id) {
            $template = self::find()->where([
                'id' => $template_id,
                'model' => $model->formName(),
            ])->one();
            if ($template) {
                $data = $template->data;
                foreach ((array)$lose_fields as $field) {
                    unset($data[$field]);
                }
                $model->load($data, '');
            }
        }
    }
}
