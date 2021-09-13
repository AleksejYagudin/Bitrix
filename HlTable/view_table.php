<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');?>
<?
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
Loader::includeModule('highloadblock');

$hlId = $_GET['id'];
if(empty($hlId))
{
    echo 'Не указан ID таблицы';
}
else
{
    $hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $rsData = $entity_data_class::getList(array(
        'select' => array('UF_TABLE_TITLE')
    ));
    if($arData = $rsData->Fetch()){
       $tableName = $arData['UF_TABLE_TITLE'];
    }
}
?>
<script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>
<script src="https://bossanova.uk/jspreadsheet/v4/jexcel.js"></script>
<script src="https://jsuites.net/v4/jsuites.js"></script>
<script src="script.js"></script>
<link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />
<link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v4/jexcel.css" type="text/css" />

<div class="view-table">
    <h2 id="table-title"><?=$tableName?></h2>
</div>
<div id="spreadsheet"></div>
<div>
    <button id="save-table">Сохранить</button>
</div>

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');?>