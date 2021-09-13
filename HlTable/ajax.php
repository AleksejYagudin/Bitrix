<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?

use Bitrix\Main\Loader;
Loader::IncludeModule('highloadblock');
use Bitrix\Highloadblock as HL;

if($_POST['mode'] == 'createTable')
{
    $tableName = mb_strtolower(trim($_POST['tableName']));
    $tableAltName = mb_strtolower(trim($_POST['tableAltName']));


    $tableName = str_replace(' ', '', $tableName);
    $result = HL\HighloadBlockTable::add(array(
        'NAME' => ucfirst($tableName),
        'TABLE_NAME' => $tableName,
    ));

    if ($result->isSuccess()) {
        $id = $result->getId();
        HL\HighloadBlockLangTable::add(array(
            'ID' => $id,
            'LID' => 'ru',
            'NAME' => mb_convert_case($tableAltName, MB_CASE_TITLE, "UTF-8")
        ));

        createTable($id);
        echo json_encode('Таблица создана');
    } else {
        $errors = $result->getErrorMessages();
        echo json_encode($tableName);
    }
}
if($_POST['mode'] == 'updateTableList')
{
    $res = HL\HighloadBlockTable::getList(array(
    ));

    while ($row = $res->fetch())
    {
        $hlId[] = $row['ID'];
    }

    foreach ($hlId as $arItem)
    {

        $res = HL\HighloadBlockTable::getList(array(
            'select' => array('ID', 'NAME_LANG' => 'LANG.NAME'),
            'filter' => array('ID' => $arItem)
        ));

        if($ar_res = $res->fetch())
        {
             $hlInfo[$ar_res['ID']] = $ar_res['NAME_LANG'];
        }
    }

    if(!empty($hlInfo))
    {
        echo json_encode($hlInfo);
    }
    else
    {
        echo json_encode('null');
    }

}
if($_POST['mode'] == 'viewTable')
{
    $hlId = $_POST['hlId'];
    $hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $arResult = array();

    $rsData = $entity_data_class::getList(array(
        'select' => array('ID', 'UF_TABLE_TITLE_COLUMN')
    ));

    while($arData = $rsData->Fetch()){

        $arResult['COLUMN_TITLE'][] = $arData;
    }

    //Получаем название строк
    $rsData = $entity_data_class::getList(array(
        'select' => array('ID', 'UF_TABLE_COLUMN_*'),
        'order' => array('UF_SORT' => 'ASC')
    ));


    while($arData = $rsData->Fetch()){

        $arResult['STR'][] = $arData;
        $arResult['ID'][] = $arData['ID'];

    }

    $temp = array();
    foreach ($arResult['STR'] as $key1 => $arItem1)
    {
        foreach ($arItem1 as $key2 => $arItem2)
        {
            if($key2 == 'ID')
            {
                continue;
            }

            $rsData = CUserTypeEntity::GetList(array('SORT' => 'ASC'), array('ENTITY_ID' => 'HLBLOCK_'.$hlId,  'FIELD_NAME' => $key2, 'LANG' => 'ru') );
            if($arRes = $rsData->Fetch())
            {
                $temp[] = $arRes['LIST_COLUMN_LABEL'];

            }
        }

    }
    $arResult['COLUMN_NAME'] = array_unique($temp);

    foreach ($arResult['STR'] as $key1 => $arItem1)
    {

        foreach ($arItem1 as $key2 => $arItem2)
        {
            if($key2 == 'ID')
            {
                continue;
            }
            $arResult['COLUMN_TEXT'][$key2][] = $arItem2;

        }
    }

    foreach ($arResult['COLUMN_TEXT'] as $key => $arItem)
    {
        if($key == 'UF_SORT')
        {
            continue;
        }
        $arResult['COLUMN_TEXT_NAME'][] = $key;
    }


    $count = 0;
    foreach ($arResult['COLUMN_TEXT'] as $key1 => $arItem1)
    {
        $arResult['FOR_CLIENT'][$count][]= $arResult['COLUMN_NAME'][$count];
        foreach ($arItem1 as $key2 => $arItem2)
        {
            $arResult['FOR_CLIENT'][$count][] = $arItem2;
        }
        $count++;
    }




    echo json_encode($arResult);
}

