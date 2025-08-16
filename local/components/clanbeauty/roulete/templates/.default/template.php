<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/amcharts-core.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/amcharts-charts.js'); ?>

<div class="roulete-container">
    <div class="roulete-form">
        <a class="close" href="#" onclick="roulete.closeWindow(event);">X</a>
        <div class="hero-heading"></div>
        <div class="roulete-heading"><?= $arResult['USER_NAME'] ?>, Вы участвуете в розыгрыше приза за ваш заказ!</div>
        <div id="order-id" class="roulete-heading">Участвует заказ №<?= $arResult['ORDER_ID']?></div>
        
        <form id="agreement-form">
            <div class="form-group">
                <input type="checkbox" id="roulete_agreement" name="roulete_agreement" required>
                <label for="roulete_agreement">Я принимаю <a href="#">условия акции</a></label>
            </div>
        </form>
        
        <a href="#" id="roll-btn" class="btn btn-roulete btn-default" onclick="roulete.roll()">КРУТИТЬ</a>

        <div class="again" style="display: none;">
            <a href="/" class="btn btn-roulete btn-default">Сыграть еще раз</a>
            <div class="roulete-order-count" style="display: none;"></div>
        </div>
        
        <div id="roulete-message" style="display: none;"><small>Запускаем рулетку</small></div>

        <div class="roulete-circle-container">
            <div id="roulete-circle"></div>
        </div>

    </div>
</div>
<script defer>
    let roulete;
    $(document).ready(function() {
        setTimeout(function() {
            roulete = new Roulete('<?= $this->getComponent()->getSignedParameters()?>');
            const prizes = <?= CUtil::PhpToJSObject($arResult['PRIZES']['JS_DATA']); ?>;
            roulete.initChart(prizes);
        }, 1500);
    });
</script>