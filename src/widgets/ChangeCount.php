<?php
namespace dvizh\cart\widgets; 

use yii\helpers\Url;
use yii\helpers\Html;

class ChangeCount extends \yii\base\Widget
{
    public $model = NULL;
    public $lineSelector = 'li'; //Селектор материнского элемента, где выводится элемент
    public $downArr = '⟨';
    public $upArr = '⟩';
    public $cssClass = 'dvizh-change-count';
    public $defaultValue = 1;
    public $showArrows = true;
    public $actionUpdateUrl = '/cart/element/update';
    public $cartQuantityUrl = '/product/enough-cart-element-quantity';
    public $customView = false; // for example '@frontend/views/custom/changeCountLayout'

    public function init()
    {
        parent::init();

        \dvizh\cart\assets\WidgetAsset::register($this->getView());
        
        return true;
    }

    public function run()
    {
        if($this->showArrows) {
            $downArr = Html::tag('span', Html::button(Html::tag('span', "", ['class' => "glyphicon glyphicon-minus"]),  ['class' => 'btn btn-default btn-number']), ['class' => "dvizh-arr dvizh-downArr input-group-btn"]);
            $upArr =  Html::tag('span', Html::button(Html::tag('span', "", ['class' => "glyphicon glyphicon-plus"]),   ['class' => 'btn btn-default btn-number']), ['class' => "dvizh-arr dvizh-upArr input-group-btn"]);
            //Html::tag('span', "", ['class' => "glyphicon glyphicon-minus"]);
        } else {
            $downArr = $upArr = '';
        }

        if(!$this->model instanceof \dvizh\cart\interfaces\CartElement) {
            $input = Html::activeTextInput($this->model, 'count', [
                'type' => 'number',
                'class' => 'dvizh-cart-element-count form-control input-number',
                'data-role' => 'cart-element-count',
                'data-line-selector' => $this->lineSelector,
                'data-id' => $this->model->getId(),
                'data-href' => Url::toRoute($this->actionUpdateUrl),
                'data-cart-quantity-url' => Url::toRoute($this->cartQuantityUrl),
            ]);
        } else {
            $input = Html::input('number', 'count', $this->defaultValue, [
                'class' => 'dvizh-cart-element-before-count',
                'data-line-selector' => $this->lineSelector,
                'data-id' => $this->model->getCartId(),
                'data-role' => 'product-element-count',
            ]);
        }
        
        if ($this->customView) {
            return $this->render($this->customView, [
                'model' => $this->model,
                'defaultValue' => $this->defaultValue,
            ]);
        } else {
            return Html::tag('div', $downArr.$input.$upArr, ['class' => $this->cssClass. " input-group"]);
        }
    }
}
