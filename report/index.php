<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER, $APPLICATION;
$APPLICATION->ShowCSS(true, $bXhtmlStyle);
$APPLICATION->ShowHeadStrings();

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/intranet/public/configs/index.php");
$APPLICATION->SetTitle("Формы");
$APPLICATION->SetPageProperty("title", "Формы");
$APPLICATION->SetAdditionalCSS("/bitrix/components/bitrix/crm.entity.editor/templates/.default/style.css");
$APPLICATION->SetAdditionalCSS("/bitrix/crm.order.shipment.product.barcodes/templates/.default/style.css");
$APPLICATION->AddChainItem('Список форм', '/timesheets/');
$APPLICATION->AddChainItem('Общий отчет', '');

if(empty($request['arFilterForm'])){
    $startFilterDate = date('1.m.Y');
    $endFilterDate = date(cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')).'.m.Y');
} else {
    $startFilterDate = htmlspecialcharsbx(strip_tags($request['arFilterForm']['START_DATE']));
    $endFilterDate = htmlspecialcharsbx(strip_tags($request['arFilterForm']['END_DATE']));
}

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable as HL;
Loader::includeModule("highloadblock");

    $idHlProject = 81;
    function GetEntityDataClass($id){
        $hlblock = HL::getById($id)->fetch();
        $entity = HL::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;
    }

   $projects = GetEntityDataClass($idHlProject)::getList(array(
           'select' => array('ID', 'UF_NAME')
   ))->fetchAll();


?>

<script type="text/javascript" src="/timesheets/style/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="/timesheets/style/js/meta_input.js?t=<?= time() ?>"></script>
<script type="text/javascript" src="/timesheets/style/js/scripts.js"></script>

<link rel="stylesheet" type="text/css" href="/timesheets/style/css/style.css?t=<?= time() ?>">
<link rel="stylesheet" type="text/css" href="/timesheets/meta_input.css?t=<?= time() ?>">
<link rel="stylesheet" type="text/css" href="/timesheets/style/css/style-table.css?t=<?= time() ?>">
<link rel="stylesheet" type="text/css" href="/timesheets/reports2/style/style.css?t=<?= time() ?>">
<script type="text/javascript" src="/timesheets/style/js/max-limit-12.js"></script>

<script src="script\core.js"></script>
<script src="script\charts.js"></script>
<script src="script\animated.js"></script>

<div class="main-header">
    <form method="get" id="arFilterForm">
        <input type="hidden" name="formid" value="<?= $request['formid'] ?>">
    </form>
    <div class="header_fixed">

    <?
    $APPLICATION->IncludeComponent(
        "bitrix:breadcrumb",
        "timesheets",
        Array(
            "PATH" => "",
            "SITE_ID" => "s1",
            "START_FROM" => "0"
        )
    );
    ?>
    <div class="timesheet-header header-line">
        <div>
            <?php $APPLICATION->IncludeComponent("bitrix:main.calendar", "", array(
                    "SHOW_INPUT" => "Y",
                    "FORM_NAME" => "arFilterForm",
                    "INPUT_NAME" => "arFilterForm[START_DATE]",
                    "INPUT_VALUE" => $startFilterDate,
                    "SHOW_TIME" => "N",
                    "HIDE_TIMEBAR" => "Y",
                    "INPUT_ADDITIONAL_ATTR" => 'form="arFilterForm" placeholder="С ДАТЫ" required readonly id="arFilterForm_START_DATE" class="calendar_inp crm-entity-widget-content-input cl-arFilterForm clid-arFilterForm_START_DATE" style="width:100px; float:left;" onclick="BX.calendar({node:this, field: \'arFilterForm[START_DATE]\', form: \'arFilterForm\', bTime: false});"'
                )
            ); ?>
        </div>
        <div>
            <?php $APPLICATION->IncludeComponent("bitrix:main.calendar", "", array(
                    "SHOW_INPUT" => "Y",
                    "FORM_NAME" => "arFilterForm",
                    "INPUT_NAME" => "arFilterForm[END_DATE]",
                    "INPUT_VALUE" => $endFilterDate,
                    "SHOW_TIME" => "N",
                    "HIDE_TIMEBAR" => "Y",
                    "INPUT_ADDITIONAL_ATTR" => 'form="arFilterForm" placeholder="ПО ДАТУ" required readonly id="arFilterForm_END_DATE" class="calendar_inp crm-entity-widget-content-input cl-arFilterForm clid-arFilterForm_END_DATE" style="width:100px; float:left;" onclick="BX.calendar({node:this, field: \'arFilterForm[END_DATE]\', form: \'arFilterForm\', bTime: false});"'
                )
            ); ?>
        </div>
        <div class="timesheets-header-button">
            <div>
                <button class="custom-button period" form="arFilterForm">Установить период</button>
            </div>
            <div class="caption-list">Выбрать фильтр:</div>
            <div>
                <ul class="filter_list-project">
                    <li class="first-elem_project list-project" data-project="all">Все активности</li>
                    <li class="list-project" data-project="office">Проектный офис</li>
                    <li class="list-project" data-project="incident">Инциденты</li>
                    <li class="list-project" data-project="other">Иное</li>
                </ul>
            </div>
            <div class="caption-list">Выбрать период:</div>
            <div>
                <ul class="filter_list-period">
                    <li class="first-elem_period list-period" data-period="cur">Текущий</li>
                    <li class="list-period" data-period="month">Прошлый месяц</li>
                    <li class="list-period" data-period="week">Прошлая неделя</li>
                </ul>
            </div>
            <div class="select">
                <input class="select__input" type="hidden" name="">
                <div class="select__head">Выбрать проект</div>
                <ul class="select__list" style="display: none;">
                    <?foreach ($projects as $arItem):?>
                        <?
                        if(mb_strlen($arItem['UF_NAME']) > 30){
                            $arItem['UF_NAME'] = mb_substr($arItem['UF_NAME'],0,29, 'UTF-8').'...';
                        }
                        ?>
                        <li class="select__item" data-idproject="<?=$arItem['ID']?>"><?=$arItem['UF_NAME']?></li>
                    <?endforeach;?>
                </ul>
            </div>
        </div>
    </div>
    </div>
    <div class="block-reports">
        <h1 style="text-align: center">Отчет времени по направлениям проектам и задачам</h1>
        <div class="report">
            <h2>Количество часов и доля по наименованию проекта</h2>
            <div class="table-block">
                <div id="chartdiv12" style="width: 100%; height: 100%; margin: 0 auto"></div>
                <div id="smallRound"></div>
                <table class="table_values">
                    <thead>
                    <tr>
                        <th>ФИО</th>
                        <th>Часы</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <button class="custom-button legend-btn">Закрыть таблицу</button>
            </div>
            <button class="custom-button legend-btn_close">Открыть таблицу</button>
            <div id="chartdiv1" style="width: 100%; height: 600px; margin: 0 auto"></div>
        </div>
        <div class="report">
            <h2>Занятость сотрудников по проектам в часах</h2>
            <div id="chartdiv11" style="width: 100%; height: 100%"></div>
        </div>
        <div class="report">
            <h2>Количество часов и план часов по месяцам</h2>
            <div id="chartdiv2" style="width: 100%; height: 100%"></div>
        </div>
        <div class="report">
            <h2>Количество часов по периоду и наименованию проекта</h2>
            <div id="chartdiv3" style="width: 100%; height: 100%"></div>
        </div>
    </div>
</div>

<script src="script/script.js"></script>
