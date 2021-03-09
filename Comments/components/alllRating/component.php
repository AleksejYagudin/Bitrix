<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

$iblock = intval(trim($arParams["IBLOCK"]));
$elem = trim($arParams["ELEMENT_ID"]);
CJSCore::Init(array("jquery"));
if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;


$protocol = "http".(isset($_SERVER['HTTPS']) ? "s" : "");
$url = $protocol.'://'. $_SERVER["SERVER_NAME"] .''.$APPLICATION->GetCurPage(false);
$path= parse_url($url, PHP_URL_PATH);
$curPage = array_pop(explode("/", trim($path, "/")));
if($curPage === $arParams["ELEMENT_ID"]) {

    CPageOption::SetOptionString('main', 'nav_page_in_session', 'N');
    $arParams['COMMENTS_COUNT'] = intval($arParams['COMMENTS_COUNT']);
    if ($arParams['COMMENTS_COUNT'] <= 0) {
        $arParams['COMMENTS_COUNT'] = 2;
    }
    $arNavParams = null;
    $arNavigation = false;
    $arNavParams = array(
        'nPageSize' => $arParams['COMMENTS_COUNT'],
        'bShowAll' => "Y"
    );
    $arNavigation = CDBResult::GetNavParams($arNavParams);

    if ($this->startResultCache(false, $arNavigation)) {

        if (!CModule::IncludeModule("iblock")) {
            $this->abortResultCache();
            ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
            return;
        }

        $arResult = array();

        $arOrder = array(
            $arParams["SORT_BY"] => $arParams["SORT_BY"]
        );
        $arFilter = array(
            "IBLOCK_ID" => $iblock,
            "ACTIVE" => "Y",
            "PROPERTY_ID_COMMENT" => $curPage
        );
        $arSelect = array(
            "ID",
            "DATE_CREATE",
            "PREVIEW_TEXT",
            "DETAIL_TEXT",
            "CREATED_USER_NAME",
            "PROPERTY_RATING"
        );


        $resElem = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavParams, $arSelect);

        while ($elem = $resElem->GetNext()) {

            $arResult["COMMENTS"][$elem["ID"]]["ID"] = $elem["ID"];
            $arResult["COMMENTS"][$elem["ID"]]["AUTHOR"] = trim(mb_substr($elem["CREATED_USER_NAME"], mb_strpos($elem["CREATED_USER_NAME"], ' ')));
            $arResult["COMMENTS"][$elem["ID"]]["DATE_CREATE"] = substr($elem["DATE_CREATE"],0, -9);
            $arResult["COMMENTS"][$elem["ID"]]["PREVIEW_TEXT"] = $elem["PREVIEW_TEXT"];
            $arResult["COMMENTS"][$elem["ID"]]["DETAIL_TEXT"] = $elem["DETAIL_TEXT"];
            $arResult["COMMENTS"][$elem["ID"]]["PROPERTY_RATING"] = $elem["PROPERTY_RATING_VALUE"];

        }
        $arResult['NAV_STRING'] = $resElem->GetPageNavString(
            '',
            'pager',
            '',
            $this
        );

        $this->setResultCacheKeys(array(
                "COMMENTS"

        ));
        $this->includeComponentTemplate();

    }
}


