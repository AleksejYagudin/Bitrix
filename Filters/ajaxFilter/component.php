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

use Bitrix\Main\Loader,
	Bitrix\Main,
	Bitrix\Iblock;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 300;


if($this->startResultCache(false, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())))
{

	$this->includeComponentTemplate();
}
