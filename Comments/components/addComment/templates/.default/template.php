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
<?global $USER;?>
<?if ($USER->IsAuthorized()):?>
<div id="comment">
    <form class="form-comment">
        <div class="comment-title"><?=Loc::getMessage("YAGO_TITLE_BLOCK")?></div>
        <p><textarea placeholder="<?=Loc::getMessage("YAGO_PLACEHOLDER_TEXT")?>"></textarea></p>
        <div class="stars-send">
            <div class="rating-area">
                <input type="radio" id="star-5" name="rating" value="5">
                <label for="star-5"></label>
                <input type="radio" id="star-4" name="rating" value="4">
                <label for="star-4"></label>
                <input type="radio" id="star-3" name="rating" value="3">
                <label for="star-3"></label>
                <input type="radio" id="star-2" name="rating" value="2">
                <label for="star-2"></label>
                <input type="radio" id="star-1" name="rating" value="1">
                <label for="star-1"></label>
            </div>
            <div class="button"><input data-id = "<?=$arParams["ELEMENT_ID"]?>" data-iblock = "<?=$arParams["IBLOCK"]?>" type="submit" value="<?=Loc::getMessage("YAGO_TEXT_BUTTON")?>"/></div>
        </div>
    </form>
</div>
<?else:?>
<div id="comment" style="height: 35px">
    <div style="color: #515151; font-size: 16px; text-align: center;"><?=Loc::getMessage("YAGO_UNREGISTER_TEXT")?></div>
</div>
<?endif;?>

