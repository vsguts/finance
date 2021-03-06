<?php

namespace app\models;

use app\models\behaviors\TimestampBehavior;
use app\models\behaviors\UserPasswordBehavior;
use app\models\behaviors\UserRolesBehavior;
use app\models\components\LookupTrait;
use app\models\form\UserSignupForm;
use Yii;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $status
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 */
class User extends AbstractModel implements \yii\web\IdentityInterface
{
    use LookupTrait;

    const STATUS_ACTIVE = 'active';

    public $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            UserPasswordBehavior::class,
            UserRolesBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name', 'email', 'password', 'auth_key', 'password_hash'], 'required'],
            [['email'], 'unique', 'message' => __('This email address has already been taken.')],
            [['email'], 'email'],
            [['password'], 'string', 'min' => UserSignupForm::PASS_MIN_LEN],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'string'],
            [['name', 'email', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => __('ID'),
            'name' => __('Name'),
            'email' => __('E-mail'),
            'status' => __('Status'),
            'password' => __('Password'),
        ]);
    }


    /**
     * User
     */

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function canUpdate()
    {
        if (Yii::$app->user->can('user_manage_own', ['user' => $this])) {
            return true;
        }
        return Yii::$app->user->can('user_manage');
    }

}
