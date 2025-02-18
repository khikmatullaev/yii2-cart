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
    <div class="row-flex flex-center">
        <div class="col-flex col-xs-2">
            <img src="<?=Yii::getAlias('@web').$image['120']?>" alt="">
        </div>
        <div class="col-flex col-xs-9">
            <span class="minicart-product-name">
                <?= $name ?>
           </span>
            <span class="minicart-product-other">
            <?php if ($options) {
                $productOptions = '';
                foreach ($options as $optionId => $valueId) {
                    if ($optionData = $allOptions[$optionId]) {
                        $option = $optionData['name'];
                        $value = $optionData['variants'][$valueId];
                        $productOptions .= Html::tag('div', Html::tag('span', $option) . ': <strong>' .Utility::datetimeFormat($value, 'd.m.Y H:m').'</strong>',['class' => 'mini-datetime']);
                    }
                }
                echo Html::tag('div', $productOptions, ['class' => 'dvizh-cart-show-options']);
            } ?>

            <?php if(!empty($otherFields)) {
                foreach($otherFields as $fieldName => $field) {
                    if(isset($product->$field)) echo Html::tag('p', Html::tag('small', $fieldName.':  <strong>'.$product->$field . '</strong>'));
                }
            } ?>
                | <strong><?= $model->count ?><?= Translate::mini_pieces() ?></strong> | <strong><?= $model->price*$model->count ?>,- <?= Translate::mini_czk() ?></strong>
            </span>
        </div>
        <div class="col-flex col-xs-1 minicart-delete-container">
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