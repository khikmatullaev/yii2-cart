<?php
namespace dvizh\cart\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use navatech\language\Translate;
use yii;

class CartInformer extends \yii\base\Widget
{

    public $text = NULL;
    public $offerUrl = NULL;
    public $cssClass = NULL;
    public $htmlTag = 'span';
    public $showOldPrice = true;

    public function init()
    {
        parent::init();

        \dvizh\cart\assets\WidgetAsset::register($this->getView());

        if ($this->offerUrl == NULL) {
            $this->offerUrl = Url::toRoute(["/cart/default/index"]);
        }
        
        if ($this->text === NULL) {
            $this->text = '<p style="font-size:14px">V košíku máte celkem {c} ks položek<span class="hide hiddeh"></span></p>'. Translate::cart_total_text().'<span class="pull-right"> {p}</span>';
        }

        return true;
    }

    public function run()
    {
        $cart = yii::$app->cart;

        if($this->showOldPrice == false | $cart->cost == $cart->getCost(false)) {
            $this->text = str_replace(['{c}', '{p}'],
                ['<span class="dvizh-cart-count">'.$cart->getCount().'</span>', '<strong class="dvizh-cart-price">'.$cart->getCost(false). ',- ' . Translate::total_czk() . '</strong>'],
                $this->text
            );
        } else {
            $this->text = str_replace(['{c}', '{p}'],
                ['<span class="dvizh-cart-count">'.$cart->getCount().'</span>', '<strong class="dvizh-cart-price"><s>'.round($cart->getCost(false)).'</s>,- '.$cart->getCostFormatted().'</strong>'],
                $this->text
            );
        }
        
        return Html::tag($this->htmlTag, $this->text, [
                'href' => $this->offerUrl,
                'class' => "dvizh-cart-informer {$this->cssClass}",
        ]);
    }
}
