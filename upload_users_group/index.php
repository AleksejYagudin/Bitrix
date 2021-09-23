<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
$APPLICATION->SetTitle("Выгрузка участников групп");
require_once('getUsers.php');
\Bitrix\Main\UI\Extension::load("ui.forms");
?>
<div class="ui-ctl ui-ctl-multiple-select" style="height:auto; width: 400px">
    <select class="ui-ctl-element" size="20" id="group-id">
        <?foreach ($arResultGroupName as $key => $groupName):?>
        <option value="<?=$groupName?>" data-id = "<?=$key?>"><?=$groupName?></option>
        <?endforeach;?>
    </select>
    <button class="ui-btn ui-btn-success ui-btn-sm ui-btn-icon-download" style="margin-top: 20px"id="download-xls" disabled>Скачать</button>
</div>
<script>
    let downloadXls = document.querySelector('#download-xls'),
        groupId = document.querySelector('#group-id');

    groupId.addEventListener('change', () => {
        console.log('212')
        downloadXls.removeAttribute('disabled')
    })
    downloadXls.addEventListener('click', () => {
        let id = groupId.options[groupId.selectedIndex].getAttribute('data-id');
       sendData(id)
    })
    function sendData(id)
    {
        $.ajax({
            url: '/dev/upload_users_group/ajax.php',
            type: 'POST',
            dataType: 'JSON',
            data: {idGroup: id},
            success: function(data){
                var link = document.createElement('a');
                link.setAttribute('href',`/upload/dev/upload_users_group/${data}.xlsx`);
                link.setAttribute('download1','download');
                onload=link.click();
            },
            error: function (data)
            {
                console.error(data)
            }
        });
    }
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
