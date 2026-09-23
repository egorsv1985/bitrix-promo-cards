<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$now = time();
$hotPeriod = 3 * 24 * 60 * 60;

foreach ($arResult['ITEMS'] as &$item) {
    // Стандартное поле окончания активности, подтверждено в уточнении к ТЗ.
    $date = (string) ($item['DATE_ACTIVE_TO'] ?? $item['ACTIVE_TO'] ?? '');
    $expiresAt = $date !== '' ? MakeTimeStamp($date, CSite::GetDateFormat('FULL')) : false;
    $item['IS_HOT'] = $expiresAt !== false && $expiresAt > $now && ($expiresAt - $now) < $hotPeriod;

    $discount = $item['PROPERTIES']['DISCOUNT_PERCENT']['VALUE'] ?? '';
    $item['DISCOUNT_PERCENT'] = is_numeric($discount) ? (float) $discount : null;
    $item['BADGE'] = $item['DISCOUNT_PERCENT'] !== null && $item['DISCOUNT_PERCENT'] > 20 ? 'Суперцена' : 'Выгода';

    // Получаем исходные строки и экранируем один раз при выводе.
    $item['PROMO_NAME'] = (string) ($item['~NAME'] ?? $item['NAME'] ?? '');
    $item['PROMO_DESCRIPTION'] = (string) ($item['~PREVIEW_TEXT'] ?? $item['PREVIEW_TEXT'] ?? '');
    if (($item['PREVIEW_TEXT_TYPE'] ?? 'text') === 'html') {
        $item['PROMO_DESCRIPTION'] = html_entity_decode(strip_tags($item['PROMO_DESCRIPTION']), ENT_QUOTES | ENT_HTML5, SITE_CHARSET);
    }
}
unset($item);
