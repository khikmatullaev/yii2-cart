<?php
use yii\helpers\Html;
use dvizh\cart\widgets\ChangeCount;
use dvizh\cart\widgets\DeleteButton;
use dvizh\cart\widgets\TruncateButton;
use dvizh\cart\widgets\ElementPrice;
use dvizh\cart\widgets\ElementCost;
use navatech\language\Translate;
use \app\models\Utility;

?>
<div class="dvizh-cart-row ">
    <div class="row-flex flex-center cartfull-product-item">
        <div class="col-flex col-xs-1 cartfull-product-image">
            <img src="<?=Yii::getAlias('@web').$image['120']?>" alt="">
        </div>
        <div class="col-flex col-xs-5 cartfull-product-name">
            <div>
            <?= $name ?>
            </div>

            <?php if ($options) {
                $productOptions = '';
                foreach ($options as $optionId => $valueId) {
                    if ($optionData = $allOptions[$optionId]) {
                        $option = $optionData['name'];
                        $value = $optionData['variants'][$valueId];
                        $productOptions .= Html::tag('div', Html::tag('span', $option) . ': <strong>' .Utility::datetimeFormat($value, "d.m.Y H:m").'</strong>');
                    }
                }
                echo Html::tag('div', $productOptions, ['class' => 'dvizh-cart-show-options']);
            } ?>

            <?php if(!empty($otherFields)) {
                foreach($otherFields as $fieldName => $field) {
                    if(isset($product->$field)) echo Html::tag('p', Html::tag('small', $fieldName.': '.$product->$field));
                }
            } ?>
        </div>

        <div class="col-flex col-xs-2 text-center cartfull-product-price">
            <?= ElementPrice::widget(['model' => $model]); ?>,- <?= Translate::cart_czk() ?>

        </div>

        <div class="col-flex col-xs-2 cartfull-product-count">
            <?= ChangeCount::widget([
                'model' => $model,
                'showArrows' => $showCountArrows,
                'actionUpdateUrl' => $controllerActions['update'],
            ]); ?>
        </div>

        <div class="col-flex col-xs-1 cartfull-product-delete text-center">
            <?= Html::tag('div', DeleteButton::widget([
                'model' => $model,
                'deleteElementUrl' => $controllerActions['delete'],
                'lineSelector' => 'dvizh-cart-row ',
                'cssClass' => 'delete']),
                ['class' => 'shop-cart-delete']);
            ?>
        </div>

    </div>

</div>