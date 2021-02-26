<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div id="custom-filter">
    Сортировка:
    <div class="custom-filter_item">
        <span  class="click" data-name = "NAME" data-sort="ASC">Наименование &uarr;</span>
        <span  class="click arrowDown" data-name = "NAME" data-sort="DESC">Наименование &darr;</span>
    </div>
    <div class="custom-filter_item">
        <span  class="click" data-name = "DATE_CREATE" data-sort="ASC">Дата &uarr;</span>
        <span class="click arrowDown" data-name = "DATE_CREATE" data-sort="DESC">Дата &darr;</span>
    </div>
    <div class="custom-filter_item">
        <span class="click" data-name = "SHOW_COUNTER" data-sort="ASC">Просмотры &uarr;</span>
        <span class="click arrowDown" data-name = "SHOW_COUNTER" data-sort="DESC">Просмотры &darr;</span>
    </div>
</div>