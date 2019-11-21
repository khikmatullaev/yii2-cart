<?php
namespace dvizh\cart\models\tools;

use dvizh\cart\models\Cart;
use yii\web\Session;
use yii;

class CartQuery extends \yii\db\ActiveQuery
{
    public function my()
    {
        $session = yii::$app->session;

        if(!$userId = yii::$app->user->id) {
            if (!$userId = $session->get('tmp_user_id')) {
                $userId = md5(time() . '-' . yii::$app->request->userIP . Yii::$app->request->absoluteUrl);
                $session->set('tmp_user_id', $userId);
            }
            $one = $this->andWhere(['tmp_user_id' => $userId])->limit(1)->one();
        } else {
            $one = $this->andWhere(['user_id' => $userId])->limit(1)->one();
        }

        if (!$one) {
            $one = new \dvizh\cart\models\Cart();
            $one->created_time = time();
            if(yii::$app->user->id) {
                $one->user_id = $userId;
            }
            else {
                $one->tmp_user_id = $userId;
            }
            $one->updated_time = time();
            $one->save();
        }
        
        return $one;
    }

    public function userId()
    {
        $session = yii::$app->session;

        if( !$userId = yii::$app->user->id )
            $userId = $session->get('tmp_user_id');

        return $userId;
    }

    public function updateUserCart($tempUserId)
    {
        Cart::deleteAll(['user_id' => Yii::$app->user->getId()]);
        $one = Cart::find()->where(['tmp_user_id' => $tempUserId])->one();

        if( !empty($one) )
        {
            $one->user_id = Yii::$app->user->getId();
            $one->tmp_user_id = '';

            $one->save(false);
        }
    }
}
