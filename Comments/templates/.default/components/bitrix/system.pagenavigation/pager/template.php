<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->createFrame()->begin("Загрузка навигации");
?>
<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>
<?if($arResult["NavPageCount"] > 1):?>

    <?if ($arResult["NavPageNomer"]+1 <= $arResult["nEndPage"]):?>
        <?
        $plus = $arResult["NavPageNomer"]+1;
        $url = $arResult["sUrlPathParams"] . "PAGEN_".$arResult["NavNum"]."=".$plus;

        ?>

        <div class="load_more" data-url="<?=$url?>">
            <?=Loc::getMessage('YAGO_SHOW_ALL')?>
        </div>

    <?else:?>

        <div class="load_more">
            <?=Loc::getMessage('YAGO_ALL_PAGES')?>
        </div>

    <?endif?>

<?endif?>