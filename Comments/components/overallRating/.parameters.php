<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"IBLOCK"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("YAGO_OVERALL_IBLOCK_ID"),
			"TYPE" => "STRING",
			"VALUES" => "",
			"DEFAULT" => '',
		),
		"ELEMENT_ID"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("YAGO_OVERALL_ELEMENT_ID"),
			"TYPE" => "STRING",
			"VALUES" => "",
			"DEFAULT" => '',
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000)
	)
);
?>