if($_POST['mode'] == 'insertedColumn')
{
    $hlId = $_POST['hlId'];
    $hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

        $fields = array(
            "fields" => array(
            ));
        $entity_data_class::add($fields);
        echo json_encode($_POST);

}
if($_POST['mode'] == 'deletedColumn')
{
    $hlId = $_POST['hlId'];
    $serverSTR = $_POST['serverSTR'];
    $hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $strIdClientTemp = array();
    foreach ($serverSTR as $key => $arItem)
    {
        if($arItem['id'] == '')
        {
            continue;
        }
        $strIdClientTemp[] = $arItem['id'];
    }
    $strIdClient = array_unique($strIdClientTemp);
    //Получаем строки с сервера
    $rsData = $entity_data_class::getList(array(

    ));
    while($arData = $rsData->Fetch()){

        $arrTemp1[] = $arData['ID'];
    }
    $strDelete = array_diff($arrTemp1, $strIdClient);
    foreach ($strDelete as $arItem)
    {
        if($entity_data_class::getRowById($arItem) !== null)
        {
            $entity_data_class::delete($arItem);
        }

    }
    echo json_encode($_POST);

}
if($_POST['mode'] == 'insertedRow')
{
    $hlId = $_POST['hlId'];
    $columnNumber = $_POST['columnNumber'];



    $UFObject = 'HLBLOCK_'.$hlId;
    $arFields = array(
        $arItem=>Array(
            'ENTITY_ID' => $UFObject,
            'FIELD_NAME' => 'UF_TABLE_COLUMN_'.$columnNumber,
            'USER_TYPE_ID' => 'string',
            'MANDATORY' => 'N',
            "EDIT_FORM_LABEL" => Array('ru'=>'Колонка таблицы №'.$columnNumber, 'en'=>''),
            "LIST_COLUMN_LABEL" => Array('ru'=>'Колонка таблицы №'.$columnNumber, 'en'=>''),
            "LIST_FILTER_LABEL" => Array('ru'=>'Колонка таблицы №'.$columnNumber, 'en'=>''),
            "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
            "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
        ),
    );
    foreach($arFields as $arItem1){
        $obUserField  = new CUserTypeEntity;
        $obUserField->Add($arItem1);
    }
    echo json_encode($_POST);

}
if($_POST['mode'] == 'deletedRow')
{
    $hlId = $_POST['hlId'];
    $deleteRowName = $_POST['deleteRowName'];
    $hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $columnNameClient = array();
    foreach ($deleteRowName as $arItem)
    {
        $columnNameClient[] = $arItem['column'];

    }

    //Получаем колонки с сервера
    $columnNameServerTemp = array();
    $rsData = $entity_data_class::getList(array(
        'select' => array('UF_TABLE_COLUMN_*')
    ));
    while($arData = $rsData->Fetch()){

        $columnNameServerTemp = $arData;
    }
    foreach ($columnNameServerTemp as $key1 => $arItem1)
    {

        $columnNameServer[] = $key1;
    }

    $columnDell = array_diff($columnNameServer, $columnNameClient);
    foreach ($columnDell as $arItem)
    {
        $rsData = CUserTypeEntity::GetList(array('SORT' => 'ASC'), array('ENTITY_ID' => 'HLBLOCK_'.$hlId,  'FIELD_NAME' => $arItem, 'LANG' => 'ru') );
        if($arRes = $rsData->Fetch())
        {
            $temp[] = $arRes['ID'];

        }
        foreach($temp as $arItem1){
            $obUserField  = new CUserTypeEntity;
            $obUserField->Delete($arItem1);
        }
    }

    echo json_encode($columnDell);

}
if($_POST['mode'] == 'saveTable')
{
    $hlId = $_POST['hlId'];
    $arrToServerSTR = $_POST['arrToServerSTR'];
    $arrToServerColumns = $_POST['arrToServerColumns'];
    $hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    foreach ($arrToServerSTR as $arItem)
    {
        if($arItem['id'] == null || $arItem['id'] == 'undefined')
        {
            continue;
        }
        $data = array(
            $arItem['column'] => $arItem['text'],
            'UF_SORT' => $arItem['sort']
        );
        $entity_data_class::update($arItem['id'], $data);
    }
    foreach ($arrToServerColumns as $arItem)
    {
        $res = CUserTypeEntity::GetList(array('SORT' => 'ASC'), array('ENTITY_ID' => 'HLBLOCK_'.$hlId, 'FIELD_NAME' => $arItem['id'], 'LANG' => 'ru') );
        if($ar_res = $res->Fetch())
        {
            if($arItem['text'] == '')
            {
                $arItem['text'] = '--';
            }
        }
        $arFields = array(
            'SORT' => $arItem['sort'],
            "EDIT_FORM_LABEL" => Array('ru'=> $arItem['text'], 'en'=>''),
            "LIST_COLUMN_LABEL" => Array('ru'=> $arItem['text'], 'en'=>''),
            "LIST_FILTER_LABEL" => Array('ru'=> $arItem['text'], 'en'=>''),
            "HELP_MESSAGE" => Array('ru'=> $arItem['text'], 'en'=>''),
        );

        $obUserField  = new CUserTypeEntity;
        $obUserField->Update($ar_res['ID'], $arFields);

    }
    echo json_encode($_POST);

}



