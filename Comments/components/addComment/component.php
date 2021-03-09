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


CJSCore::Init(array("fx"));
$protocol = "http".(isset($_SERVER['HTTPS']) ? "s" : "");
$url = $protocol.'://'. $_SERVER["SERVER_NAME"] .''.$APPLICATION->GetCurPage(false);
$path= parse_url($url, PHP_URL_PATH);
$curPage = array_pop(explode("/", trim($path, "/")));
if($curPage === $arParams["ELEMENT_ID"])
{
  $this->includeComponentTemplate();
}


