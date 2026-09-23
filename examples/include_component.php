<?php
// Фрагмент для страницы сайта ПОСЛЕ подключения /bitrix/header.php.
// Замените IBLOCK_TYPE и IBLOCK_ID значениями своего инфоблока.
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$APPLICATION->IncludeComponent('bitrix:news.list', 'promo_cards', [
    'IBLOCK_TYPE' => 'content',
    'IBLOCK_ID' => '1',
    'NEWS_COUNT' => '12',
    'SORT_BY1' => 'SORT',
    'SORT_ORDER1' => 'ASC',
    'SORT_BY2' => 'ID',
    'SORT_ORDER2' => 'DESC',
    'FIELD_CODE' => ['ID', 'IBLOCK_ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'DATE_ACTIVE_TO'],
    'PROPERTY_CODE' => ['DISCOUNT_PERCENT', 'BADGE'],
    'CHECK_DATES' => 'Y',
    'CACHE_TYPE' => 'N', // Без кеша: отметка зависит от текущего времени.
    'SET_TITLE' => 'N',
    'SET_BROWSER_TITLE' => 'N',
    'SET_META_KEYWORDS' => 'N',
    'SET_META_DESCRIPTION' => 'N',
    'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
    'ADD_SECTIONS_CHAIN' => 'N',
    'DISPLAY_TOP_PAGER' => 'N',
    'DISPLAY_BOTTOM_PAGER' => 'N',
], false);
