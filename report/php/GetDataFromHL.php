<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable as HL;
use	Bitrix\Main\Entity;
use Bitrix\Main\Entity\Query;
Loader::includeModule("highloadblock");

abstract class GetDataFromHL{

    const HLID_ITEMS = 89;
    const HLID_PROJECTS = 81;

    protected function GetEntityDataClass($hlId){
        $hlblock = HL::getById($hlId)->fetch();
        $entity = HL::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;
    }

    protected function queryItems(){
        $request = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->getValues();

        if($request['modePeriod'] == ''){
            if($request['dataStart'] == 'undefined' || $request['dataStart'] == ''){
                $dataStart = date('1.m.Y');
            } else {
                $dataStart = $request['dataStart'];
            }
            if($request['dataEnd'] == 'undefined' || $request['dataEnd'] == ''){
                $dataEnd = date('t.m.Y');
            } else {
                $dataEnd = $request['dataEnd'];
            }
        }
        elseif ($request['modePeriod'] == 'cur'){
            $dataStart = date('1.m.Y');
            $dataEnd = date('t.m.Y');
        }
        elseif ($request['modePeriod'] == 'month'){
            $dataStart = date('d.m.Y', strtotime("first day of previous month"));
            $dataEnd = date('t.m.Y', strtotime("last day of previous month"));

        } elseif ($request['modePeriod'] == 'week'){
            $previous_week = strtotime("-1 week -1 day");
            $start_week  = strtotime("last monday midnight",$previous_week);
            $end_week = strtotime("next friday",$start_week);

            $dataStart = date("d.m.Y",$start_week);
            $dataEnd = date("d.m.Y",$end_week);
        }


        $tempArr =  array();

        $rsData = $this->GetEntityDataClass(self::HLID_ITEMS)::getList(array(
            'select' => array('UF_PROJECT', 'UF_COUNT_HOURS', 'UF_DATE', 'UF_TECH_AUTOR'),
            'filter' => array('LOGIC' => 'AND', array('>=UF_DATE' => $dataStart), array('<=UF_DATE' => $dataEnd)),
            'order' => array('UF_DATE'=>'asc')
        ));

        while($el = $rsData->fetch()){
            $tempArr[] = $el;
        }

        return $tempArr;
    }

    protected function getMonth($date){
        $tempMonth = ParseDateTime($date);
        $_monthsList = array(
            "01"=>"Январь","02"=>"Февраль","03"=>"Март",
            "04"=>"Апрель","05"=>"Май", "06"=>"Июнь",
            "07"=>"Июль","08"=>"Август","09"=>"Сентябрь",
            "10"=>"Октябрь","11"=>"Ноябрь","12"=>"Декабрь");
        $month = $_monthsList[date($tempMonth['MM'])];
        return $month;
    }

    protected function getYear($date){
        $tempYear = ParseDateTime($date);
        return $tempYear['YYYY'];
    }

    protected function getWorkHours($month, $year) {
        if($year == '2021'){
            $workHours = array(
                '09' => '176',
                '10' => '168',
                '11' => '159',
                '12' => '176'
            );
        }
        if($year == '2022'){
            $workHours = array(
                '01' => '136',
                '02' => '152',
                '03' => '168',
                '04' => '175',
                '05' => '135',
                '06' => '167',
                '07' => '184',
                '08' => '168',
                '09' => '176',
                '10' => '176',
                '11' => '159',
                '12' => '183'
            );
        }

        $hours = (int)$workHours[$month];
        return $hours;
    }

    protected function getProjectName($id) {
        $tempArr =  array();
        $entity_data_class = $this->GetEntityDataClass(self::HLID_PROJECTS);
        $rsData = $entity_data_class::getList(array(
            'select' => array('UF_NAME', 'UF_METAGROUP'),
            'filter' => array('=ID' => $id, '!UF_METAGROUP' => '')
        ));

        if($gropData = $rsData->fetch()){
            $gropResult['UF_NAME'] = trim($gropData['UF_NAME']);
            $gropResult['UF_METAGROUP'] = trim($gropData['UF_METAGROUP']);
        }
        return $gropResult;
    }

