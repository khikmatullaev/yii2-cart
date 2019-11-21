<?php
use dvizh\cart\widgets\DeleteButton;
use dvizh\cart\widgets\TruncateButton;
use dvizh\cart\widgets\ChangeCount;
use dvizh\cart\widgets\CartInformer;
use dvizh\cart\widgets\ChangeOptions;
use dvizh\shop\widgets\ShowPrice;
use navatech\language\Translate;

$this->title = Translate::cart_title();
?>

<div class="cart">
    <h1><?= Translate::cart_title() ?></h1>

    <?=\dvizh\cart\widgets\ElementsList::widget(['type' => \dvizh\cart\widgets\ElementsList::TYPE_FULL])?>


    <?php foreach($elements as $element) { ?>
        <div class="row">
            <div class="col-lg-6 col-xs-6">
                <strong><?=$element->getModel()->getCartName();?> (
                    <?=ShowPrice::widget(['model' => $element]);?>
                )</strong>
                <br>

                <strong><?= $element->getCartElementOption()?></strong>
                <br><br>

            </div>
            <div class="col-lg-4 col-xs-4">
                <?=ChangeCount::widget(['model' => $element]);?>
            </div>
            <div class="col-lg-2 col-xs-2">
                <?=DeleteButton::widget(['model' => $element, 'lineSelector' => '.row']);?>
            </div>
        </div>
    <?php } ?>
    <div class="total">
        <?=CartInformer::widget(['htmlTag' => 'h3']); ?>
    </div>
    <div class="total">
        <?=TruncateButton::widget(); ?>
    </div>
</div>
