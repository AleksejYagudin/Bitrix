<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
\Bitrix\Main\Loader::includeModule('socialnetwork');
\Bitrix\Main\Loader::includeModule('iblock');
$arResultGroupName = array();
$res = CSocNetGroup::GetList(
    array('ID' => 'ASC'),
    array('ACTIVE' => 'Y'),
    false,
    false,
    array('ID', 'NAME')
);

while ($ar_res = $res->GetNext())
{
    $arResultGroupName[$ar_res['ID']]= $ar_res['NAME'];
}



$l=1;
?>
