<?php

namespace app\models;

use app\models\Helper;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "test_user".
 *
 * @property string $user_id
 * @property string $user_name
 * @property string $user_password
 * @property string $user_access_token
 * @property string $user_auth_key
 * @property string $user_create_time
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{

    public $user_password2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%backend_user_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'user_password','user_password2'], 'required'],
            [['user_name'],'match','pattern'=>'/^1[3|5|7|8|][0-9]{9}$/','message'=>'必须是手机格式' ],
            [['user_create_time'], 'safe'],
            [['user_name'], 'unique','message'=>'{attribute}已经被占用了'],
            [['user_name', 'user_password'], 'string', 'max' => 100],
            [['user_access_token', 'user_auth_key'], 'string', 'max' => 200],
            [['user_password2'],'compare',"compareAttribute"=>'user_password','message'=>"两次密码不一致"],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_name' => '用户名',
            'user_passwd' => '密码',
//            'user_password2' => '确认密码',
//            'user_access_token' => 'User Access Token',
//            'user_auth_key' => 'User Auth Key',
//            'user_create_time' => 'User Create Time',
        ];
    }


    public static function findIdentity($id)
    {
        //自动登陆时会调用
        $temp = parent::find()->where(['user_id'=>$id])->one();
        return isset($temp)?new static($temp):null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['user_access_token' => $token]);
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getUserName()
    {
        return $this->user_name;
    }

    public function getAuthKey()
    {
//        return $this->user_auth_key;
    }

    public function validateAuthKey($authKey)
    {
//        return $this->user_auth_key === $authKey;
    }


    public function validatePassword($password)
    {


        return $this->user_passwd === $password;

    }

    public static function getUserArray(){
        $connection = Yii::$app->db;
        $sql = "SELECT user_id FROM `auth_assignment` WHERE item_name = 'DeUser';";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        $connection->close();
        $user_list = Helper::array_column ( $result ,  'user_id' );
        $user_ids = implode(',',$user_list);
        if($user_ids){
            $connection_authz = Yii::$app->authz;
            $sql_2 = "SELECT user_name FROM backend_user_list WHERE user_id in ("."$user_ids".");";
            $command_authz = $connection_authz->createCommand($sql_2);
            $result_authz = $command_authz->queryAll();
            $connection_authz->close();

            return Helper::array_column ( $result_authz ,  'user_name' ,'user_name' );
        }else{
            return array();
        }

    }
}
