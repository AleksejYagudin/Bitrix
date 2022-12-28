<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arSortFields = array(
	"ID"=>GetMessage("T_IBLOCK_DESC_FID"),
	"TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
);
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"IBLOCK"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("YAGO_ALLRATING_IBLOCK_ID"),
			"TYPE" => "STRING",
			"VALUES" => "",
			"DEFAULT" => '',
		),
		"ELEMENT_ID"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("YAGO_ALLRATING_ELEMENT_ID"),
			"TYPE" => "STRING",
			"VALUES" => "",
			"DEFAULT" => '',
		),
		"SORT_BY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("YAGO_ALLRATING_SORT"),
			"TYPE" => "LIST",
			"DEFAULT" => $arSortFields["ID"],
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "N",
		),
		"COMMENTS_COUNT"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("YAGO_ALLRATING_COMMENTS_COUNT"),
			"TYPE" => "STRING",
			"VALUES" => "",
			"DEFAULT" => '2',
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000)
	)
);
?>
