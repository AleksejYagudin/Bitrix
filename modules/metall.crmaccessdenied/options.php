<?
use Bitrix\Main\Loader;
use Access\Fields\GetFields;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$fields = new GetFields();
$request = $fields->getRequest();
$result = $fields->getResult();


if($request['save'] || $request['apply']) {
    if(isset($request['fields'])) {
        foreach ($request['fields'] as $entityName => $entity) {
            $entity = $entityName;
            foreach ($request['fields'][$entity] as $fieldName => $item) {
                if ($item['ID'] === "" && (isset($item['is_view']) || isset($item['is_edit'])) && (isset($item['UF_DEPARTMENT_VIEW']) || isset($item['UF_DEPARTMENT_EDIT']))) {
                    $fields->creatField($fieldName, $item, $entity);
                } elseif ((int)$item['ID'] > 0 && (isset($item['UF_DEPARTMENT_VIEW']) || isset($item['UF_DEPARTMENT_EDIT'])) || isset($item['update'])) {
                    $fields->updateField($item);
                }
            }
        }


    }

    if ($request['apply']) {
        LocalRedirect("/bitrix/admin/settings.php?" . LANG . "=ru&mid=metall.crmaccessdenied");
    }
    elseif($request['save']){
        LocalRedirect("/bitrix/admin/settings.php?lang=" . LANG);
    }

}
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<?
$aTabs = array(
    array("DIV" => "contact", "TAB" => 'Контакты', "TITLE"=>'Контакты'),
    array("DIV" => "company", "TAB" => 'Компания', "TITLE"=>'Компания'),

);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<form method="POST" ENCTYPE="multipart/form-data" name="post_form">
<?php
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
        <thead>
        <tr>
            <th style="text-align: left">Название поля</th>
            <th style="text-align: left">Код поля</th>
            <th style="text-align: center">Запрет видимости</th>
            <th style="text-align: center">Запрет редактирования</th>
        </tr>
        </thead>


<?if(isset($result['contact_details'])):?>
    <?$count = 1;?>
    <?foreach ($result['contact_details'] as $key => $item):?>
    <tr class="field-line">
        <input type="hidden"  name="fields[contact_details][<?=$key?>][ID]" value="<?=$fields->getResult()['contact_details'][$key]['ID']?>">
        <input type="hidden"  name="fields[contact_details][<?=$key?>][TITLE]" value="<?=$key?>">

        <td style="width: 10%; text-align: left; border-bottom: 1px solid black"><?=$item['TITLE_RU'] ?? $item['TITLE'];?></td>
        <td style="width: 10%; text-align: left; border-bottom: 1px solid black"><?=$key;?></td>

        <?if($item['IS_VIEW']):?>
            <td style="width: 25%; text-align: center; border-bottom: 1px solid black"><input class="change-view" type="checkbox" name="fields[contact_details][<?=$key?>][is_view]" checked>
            <select class = "select-users_field" name="fields[contact_details][<?=$key?>][UF_DEPARTMENT_VIEW][]" size="5" multiple="multiple">
                <?foreach ($result['DEPARTAMENTS'] as $keyDept => $arDepartment):?>
                    <option value="<?= $keyDept?>" <?if(in_array($keyDept, $item['USERS']['UF_DEPARTMENT_VIEW'])):?>selected<?endif;?>><?= $arDepartment?></option>
                <?endforeach;?>
            </select>
            </td>
        <?else:?>
            <td style="width: 25%; text-align: center; border-bottom: 1px solid black"><input class="change-view" type="checkbox" name="fields[contact_details][<?=$key?>][is_view]">
            <select class = "select-users_field" name="fields[contact_details][<?=$key?>][UF_DEPARTMENT_VIEW][]" size="5" multiple="multiple">
                <?foreach ($result['DEPARTAMENTS'] as $keyDept => $arDepartment):?>
                    <option value="<?= $keyDept?>" <?if(in_array($keyDept, $item['USERS'])):?>selected<?endif;?>><?= $arDepartment?></option>
                <?endforeach;?>
            </select>
            </td>
        <?endif;?>


        <?if($item['IS_EDIT']):?>
            <td style="width: 25%; text-align: center; border-bottom: 1px solid black"><input class="change-edit" type="checkbox" name="fields[contact_details][<?=$key?>][is_edit]" checked>
            <select class = "select-users_field" name="fields[contact_details][<?=$key?>][UF_DEPARTMENT_EDIT][]" size="5" multiple="multiple">
                <?foreach ($result['DEPARTAMENTS'] as $keyDept => $arDepartment):?>
                    <option value="<?= $keyDept?>" <?if(in_array($keyDept, $item['USERS']['UF_DEPARTMENT_EDIT'])):?>selected<?endif;?>><?= $arDepartment?></option>
                <?endforeach;?>
            </select>
            </td>
        <?else:?>
            <td style="width: 25%; text-align: center; border-bottom: 1px solid black"><input class="change-edit" type="checkbox" name="fields[contact_details][<?=$key?>][is_edit]">
            <select class = "select-users_field" name="fields[contact_details][<?=$key?>][UF_DEPARTMENT_EDIT][]" size="5" multiple="multiple">
                <?foreach ($result['DEPARTAMENTS'] as $keyDept => $arDepartment):?>
                    <option value="<?= $keyDept?>" <?if(in_array($keyDept, $item['USERS'])):?>selected<?endif;?>><?= $arDepartment?></option>
                <?endforeach;?>
            </select>
            </td>
        <?endif;?>
    </tr>
    <?$count++;?>
    <?endforeach;?>