    protected function getGroupType($type){
        if($type == 'office'){
            $groupName = 'Проектный офис';
        } elseif ($type == 'incident'){
            $groupName = 'Инциденты';
        } elseif ($type == 'other'){
            $groupName = 'Иное';
        } elseif($type == 'all') {
            $groupName = '';
        }
        return $groupName;
    }

    protected function getUserFio($id){
        $arRes =  \Bitrix\Main\UserTable::getList(array(
            'filter' => array('=ID' => $id),
            'select' => array('NAME', 'LAST_NAME')
        ));
        if($userFIO = $arRes->fetch()){
            $user = $userFIO['NAME'].' '.$userFIO['LAST_NAME'];
        }
        return $user;
    }

    abstract function showItems();
}


class FirstReport extends GetDataFromHL{
    public function showItems()
    {
        $arResultTemp = array();
        $arResult = array();

        $request = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->getValues();
        $mode = $request['mode'];


        $groupName = $this->getGroupType($mode);

        foreach ($this->queryItems() as $key => $arItem){
            if($groupName !=''){
                if($groupName !== trim($this->getProjectName((int)$arItem['UF_PROJECT'])['UF_METAGROUP'])){
                    continue;
                }
            }



            $arResultTemp[$key]['UF_PROJECT'] = $this->getProjectName((int)$arItem['UF_PROJECT'])['UF_NAME'];
            $arResultTemp[$key]['UF_COUNT_HOURS'] = $arItem['UF_COUNT_HOURS'];
            $arResultTemp[$key]['UF_DATE'] = $this->getMonth($arItem['UF_DATE']);
        }

        $project = '';
        foreach ($arResultTemp as $key => $arItem)
        {
            if($project != $arItem['UF_PROJECT'])
            {
                $project = $arItem['UF_PROJECT'];
            }
            $arResult[$project] +=  (int)$arItem['UF_COUNT_HOURS'];
        }

        arsort($arResult, SORT_NUMERIC);
        $arrFirst = $arResult;
        return $arrFirst;
    }

}
class SecondReport extends GetDataFromHL{
    public function showItems()
    {
        $arResultTemp = array();
        $arResult = array();

        $request = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->getValues();
        $mode = $request['mode'];


        $groupName = $this->getGroupType($mode);

        foreach ($this->queryItems() as $key => $arItem){
            if($groupName !=''){
                if($groupName !== trim($this->getProjectName((int)$arItem['UF_PROJECT'])['UF_METAGROUP'])){
                    continue;
                }
            }

            $arResultTemp[$key]['UF_COUNT_HOURS'] = $arItem['UF_COUNT_HOURS'];
            $arResultTemp[$key]['UF_DATE'] = $arItem['UF_DATE'];
            $arResultTemp[$key]['UF_YEAR'] = $this->getYear($arItem['UF_DATE']);
            $arResultTemp[$key]['UF_TECH_AUTOR'] = $arItem['UF_TECH_AUTOR'];
        }

        $year = '';
        foreach ($arResultTemp as $key => $arItem)
        {
            if($year != $arItem['UF_YEAR'])
            {
                $year = $arItem['UF_YEAR'];
            }

            $monthNumber = ParseDateTime($arItem['UF_DATE'])['MM'];
            $monthStr = $this->getMonth($arItem['UF_DATE']);
            $arResult[$year][$monthStr]['MONTH'] =  $monthStr;
            $arResult[$year][$monthStr]['PLAN'] =  $this->getWorkHours($monthNumber, $year);
            $arResult[$year][$monthStr]['FACT'] += $arItem['UF_COUNT_HOURS'];
            $arResult[$year][$monthStr]['USERS_TEMP'][] = $arItem['UF_TECH_AUTOR'];
        }

        foreach ($arResult as $key1 => $arItem){
            foreach ($arItem as $key2 => $newItem){
                $arResult[$key1][$key2]['USERS'] = count(array_unique($newItem['USERS_TEMP']));
                $arResult[$key1][$key2]['PLAN'] = $arResult[$key1][$key2]['PLAN'] * $arResult[$key1][$key2]['USERS'];
            }
        }

        $arrSecond = $arResult;
        return $arrSecond;
    }
}
class ThirdReport extends GetDataFromHL{

