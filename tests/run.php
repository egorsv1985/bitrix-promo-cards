<?php
// Изолированная проверка исходников без ядра Битрикс.
// Заглушки ниже проверяют наш код, но не поведение CMS и её фильтров.
define('B_PROLOG_INCLUDED', true);
define('SITE_CHARSET', 'UTF-8');

class CSite
{
    public static function GetDateFormat($type) { return 'DD.MM.YYYY HH:MI:SS'; }
}

function MakeTimeStamp($date, $format)
{
    // Относительные фикстуры используют $now из самого result_modifier.php:
    // границы проверяются без зависимости от скорости выполнения теста.
    global $now;
    return preg_match('/^offset:(-?\d+)$/', $date, $matches) ? $now + (int) $matches[1] : false;
}

function htmlspecialcharsbx($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET);
}

class CIBlock
{
    public static function GetArrayByID($id, $key) { return ''; }
}

class TemplateHarness
{
    public function setFrameMode($enabled) {}
    public function AddEditAction($id, $link, $action) {}
    public function AddDeleteAction($id, $link, $action, $options) {}
    public function GetEditAreaId($id) { return 'promo-' . (int) $id; }
    public function render($arResult, $path)
    {
        ob_start();
        include $path;
        return ob_get_clean();
    }
}

$checks = 0;
function check($condition, $message)
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException($message);
    }
    ++$checks;
}

$cases = [
    ['offset:259199', '25', true, 'Суперцена'],
    ['offset:259200', '20', false, 'Выгода'],
    ['offset:345600', '20.5', false, 'Суперцена'],
    ['offset:1', '0', true, 'Выгода'],
    ['offset:0', '', false, 'Выгода'],
    ['offset:-1', 'wrong', false, 'Выгода'],
    ['', '15', false, 'Выгода'],
    ['invalid', '10', false, 'Выгода'],
];
$arResult = ['ITEMS' => []];
foreach ($cases as $index => $case) {
    $arResult['ITEMS'][] = [
        'ID' => $index + 1, 'IBLOCK_ID' => 1, 'EDIT_LINK' => '', 'DELETE_LINK' => '',
        'DATE_ACTIVE_TO' => $case[0],
        'PROPERTIES' => ['DISCOUNT_PERCENT' => ['VALUE' => $case[1]], 'BADGE' => ['VALUE' => 'Исходный']],
        '~NAME' => '<img src=x onerror=alert(1)>',
        '~PREVIEW_TEXT' => '<b>Описание &amp; детали</b>', 'PREVIEW_TEXT_TYPE' => 'html',
    ];
}
$path = dirname(__DIR__) . '/local/templates/.default/components/bitrix/news.list/promo_cards/';
require $path . 'result_modifier.php';
foreach ($cases as $index => $case) {
    check($arResult['ITEMS'][$index]['IS_HOT'] === $case[2], 'IS_HOT: case ' . $index);
    check($arResult['ITEMS'][$index]['BADGE'] === $case[3], 'BADGE: case ' . $index);
    check($arResult['ITEMS'][$index]['PROPERTIES']['BADGE']['VALUE'] === 'Исходный', 'Source property changed');
}
check($arResult['ITEMS'][2]['DISCOUNT_PERCENT'] === 20.5, 'Decimal discount');
check($arResult['ITEMS'][3]['DISCOUNT_PERCENT'] === 0.0, 'Zero discount');
check($arResult['ITEMS'][4]['DISCOUNT_PERCENT'] === null, 'Missing discount');
check($arResult['ITEMS'][5]['DISCOUNT_PERCENT'] === null, 'Invalid discount');
check($arResult['ITEMS'][0]['PROMO_DESCRIPTION'] === 'Описание & детали', 'HTML to text');
$template = new TemplateHarness();
$html = $template->render($arResult, $path . 'template.php');
check(strpos($html, '<img src=x') === false, 'Unsafe name markup');
check(strpos($html, '&lt;img src=x') !== false, 'Escaped name missing');
check(substr_count($html, '🔥 Заканчивается!') === 2, 'Hot badge count');
check(strpos($html, 'Скидка 0%') !== false, 'Zero discount not rendered');
check(strpos($html, 'Изображение отсутствует') !== false, 'Placeholder missing');
check(strpos($template->render(['ITEMS' => []], $path . 'template.php'), 'Сейчас нет доступных акций.') !== false, 'Empty state');

// Статический предпросмотр генерируется тем же PHP-шаблоном.
$arResult['ITEMS'] = array_slice($arResult['ITEMS'], 0, 3);
foreach ($arResult['ITEMS'] as $index => &$item) {
    $item['PROMO_NAME'] = ['Летняя коллекция', 'Товары для дома', 'Специальное предложение'][$index];
    $item['PROMO_DESCRIPTION'] = 'Демонстрационные данные для проверки внешнего вида карточек.';
}
unset($item);
$arResult['ITEMS'][0]['PREVIEW_PICTURE']['SRC'] = 'sample.svg';
$preview = '<!doctype html><html lang="ru"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Предпросмотр promo_cards</title><link rel="stylesheet" href="../local/templates/.default/components/bitrix/news.list/promo_cards/style.css"><style>body{margin:0;padding:0 20px;font-family:Arial,sans-serif;background:#f8fafc}main{max-width:1180px;margin:auto}.note{color:#525d6c;line-height:1.5}</style><main><p class="note">Статический предпросмотр на демонстрационных данных. Не является работающим сайтом на Битрикс.</p>';
$preview .= $template->render($arResult, $path . 'template.php') . '</main></html>';
file_put_contents(dirname(__DIR__) . '/preview/index.html', $preview);
echo 'OK: ' . $checks . " checks passed; preview generated.\n";
