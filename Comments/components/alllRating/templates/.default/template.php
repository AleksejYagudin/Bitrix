<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>
<?if(!empty($arResult["COMMENTS"])):?>
<div id="allRating">
  <div class="rating-title"><?=Loc::getMessage("YAGO_ALLRATING_TITLE")?></div>
    <?foreach ($arResult["COMMENTS"] as $key => $arItem):?>
    <div class="comment-item">
        <div class="comment-title"><?=Loc::getMessage("YAGO_ALLRATING_COMMENT_ID")?><?=$arItem["ID"]?></div>
        <div class="comment-block">
            <div><div class="comment-date"><span><?=Loc::getMessage("YAGO_ALLRATING_COMMENT_DATE")?></span> <?=$arItem["DATE_CREATE"]?></div>
                 <div class="comment-author"><span><?=Loc::getMessage("YAGO_ALLRATING_COMMENT_AUTHOR")?></span> <?=$arItem["AUTHOR"]?></div>
                 <div class="comment-rating"><span><?=Loc::getMessage("YAGO_ALLRATING_COMMENT_RATING_VALUE")?></span> <?=$arItem["PROPERTY_RATING"]?></div>
            </div>
            <div class="comment-text"><span><?=Loc::getMessage("YAGO_ALLRATING_COMMENT_TEXT")?></span> <?=$arItem["PREVIEW_TEXT"]?></div>
        </div>
            <?if(!$arItem["DETAIL_TEXT"] == ""):?>
            <div class="comment-moderator"><span><?=Loc::getMessage("YAGO_ALLRATING_ANSWER_TEXT")?></span><span><?=$arItem["DETAIL_TEXT"]?></span></div><?endif;?>
    <?endforeach;?>
    </div>
</div>
<?echo  $arResult['NAV_STRING'];?>
<?endif;?>