function createTable($id)
{
    $UFObject = 'HLBLOCK_'.$id;
    $arFields = array(
        'UF_TABLE_TITLE'=>Array(
            'ENTITY_ID' => $UFObject,
            'FIELD_NAME' => 'UF_TABLE_TITLE',
            'USER_TYPE_ID' => 'string',
            'MANDATORY' => 'N',
            'SHOW_IN_LIST' => 'Y',
            "EDIT_FORM_LABEL" => Array('ru'=>'Заголовок таблицы', 'en'=>''),
            "LIST_COLUMN_LABEL" => Array('ru'=> 'Заголовок таблицы', 'en'=>''),
            "LIST_FILTER_LABEL" => Array('ru'=>'Заголовок таблицы', 'en'=>''),
            "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
            "HELP_MESSAGE" => Array('ru'=>'Заголовок таблицы', 'en'=>''),
        ),
        'UF_TABLE_COLUMN_0'=>Array(
            'ENTITY_ID' => $UFObject,
            'FIELD_NAME' => 'UF_TABLE_COLUMN_0',
            'USER_TYPE_ID' => 'string',
            'SORT' => 1,
            'MANDATORY' => 'N',
            "EDIT_FORM_LABEL" => Array('ru'=>'Колонка таблицы №0', 'en'=>''),
            "LIST_COLUMN_LABEL" => Array('ru'=>'Колонка таблицы №0', 'en'=>''),
            "LIST_FILTER_LABEL" => Array('ru'=>'Колонка таблицы №0', 'en'=>''),
            "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
            "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
        ),
        'UF_TABLE_COLUMN_1'=>Array(
            'ENTITY_ID' => $UFObject,
            'FIELD_NAME' => 'UF_TABLE_COLUMN_1',
            'USER_TYPE_ID' => 'string',
            'SORT' => 2,
            'MANDATORY' => 'N',
            "EDIT_FORM_LABEL" => Array('ru'=>'Колонка таблицы №1', 'en'=>''),
            "LIST_COLUMN_LABEL" => Array('ru'=>'Колонка таблицы №1', 'en'=>''),
            "LIST_FILTER_LABEL" => Array('ru'=>'Колонка таблицы №1', 'en'=>''),
            "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
            "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
        ),
        'UF_TABLE_COLUMN_2'=>Array(
            'ENTITY_ID' => $UFObject,
            'FIELD_NAME' => 'UF_TABLE_COLUMN_2',
            'USER_TYPE_ID' => 'string',
            'SORT' => 2,
            'MANDATORY' => 'N',
            "EDIT_FORM_LABEL" => Array('ru'=>'Колонка таблицы №2', 'en'=>''),
            "LIST_COLUMN_LABEL" => Array('ru'=>'Колонка таблицы №2', 'en'=>''),
            "LIST_FILTER_LABEL" => Array('ru'=>'Колонка таблицы №2', 'en'=>''),
            "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
            "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
        ),
        'UF_TABLE_TITLE_COLUMN'=>Array(
            'ENTITY_ID' => $UFObject,
            'FIELD_NAME' => 'UF_TABLE_TITLE_COLUMN',
            'USER_TYPE_ID' => 'string',
            'MANDATORY' => 'N',
            "EDIT_FORM_LABEL" => Array('ru'=>'Заголовок колонки', 'en'=>''),
            "LIST_COLUMN_LABEL" => Array('ru'=> 'Заголовок колонки', 'en'=>''),
            "LIST_FILTER_LABEL" => Array('ru'=>'Заголовок колонки', 'en'=>''),
            "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
            "HELP_MESSAGE" => Array('ru'=>'Заголовок колонки', 'en'=>''),
        ),
        'UF_SORT'=>Array(
            'ENTITY_ID' => $UFObject,
            'FIELD_NAME' => 'UF_SORT',
            'USER_TYPE_ID' => 'integer',
            'MANDATORY' => 'N',
            "EDIT_FORM_LABEL" => Array('ru'=>'Сортировка', 'en'=>''),
            "LIST_COLUMN_LABEL" => Array('ru'=> 'Сортировка', 'en'=>''),
            "LIST_FILTER_LABEL" => Array('ru'=>'Сортировка', 'en'=>''),
            "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
            "HELP_MESSAGE" => Array('ru'=>'Сортировка', 'en'=>''),
        ),

    );
    foreach($arFields as $arItem){
        $obUserField  = new CUserTypeEntity;
        $obUserField->Add($arItem);
    }

    $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById($id)->fetch();
    $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $fields = array(
        "fields" => array(
            "UF_TABLE_TITLE" => trim($_POST['tableTitle']),
            "UF_TABLE_COLUMN_0" => '',
            "UF_TABLE_COLUMN_1" => '',
            "UF_TABLE_COLUMN_2" => '',
            "UF_TABLE_TITLE_COLUMN" => '',
            "UF_SORT" => 1,
        ));
    $entity_data_class::add($fields);

    $fields = array(
        "fields" => array(
            "UF_TABLE_TITLE" => trim($_POST['tableTitle']),
            "UF_TABLE_COLUMN_0" => '',
            "UF_TABLE_COLUMN_1" => '',
            "UF_TABLE_COLUMN_2" => '',
            "UF_TABLE_TITLE_COLUMN" => '',
            "UF_SORT" => 2,
        ));
    $entity_data_class::add($fields);

    $fields = array(
        "fields" => array(
            "UF_TABLE_TITLE" => trim($_POST['tableTitle']),
            "UF_TABLE_COLUMN_0" => '',
            "UF_TABLE_COLUMN_1" => '',
            "UF_TABLE_COLUMN_2" => '',
            "UF_TABLE_TITLE_COLUMN" => '',
            "UF_SORT" => 3,
        ));
    $entity_data_class::add($fields);
}

?>
