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


$protocol = "http".(isset($_SERVER['HTTPS']) ? "s" : "");
$url = $protocol.'://'. $_SERVER["SERVER_NAME"] .''.$APPLICATION->GetCurPage(false);
$path= parse_url($url, PHP_URL_PATH);
$curPage = array_pop(explode("/", trim($path, "/")));

if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;

if($curPage === trim($arParams["ELEMENT_ID"]))
{
    if($this->startResultCache())
    {
        if (!CModule::IncludeModule("iblock"))
        {
            $this->abortResultCache();
            ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
            return;
        }
        $arResult = array();
        $arFilter = array (
            "IBLOCK_ID"=> intval(trim($arParams["IBLOCK"])),
            "ACTIVE" => "Y",
            "CHECK_PERMISSIONS" => "Y",
            'PROPERTY_ID_COMMENT' =>$curPage
        );

        $rsComment = CIBlockElement::GetList(array(), $arFilter, array("PROPERTY_RATING"), array(), array());
        while($arComment = $rsComment->GetNext())
        {
            $arResult['CNT'] += $arComment['CNT'];
            $arResult['SUMM'] += $arComment['CNT']*$arComment['PROPERTY_RATING_VALUE'];
        }
        function wilsonScore($sumVotes, $totalVotes, $votesRange = [1, 2, 3, 4, 5])
        {
            if ($sumVotes > 0 && $totalVotes > 0) {
                $z = 1.64485;
                $vMin = min($votesRange);
                $vWidth = floatval(max($votesRange) - $vMin);
                $phat = ($sumVotes - $totalVotes * $vMin) / $vWidth / floatval($totalVotes);
                $rating = ($phat + $z * $z / (2 * $totalVotes) - $z * sqrt(($phat * (1 - $phat) + $z * $z / (4 * $totalVotes)) / $totalVotes)) / (1 + $z * $z / $totalVotes);
                return round($rating * $vWidth + $vMin, 1);
            }
            return 0;
        };

        $arResult['OVERALL_RATING'] = wilsonScore($arResult['SUMM'],$arResult['CNT']);
        $this->SetResultCacheKeys(array(
            "CNT",
            "SUMM",
            "OVERALL_RATING",
        ));
        $this->includeComponentTemplate();
    }
}