    public function showItems()
    {
        $arResultTemp = array();
        $arResult = array();

        $request = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->getValues();
        $mode = $request['mode'];

        $groupName = $this->getGroupType($mode);

        foreach ($this->queryItems() as $key => $arItem){
            if($groupName !=''){
                if($groupName !== trim($this->getProjectName((int)$arItem['UF_PROJECT'])['UF_METAGROUP'])){
                    continue;
                }
            }

            $arResultTemp[$key]['UF_PROJECT'] = $this->getProjectName((int)$arItem['UF_PROJECT'])['UF_NAME'];
            $arResultTemp[$key]['UF_COUNT_HOURS'] = $arItem['UF_COUNT_HOURS'];
            $arResultTemp[$key]['UF_DATE'] = $this->getMonth($arItem['UF_DATE']);
        }

        $str = '';
        foreach ($arResultTemp as $key => $arItem)
        {
            if($str != $arItem['UF_DATE'])
            {
                $str = $arItem['UF_DATE'];
            }
            $arResult[$str]['month'] =  $str;
            $arResult[$str][$arItem['UF_PROJECT']] +=  (int)$arItem['UF_COUNT_HOURS'];
        }

        $arrThird = $arResult;
        return $arrThird;

    }
}
class FourthReport extends GetDataFromHL{
    public function showItems()
    {
        $arResultTemp = array();
        $arResult = array();

        $request = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->getValues();
        $mode = $request['mode'];
        $projectId = $request['project'];


        $groupName = $this->getGroupType($mode);

        foreach ($this->queryItems() as $key => $arItem){
            if($groupName !=''){
                if($groupName !== trim($this->getProjectName((int)$arItem['UF_PROJECT'])['UF_METAGROUP'])){
                    continue;
                }
            }

            if($projectId !=$arItem['UF_PROJECT']){
                continue;
            }


            $arResultTemp[$key]['UF_COUNT_HOURS'] = $arItem['UF_COUNT_HOURS'];
            $arResultTemp[$key]['USER'] = $this->getUserFio($arItem['UF_TECH_AUTOR']);
        }

        $user = '';
        foreach ($arResultTemp as $key => $arItem)
        {
            if($user != $arItem['USER'])
            {
                $user = $arItem['USER'];
            }
            $arResult[$user] +=  (int)$arItem['UF_COUNT_HOURS'];
        }

        arsort($arResult, SORT_NUMERIC);
        $arrFourth = $arResult;
        return $arrFourth;
    }

    public function showItems2()
    {
        $arResultTemp = array();
        $arResult = array();

        $request = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->getValues();
        $mode = $request['mode'];
        $projectName = $request['projectName'];


        $groupName = $this->getGroupType($mode);

        foreach ($this->queryItems() as $key => $arItem){
          /*  if($groupName !=''){
                if($groupName !== trim($this->getProjectName((int)$arItem['UF_PROJECT'])['UF_METAGROUP'])){
                    continue;
                }
            }*/

          /*  if($projectName != $this->getProjectName($arItem['UF_PROJECT'])){
                continue;
            }*/


            $arResultTemp[$key]['UF_COUNT_HOURS'] = $arItem['UF_COUNT_HOURS'];
            $arResultTemp[$key]['USER'] = $this->getUserFio($arItem['UF_TECH_AUTOR']);
            $arResultTemp[$key]['PROJECT'] = $this->getProjectName($arItem['UF_PROJECT'])['UF_NAME'];
        }

        $user = '';
        foreach ($arResultTemp as $key => $arItem)
        {
            if($user != $arItem['USER'])
            {
                $user = $arItem['USER'];
            }
            $arResult[$arItem['PROJECT']][$user] +=  (int)$arItem['UF_COUNT_HOURS'];

        }

        arsort($arResult, SORT_NUMERIC);
        $arrFifth = $arResult;
        return $arrFifth;
    }
}

$arResult = [];
$arResult['FIRST'] = (new FirstReport())->showItems();
$arResult['SECOND'] = (new SecondReport())->showItems();
$arResult['THIRD'] = (new ThirdReport())->showItems();
$arResult['FOURTH'] = (new FourthReport())->showItems();
$arResult['FIFTH'] = (new FourthReport())->showItems2();

echo json_encode($arResult);
