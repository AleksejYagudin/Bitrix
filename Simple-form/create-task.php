<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $USER;
if(!check_bitrix_sessid()){
    die("ACCESS_DENIED");
}
if ($USER->IsAuthorized())
{
 if(createTask($USER))
    {
        echo json_encode('ok');
    }
    else
    {
        echo json_encode('no');
    }

}
else
{
    LocalRedirect("/");
}
function createTask($USER)
{

    $request = \Bitrix\Main\Context::getCurrent()->getRequest();
    if($request->get('projects') !== '' && $request->get('task-name') !== '' && $request->get('task-text1') !== '' && $request->get('task-text2') !== '')
    {
        $created_by = $USER->GetID();
        $project_name = $request->get("projects");
        if($project_name == 'IMS')
        {
            $responsible_id = 3;
        }
        elseif ($project_name == 'PM')
        {
            $responsible_id = 525;
        }
        elseif ($project_name == 'BI')
        {
           $responsible_id = 200;
        }
        if (CModule::IncludeModule("tasks"))
        {
            \Bitrix\Main\Loader::includeModule('disk');

            $task = new \Bitrix\Tasks\Item\Task(0, $created_by);
            $task["TITLE"] = $request->get('task-name');
            $task["DESCRIPTION"] = '<b>Основание:</b><br>'.$request->get('task-text1').'<br><b>Описание:</b><br>'.$request->get('task-text2');
            $task["CREATED_BY"] = $created_by;
            $task["RESPONSIBLE_ID"] = $responsible_id;
            $selectDate = MakeTimeStamp($request->get('task-date'), 'YYYY-MM-DD');
            if(!empty($request->get('task-date')))
            {
                $task["DEADLINE"] = ConvertTimeStamp($selectDate, 'FULL');
            }
            $result = $task->save();
            if($result->isSuccess())
            {
                if (\Bitrix\Main\Loader::includeModule('disk'))
                {
                    $storage = Bitrix\Disk\Driver::getInstance()->getStorageByUserId($created_by);
                    $folder = $storage->getFolderForUploadedFiles();
                    $arFile = $_FILES['task-file'];
                    $file = $folder->uploadFile($arFile, array(
                        'NAME' => $arFile["name"],
                        'CREATED_BY' => $created_by
                    ), array(), true);
                    $FILE_ID = $file->getId();
                    $oTaskItem = new CTaskItem($task->getId(), $created_by);
                    $oTaskItem->Update(array("UF_TASK_WEBDAV_FILES" => Array("n$FILE_ID")));
                }

                return true;
            }
            else
            {
                return false;
            }
        }
    }
    else
    {
        return false;
    }

}

