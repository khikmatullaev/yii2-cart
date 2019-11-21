<?php
namespace dvizh\cart\widgets;

use yii\helpers\Html;

class ElementPrice extends \yii\base\Widget
{
    public $model = NULL;
    public $cssClass = NULL;
    public $htmlTag = 'span';
    
    public function init()
    {
        parent::init();
        return true;
    }
    
    public function run()
    {
        $format_price = round($this->model->price);
        return Html::tag($this->htmlTag, $format_price, [
            'class' => "cartfull-product-price dvizh-cart-element-price{$this->model->getId()} {$this->cssClass}",
        ]);
    }
}