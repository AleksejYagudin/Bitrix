<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
$date_today = date("m.d.y");
$today[1] = date("H:i:s");
$id = trim($_POST['id']);
$iblock = intval(trim($_POST['iblock']));
$text = trim($_POST['text']);
$rating = trim($_POST['rating']);

if (CModule::IncludeModule("iblock")) {
    $el = new CIBlockElement;
    $PROP = array();
    $PROP["RATING"] = $rating;
    $PROP["ID_COMMENT"] = $id;

    $arLoadProductArray = array(
      "MODIFIED_BY" => $USER->GetID(),
      "IBLOCK_SECTION_ID" => false,
      "IBLOCK_ID" => $iblock,
      "PROPERTY_VALUES"=> $PROP,
      "NAME" => "Комментарий от ".$date_today.' ('.$today[1].')',
      "ACTIVE" => "N",
      "PREVIEW_TEXT" => $text
    );
    if($PRODUCT_ID = $el->Add($arLoadProductArray))
        echo json_encode('Спасибо за Ваш комментарий, он будет опубликован после проверки модератором!');
    else
        echo json_encode('Ошибка при добавлении комментария');
}