<?endif;?>

<?

$tabControl->EndTab();
$tabControl->BeginNextTab();
?>
    <?if(!empty($result['company_details'])):?>
        <?if(isset($result['company_details'])):?>
            <?$count = 1;?>
            <?foreach ($result['company_details'] as $key => $item):?>
                <tr class="field-line">
                    <input type="hidden"  name="fields[company_details][<?=$key?>][ID]" value="<?=$fields->getResult()['company_details'][$key]['ID']?>">
                    <input type="hidden"  name="fields[company_details][<?=$key?>][TITLE]" value="<?=$key?>">

                    <td style="width: 10%; text-align: left; border-bottom: 1px solid black"><?=$item['TITLE_RU'] ?? $item['TITLE'];?></td>
                    <td style="width: 10%; text-align: left; border-bottom: 1px solid black"><?=$key;?></td>

                    <?if($item['IS_VIEW']):?>
                        <td style="width: 25%; text-align: center; border-bottom: 1px solid black"><input class="change-view" type="checkbox" name="fields[company_details][<?=$key?>][is_view]" checked>
                            <select class = "select-users_field" name="fields[company_details][<?=$key?>][UF_DEPARTMENT_VIEW][]" size="5" multiple="multiple">
                                <?foreach ($result['DEPARTAMENTS'] as $keyDept => $arDepartment):?>
                                    <option value="<?= $keyDept?>" <?if(in_array($keyDept, $item['USERS']['UF_DEPARTMENT_VIEW'])):?>selected<?endif;?>><?= $arDepartment?></option>
                                <?endforeach;?>
                            </select>
                        </td>
                    <?else:?>
                        <td style="width: 25%; text-align: center; border-bottom: 1px solid black"><input class="change-view" type="checkbox" name="fields[company_details][<?=$key?>][is_view]">
                            <select class = "select-users_field" name="fields[company_details][<?=$key?>][UF_DEPARTMENT_VIEW][]" size="5" multiple="multiple">
                                <?foreach ($result['DEPARTAMENTS'] as $keyDept => $arDepartment):?>
                                    <option value="<?= $keyDept?>" <?if(in_array($keyDept, $item['USERS'])):?>selected<?endif;?>><?= $arDepartment?></option>
                                <?endforeach;?>
                            </select>
                        </td>
                    <?endif;?>


                    <?if($item['IS_EDIT']):?>
                        <td style="width: 25%; text-align: center; border-bottom: 1px solid black"><input class="change-edit" type="checkbox" name="fields[company_details][<?=$key?>][is_edit]" checked>
                            <select class = "select-users_field" name="fields[company_details][<?=$key?>][UF_DEPARTMENT_EDIT][]" size="5" multiple="multiple">
                                <?foreach ($result['DEPARTAMENTS'] as $keyDept => $arDepartment):?>
                                    <option value="<?= $keyDept?>" <?if(in_array($keyDept, $item['USERS']['UF_DEPARTMENT_EDIT'])):?>selected<?endif;?>><?= $arDepartment?></option>
                                <?endforeach;?>
                            </select>
                        </td>
                    <?else:?>
                        <td style="width: 25%; text-align: center; border-bottom: 1px solid black"><input class="change-edit" type="checkbox" name="fields[company_details][<?=$key?>][is_edit]">
                            <select class = "select-users_field" name="fields[company_details][<?=$key?>][UF_DEPARTMENT_EDIT][]" size="5" multiple="multiple">
                                <?foreach ($result['DEPARTAMENTS'] as $keyDept => $arDepartment):?>
                                    <option value="<?= $keyDept?>" <?if(in_array($keyDept, $item['USERS'])):?>selected<?endif;?>><?= $arDepartment?></option>
                                <?endforeach;?>
                            </select>
                        </td>
                    <?endif;?>
                </tr>
                <?$count++;?>
            <?endforeach;?>
        <?endif;?>
    <?endif;?>
    <tr>


    </tr>

<?
$tabControl->Buttons(true);
?>
    <?$tabControl->Buttons();?>
    <?=bitrix_sessid_post();?>
<?php
$tabControl->End();
?>
</form>
<script>

        let inputs = document.querySelectorAll('INPUT'),
            select = document.querySelectorAll('.select-users_field');

        inputs.forEach(function (item) {
            item.addEventListener('change', function (e) {
                if (e.target.classList.contains('change-edit') || e.target.classList.contains('change-view'))
                {
                    let parentNode = item.parentNode,
                        nameField = item.name;
                    console.log(item);
                    console.log(parentNode);
                    console.log(nameField);
                    if(nameField.includes('[is_view]')) {
                        nameField = nameField.replace('[is_view]', '[update]');

                    }
                    if(nameField.includes('[is_edit]')) {
                        nameField = nameField.replace('[is_edit]', '[update]');
                    }

                    let hiddenInput = parentNode.querySelector('input[type="hidden"]');

                    if (!hiddenInput) {

                        let input = document.createElement("input");
                        input.type = "hidden";
                        input.name = nameField;
                        input.value = 'Update';
                        parentNode.appendChild(input);
                    }
                }

            })
        });


</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>