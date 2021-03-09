<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("YAGO_OVERALL_RATING"),
	"DESCRIPTION" => GetMessage("YAGO_OVERALL_RATING_DESC"),
	"ICON" => "/images/news_line.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => GetMessage("YAGO_COMPONENT_OTHER"),
	),
);
?>