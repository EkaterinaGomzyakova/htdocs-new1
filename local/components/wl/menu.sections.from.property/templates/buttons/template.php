<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="row scope-switch-list">
    <?php foreach ($arResult as $menuItem) { ?>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
            <div class="item">
                <div class="name">
                    <a href="<?= $menuItem[1] //DETAIL_PAGE_URL
                                ?>">
                        <?= $menuItem[0] //name 
                        ?>
                    </a>
                </div>
            </div>
        </div>
    <? } ?>
</div>