<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
?>
<section class="promo-cards" aria-label="Акции и спецпредложения">
    <h2 class="promo-cards__title">Акции и спецпредложения</h2>
    <?php if (empty($arResult['ITEMS'])): ?>
        <p class="promo-cards__empty">Сейчас нет доступных акций.</p>
    <?php else: ?>
        <div class="promo-cards__grid">
            <?php foreach ($arResult['ITEMS'] as $item): ?>
                <?php
                $this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item['IBLOCK_ID'], 'ELEMENT_EDIT'));
                $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item['IBLOCK_ID'], 'ELEMENT_DELETE'), ['CONFIRM' => 'Удалить акцию?']);
                ?>
                <article class="promo-cards__card" id="<?= $this->GetEditAreaId($item['ID']) ?>">
                    <?php if (!empty($item['PREVIEW_PICTURE']['SRC'])): ?>
                        <img class="promo-cards__image" src="<?= htmlspecialcharsbx($item['PREVIEW_PICTURE']['SRC']) ?>" alt="<?= htmlspecialcharsbx($item['PROMO_NAME']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="promo-cards__placeholder">Изображение отсутствует</div>
                    <?php endif; ?>
                    <div class="promo-cards__body">
                        <div class="promo-cards__badges">
                            <span class="promo-cards__badge"><?= htmlspecialcharsbx($item['BADGE']) ?></span>
                            <?php if ($item['IS_HOT']): ?>
                                <span class="promo-cards__badge promo-cards__badge--hot">🔥 Заканчивается!</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="promo-cards__name"><?= htmlspecialcharsbx($item['PROMO_NAME']) ?></h3>
                        <?php if ($item['PROMO_DESCRIPTION'] !== ''): ?>
                            <p class="promo-cards__description"><?= htmlspecialcharsbx($item['PROMO_DESCRIPTION']) ?></p>
                        <?php endif; ?>
                        <?php if ($item['DISCOUNT_PERCENT'] !== null): ?>
                            <p class="promo-cards__discount">Скидка <?= htmlspecialcharsbx((string) $item['DISCOUNT_PERCENT']) ?>%</p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
