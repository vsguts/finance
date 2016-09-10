<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;
use yii\helpers\FileHelper;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * Attachment which stored in separate field
 */
class AttachmentBehavior extends Behavior
{
    public $dir;

    public $fields = [];

    public $filesize_fields = [];

    protected $_processed_fields = [];

    protected $_dir;


    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'attachEvent',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'attachEvent',
            ActiveRecord::EVENT_BEFORE_DELETE => 'detachEvent',
        ];
    }

    public function attachEvent($event)
    {
        $model = $this->owner;

        foreach ((array)$this->fields as $key => $field) {
            if (in_array($field, $this->_processed_fields)) {
                continue;
            }
            $this->_processed_fields[] = $field;

            $old_filename = $model->getOldAttribute($field);
            $upload = UploadedFile::getInstance($model, $field);
            if ($upload) {

                if ($old_filename) {
                    $dir = $this->getPath($field, true);
                    @unlink($dir . $old_filename);
                }

                list($filename, $filesize) = $this->saveUploaded($field, $upload);

                $model->{$field} = $filename;

                if (!empty($this->filesize_fields[$key])) {
                    $model->{$this->filesize_fields[$key]} = $filesize;
                }

                if ($event->name == ActiveRecord::EVENT_AFTER_INSERT) {
                    $res = $model->save();
                }
            } else {
                $model->{$field} = $old_filename;

            }
        }
    }

    public function detachEvent($event)
    {
        foreach ((array)$this->fields as $key => $field) {
            $file = $this->getPath($field);
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }

    public function getPath($field = null, $only_dir = false)
    {
        $model = $this->owner;

        $path = $this->getDir();

        if (!$only_dir) {
            if (is_null($field)) {
                $fields = (array)$this->fields;
                $field = reset($fields);
            }

            $path .= $model->$field;
        }

        return $path;
    }

    protected function getDir()
    {
        if (!$this->_dir) {
            $dir = preg_replace_callback('/\{([a-z0-9_]+)\}/Sui', function($m) {
                return $this->owner->{$m[1]};
            }, $this->dir);

            $dir = Yii::getAlias($dir);
            $this->_dir = rtrim($dir, '/') . '/';
        }

        return $this->_dir;
    }

    protected function saveUploaded($field, UploadedFile $file)
    {
        $dir = $this->getPath($field, true);

        $file_name = $file->getBaseName();
        $file_ext = $file->getExtension();

        $filename = $file_name .'.'. $file_ext;
        $index = 0;
        while (file_exists($dir . $filename)) {
            $index ++;
            $filename = $file_name . '-' . $index . '.' . $file_ext;
        }
        
        FileHelper::createDirectory($dir, 0777, true);
        $path = $dir . $filename;
        if ($file->saveAs($path)) {
            return [$filename, filesize($path)];
        }

        return false;
    }

}
